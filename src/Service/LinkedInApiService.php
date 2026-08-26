<?php

namespace App\Service;

use App\Entity\LinkedInPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Psr\Log\LoggerInterface;

class LinkedInApiService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
        #[Autowire(env: 'default::LINKEDIN_ACCESS_TOKEN')]
        private readonly ?string $accessToken = null,
        #[Autowire(env: 'default::LINKEDIN_ORGANIZATION_ID')]
        private readonly ?string $organizationId = null
    ) {
    }

    /**
     * @return array{success: bool, message: string, count: int}
     */
    public function importPosts(): array
    {
        if (empty($this->accessToken) || empty($this->organizationId)) {
            return [
                'success' => false,
                'message' => 'Les identifiants API LinkedIn (Token ou ID Organisation) '
                    . 'ne sont pas configurés dans les variables d\'environnement.',
                'count' => 0
            ];
        }

        try {
            // LinkedIn Community Management API endpoint for organization posts
            // We use the shares endpoint as an example of fetching recent activities.
            $response = $this->httpClient->request('GET', 'https://api.linkedin.com/v2/shares', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'X-Restli-Protocol-Version' => '2.0.0',
                ],
                'query' => [
                    'q' => 'owners',
                    'owners' => 'urn:li:organization:' . $this->organizationId,
                    'sortBy' => 'LAST_MODIFIED',
                    'sharesPerOwner' => 10,
                ],
            ]);

            $data = $response->toArray();

            if (!isset($data['elements'])) {
                return [
                    'success' => false,
                    'message' => 'Format de réponse inattendu depuis l\'API LinkedIn.',
                    'count' => 0
                ];
            }

            $repository = $this->entityManager->getRepository(LinkedInPost::class);
            $importedCount = 0;

            foreach ($data['elements'] as $element) {
                // In a real scenario, we extract the URN to construct the embed link
                $urn = $element['id'] ?? null;
                $text = $element['text']['text'] ?? 'Post LinkedIn';

                if (!$urn) {
                    continue;
                }

                // Check if post already exists using the embedLink as a unique proxy
                $embedLink = "https://www.linkedin.com/embed/feed/update/{$urn}";
                $existing = $repository->findOneBy(['embedLink' => $embedLink]);

                if (!$existing) {
                    $post = new LinkedInPost();
                    $post->setTitle(mb_substr($text, 0, 250)); // Truncate title
                    $post->setEmbedLink($embedLink);

                    $this->entityManager->persist($post);
                    $importedCount++;
                }
            }

            $this->entityManager->flush();

            return [
                'success' => true,
                'message' => 'Importation réussie.',
                'count' => $importedCount
            ];
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'import LinkedIn : ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la communication avec l\'API LinkedIn : '
                    . $e->getMessage(),
                'count' => 0
            ];
        }
    }
}
