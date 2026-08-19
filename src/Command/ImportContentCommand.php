<?php

namespace App\Command;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\LinkedInPost;
use App\Entity\PressMention;
use App\Entity\Resource;
use App\Entity\User;
use DateTimeImmutable;
use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(
    name: 'app:import:content',
    description: 'Importe des contenus depuis un fichier CSV ou JSON.'
)]
class ImportContentCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface $slugger,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::REQUIRED, 'Type: articles|categories|resources|press_mentions|linkedin_posts|users')
            ->addArgument('file', InputArgument::REQUIRED, 'Chemin vers un fichier CSV/JSON')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Analyse sans écrire en base');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = strtolower((string)$input->getArgument('type'));
        $filePath = (string)$input->getArgument('file');
        $dryRun = (bool)$input->getOption('dry-run');

        if (!is_file($filePath)) {
            $io->error(sprintf('Fichier introuvable: %s', $filePath));
            return Command::FAILURE;
        }

        $records = $this->loadRecords($filePath, $io);
        if ($records === null) {
            return Command::FAILURE;
        }

        $imported = 0;
        foreach ($records as $record) {
            $normalized = $this->normalizeRecord($record);
            $handled = match ($type) {
                'articles' => $this->importArticle($normalized, $io),
                'categories' => $this->importCategory($normalized, $io),
                'resources' => $this->importResource($normalized, $io),
                'press_mentions' => $this->importPressMention($normalized, $io),
                'linkedin_posts' => $this->importLinkedInPost($normalized, $io),
                'users' => $this->importUser($normalized, $io),
                default => null,
            };

            if ($handled === null) {
                $io->error(sprintf('Type invalide: %s', $type));
                return Command::FAILURE;
            }
            if ($handled) {
                $imported++;
            }
        }

        if (!$dryRun) {
            $this->entityManager->flush();
        }

        $io->success(sprintf('%d enregistrements importés (%s).', $imported, $dryRun ? 'dry-run' : 'persisté'));
        return Command::SUCCESS;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    private function loadRecords(string $filePath, SymfonyStyle $io): ?array
    {
        $extension = strtolower((string)pathinfo($filePath, PATHINFO_EXTENSION));
        if ($extension === 'json') {
            $payload = json_decode((string)file_get_contents($filePath), true);
            if (!is_array($payload)) {
                $io->error('JSON invalide.');
                return null;
            }
            if (isset($payload['data']) && is_array($payload['data'])) {
                return $payload['data'];
            }
            return $payload;
        }

        if ($extension !== 'csv') {
            $io->error('Format non supporté. Utilisez CSV ou JSON.');
            return null;
        }

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            $io->error('Impossible d’ouvrir le fichier CSV.');
            return null;
        }

        $headerLine = fgets($handle);
        if ($headerLine === false) {
            fclose($handle);
            return [];
        }
        $delimiter = $this->detectDelimiter($headerLine);
        $header = str_getcsv($headerLine, $delimiter);
        if (isset($header[0])) {
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$header[0]);
        }
        $header = array_map([$this, 'normalizeKey'], $header);

        $records = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) === 1 && $row[0] === null) {
                continue;
            }
            $records[] = array_combine($header, $row);
        }
        fclose($handle);
        return $records;
    }

    private function detectDelimiter(string $line): string
    {
        $commaCount = substr_count($line, ',');
        $semicolonCount = substr_count($line, ';');
        return $semicolonCount > $commaCount ? ';' : ',';
    }

    /**
     * @param array<string, mixed> $record
     * @return array<string, mixed>
     */
    private function normalizeRecord(array $record): array
    {
        $normalized = [];
        foreach ($record as $key => $value) {
            $normalized[$this->normalizeKey((string)$key)] = $value;
        }
        return $normalized;
    }

    private function normalizeKey(string $key): string
    {
        $lower = function_exists('mb_strtolower') ? mb_strtolower($key) : strtolower($key);
        $clean = preg_replace('/[^\p{L}\p{N}]+/u', '_', $lower);
        $clean = trim((string)$clean, '_');
        return $clean;
    }

    /**
     * @param array<string, mixed> $record
     * @param list<string> $keys
     */
    private function getValue(array $record, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $record)) {
                $value = $record[$key];
                if ($value !== null && $value !== '') {
                    return is_string($value) ? trim($value) : (string)$value;
                }
            }
        }
        return null;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function importCategory(array $record, SymfonyStyle $io): bool
    {
        $name = $this->getValue($record, ['name', 'nom']);
        if ($name === null) {
            $io->warning('Catégorie ignorée (nom manquant).');
            return false;
        }

        $slug = $this->getValue($record, ['slug']);
        if ($slug === null) {
            $slug = $this->slugger->slug($name)->lower()->toString();
        }

        $repository = $this->entityManager->getRepository(Category::class);
        $category = $repository->findOneBy(['slug' => $slug]) ?? new Category();
        $category->setName($name)->setSlug($slug);
        $this->entityManager->persist($category);
        return true;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function importArticle(array $record, SymfonyStyle $io): bool
    {
        $title = $this->getValue($record, ['title', 'titre']);
        $content = $this->getValue($record, ['content', 'contenu']);
        if ($title === null || $content === null) {
            $io->warning('Article ignoré (titre ou contenu manquant).');
            return false;
        }

        $categoryValue = $this->getValue($record, ['category_slug', 'category', 'categorie', 'nom_categorie']);
        $category = null;
        if ($categoryValue !== null) {
            $category = $this->entityManager->getRepository(Category::class)->findOneBy(['slug' => $categoryValue]);
            if ($category === null) {
                $category = new Category();
                $category->setName($categoryValue)
                    ->setSlug($this->slugger->slug($categoryValue)->lower()->toString());
                $this->entityManager->persist($category);
            }
        }

        if ($category === null) {
            $io->warning(sprintf('Article "%s" ignoré (catégorie manquante).', $title));
            return false;
        }

        $publishedAt = $this->getValue($record, ['published_at', 'publishedat', 'date']);
        $date = $this->parseDate($publishedAt, $io);

        $article = new Article();
        $article->setTitle($title)
            ->setContent($content)
            ->setPublishedAt($date)
            ->setCategory($category);
        $this->entityManager->persist($article);
        return true;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function importResource(array $record, SymfonyStyle $io): bool
    {
        $title = $this->getValue($record, ['title', 'titre']);
        $description = $this->getValue($record, ['description']);
        if ($title === null || $description === null) {
            $io->warning('Ressource ignorée (titre ou description manquant).');
            return false;
        }

        $resource = new Resource();
        $resource->setTitle($title)
            ->setDescription($description)
            ->setLink($this->getValue($record, ['link', 'lien']))
            ->setAttachmentPath($this->getValue($record, ['attachment_path', 'fichier', 'piece_jointe']));
        $this->entityManager->persist($resource);
        return true;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function importPressMention(array $record, SymfonyStyle $io): bool
    {
        $title = $this->getValue($record, ['title', 'titre']);
        $externalLink = $this->getValue($record, ['external_link', 'lien']);
        $mediaName = $this->getValue($record, ['media_name', 'media', 'nom_du_media']);

        if ($title === null || $externalLink === null || $mediaName === null) {
            $io->warning('Mention presse ignorée (données manquantes).');
            return false;
        }

        $pressMention = new PressMention();
        $pressMention->setTitle($title)
            ->setExternalLink($externalLink)
            ->setMediaName($mediaName);
        $this->entityManager->persist($pressMention);
        return true;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function importLinkedInPost(array $record, SymfonyStyle $io): bool
    {
        $title = $this->getValue($record, ['title', 'titre']);
        $embedLink = $this->getValue($record, ['embed_link', 'lien_integration', 'lien']);

        if ($title === null || $embedLink === null) {
            $io->warning('Post LinkedIn ignoré (données manquantes).');
            return false;
        }

        $post = new LinkedInPost();
        $post->setTitle($title)
            ->setEmbedLink($embedLink);
        $this->entityManager->persist($post);
        return true;
    }

    /**
     * @param array<string, mixed> $record
     */
    private function importUser(array $record, SymfonyStyle $io): bool
    {
        $email = $this->getValue($record, ['email', 'mail']);
        $plainPassword = $this->getValue($record, ['plain_password', 'password', 'mot_de_passe']);

        if ($email === null || $plainPassword === null) {
            $io->warning('Utilisateur ignoré (email ou mot de passe manquant).');
            return false;
        }

        $rolesValue = $this->getValue($record, ['roles', 'role']);
        $roles = $rolesValue ? preg_split('/[|,;]+/', $rolesValue) : ['ROLE_USER'];
        $roles = array_values(array_filter(array_map('trim', (array)$roles)));

        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->findOneBy(['email' => $email]) ?? new User();
        $user->setEmail($email)->setRoles($roles);
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $this->entityManager->persist($user);
        return true;
    }

    private function parseDate(?string $value, SymfonyStyle $io): DateTimeImmutable
    {
        if ($value === null) {
            return new DateTimeImmutable();
        }
        try {
            return new DateTimeImmutable($value);
        } catch (Exception $exception) {
            $io->warning(sprintf('Date invalide "%s", valeur par défaut appliquée.', $value));
            return new DateTimeImmutable();
        }
    }
}
