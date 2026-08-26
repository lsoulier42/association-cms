<?php

namespace App\Service;

use App\Entity\Media;
use App\Repository\MediaRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use DOMDocument;
use DOMXPath;

class MediaMetadataScraper
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly MediaRepository $mediaRepository
    ) {
    }

    /**
     * @return array{
     *     title: string|null,
     *     publishedAt: DateTimeImmutable|null,
     *     media: Media|null,
     *     mediaLogoUrl: string|null
     * }
     */
    public function scrape(string $url): array
    {
        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                ],
            ]);

            $html = $response->getContent();
        } catch (Exception $e) {
            return [
                'title' => null,
                'publishedAt' => null,
                'media' => null,
                'mediaLogoUrl' => null,
            ];
        }

        $doc = new DOMDocument();
        @$doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new DOMXPath($doc);

        $title = $this->getXpathValue($xpath, [
            '//meta[@property="og:title"]/@content',
            '//meta[@name="twitter:title"]/@content',
            '//title',
        ]);

        $publishedAtStr = $this->getXpathValue($xpath, [
            '//meta[@property="article:published_time"]/@content',
            '//meta[@name="publication_date"]/@content',
            '//meta[@name="date"]/@content',
            '//time/@datetime',
        ]);

        if (!$publishedAtStr) {
            $publishedAtStr = $this->getJsonLdDate($xpath);
        }

        $publishedAt = null;
        if ($publishedAtStr) {
            try {
                $publishedAt = new DateTimeImmutable($publishedAtStr);
            } catch (Exception) {
                $publishedAt = null;
            }
        }

        $mediaName = $this->getXpathValue($xpath, [
            '//meta[@property="og:site_name"]/@content',
            '//meta[@name="twitter:site"]/@content',
        ]);

        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';

        if (!$mediaName && $domain) {
            $mediaName = str_replace('www.', '', $domain);
        }

        $mediaLogoUrl = $this->getXpathValue($xpath, [
            '//link[@rel="apple-touch-icon"]/@href',
            '//link[@rel="icon" and contains(@sizes, "32x32")]/@href',
            '//link[@rel="icon"]/@href',
            '//meta[@property="og:image"]/@content',
        ]);

        if ($mediaLogoUrl && !str_starts_with($mediaLogoUrl, 'http') && $domain) {
            $base = ($parsedUrl['scheme'] ?? 'https') . '://' . $domain;
            if (str_starts_with($mediaLogoUrl, '/')) {
                $mediaLogoUrl = $base . $mediaLogoUrl;
            } else {
                $mediaLogoUrl = $base . '/' . $mediaLogoUrl;
            }
        }

        $media = null;
        if ($mediaName) {
            $media = $this->mediaRepository->findOneBy(['name' => $mediaName]);
            if (!$media) {
                $media = new Media();
                $media->setName($mediaName);
                $media->setWebsiteUrl(($parsedUrl['scheme'] ?? 'https') . '://' . $domain);
                // We'll handle logo download/storage later or just store the URL for now
                $media->setLogo($mediaLogoUrl);
                $this->entityManager->persist($media);
                $this->entityManager->flush();
            }
        }

        return [
            'title' => $title,
            'publishedAt' => $publishedAt,
            'media' => $media,
            'mediaLogoUrl' => $mediaLogoUrl,
        ];
    }

    /**
     * @param list<string> $queries
     */
    private function getXpathValue(DOMXPath $xpath, array $queries): ?string
    {
        foreach ($queries as $query) {
            $node = $xpath->query($query)->item(0);
            if ($node) {
                $value = trim($node->nodeValue ?: $node->textContent);
                if ($value) {
                    return $value;
                }
            }
        }

        return null;
    }

    private function getJsonLdDate(DOMXPath $xpath): ?string
    {
        $nodes = $xpath->query('//script[@type="application/ld+json"]');
        foreach ($nodes as $node) {
            $content = trim($node->nodeValue ?: $node->textContent);
            if (!$content) {
                continue;
            }

            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                continue;
            }

            // Handle both single objects and arrays of objects (like Yoast SEO graphs)
            $objects = isset($data['@graph']) ? $data['@graph'] : (isset($data[0]) ? $data : [$data]);

            foreach ($objects as $obj) {
                if (is_array($obj)) {
                    if (isset($obj['datePublished'])) {
                        return $obj['datePublished'];
                    } elseif (isset($obj['dateModified'])) {
                        return $obj['dateModified'];
                    }
                }
            }
        }

        return null;
    }
}
