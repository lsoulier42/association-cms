<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Association;
use App\Entity\Category;
use App\Entity\LinkedInPost;
use App\Entity\Media;
use App\Entity\PressMention;
use App\Entity\SpecialPage;
use App\Entity\TeamMember;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends AbstractFixtures
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly SluggerInterface $slugger
    ) {
        parent::__construct();
    }

    public function load(ObjectManager $manager): void
    {
        $categories = $this->createCategories($manager);
        $this->createArticles($manager, $categories);
        $this->createSpecialPages($manager, $categories);
        $this->createTeamMembers($manager);
        $this->createPressMentions($manager);
        $this->createLinkedInPosts($manager);
        $this->createAssociationSettings($manager);
        $this->createUsers($manager);

        $manager->flush();
    }

    /**
     * @return list<Category>
     */
    private function createCategories(ObjectManager $manager): array
    {
        $names = ['Actualités', 'L\'association', 'Informations pratiques'];
        $categories = [];
        foreach ($names as $index => $name) {
            $category = new Category();
            $category->setName($name)
                ->setSlug($this->slugger->slug($name)->lower()->toString())
                ->setMenuOrder($index);
            $manager->persist($category);
            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * @param list<Category> $categories
     */
    private function createArticles(ObjectManager $manager, array $categories): void
    {
        foreach (range(1, 12) as $index) {
            $date = DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-1 year', 'now')
            );
            $article = new Article();
            $article->setTitle($this->faker->sentence(6))
                ->setContent($this->formatParagraphs($this->faker->paragraphs(4)))
                ->setPublishedAt($date)
                ->setCategory($this->faker->randomElement($categories))
                ->setShowInMenu($index <= 3)
                ->setMenuOrder($index);
            $manager->persist($article);
        }
    }

    /**
     * @param list<Category> $categories
     */
    private function createSpecialPages(ObjectManager $manager, array $categories): void
    {
        $pages = [
            ['L\'association', 'association', 'content', 'Présentation de l\'association.', true, 1, 1],
            ['Contact', 'contact', 'contact', 'Nous contacter.', true, 2, 1],
            ['Équipe', 'equipe', 'team', 'Les personnes qui font vivre l\'association.', true, 3, 1],
            ['Nos partenaires', 'partenaires', 'partner', 'Ils nous soutiennent.', false, 0, 2],
            ['Rendez-vous', 'rendez-vous', 'appointments', 'Les prochains rendez-vous de l\'association.', false, 0, 2],
            ['Revue de presse', 'revue-de-presse', 'press', 'Ce que les médias disent de nous.', false, 0, 2],
        ];

        foreach ($pages as [$title, $slug, $identifier, $content, $showInMenu, $menuOrder, $categoryIndex]) {
            $page = new SpecialPage();
            $page->setTitle($title)
                ->setSlug($slug)
                ->setIdentifier($identifier)
                ->setContent(sprintf('<p>%s</p>', $content))
                ->setShowInMenu($showInMenu)
                ->setMenuOrder($menuOrder)
                ->setCategory($categories[$categoryIndex]);
            $manager->persist($page);
        }
    }

    private function createTeamMembers(ObjectManager $manager): void
    {
        $roles = ['Président.e', 'Secrétaire général.e', 'Trésorier.ère', 'Membre actif.ve'];
        foreach (range(1, 6) as $index) {
            $member = new TeamMember();
            $member->setFirstName($this->faker->firstName)
                ->setLastName($this->faker->lastName)
                ->setRole($roles[($index - 1) % count($roles)])
                ->setBio($this->faker->optional(0.7)->sentence(10))
                ->setEmail($this->faker->safeEmail)
                ->setSortOrder($index);
            $manager->persist($member);
        }
    }

    private function createPressMentions(ObjectManager $manager): void
    {
        $types = ['Article', 'Tribune', 'Interview'];
        foreach (range(1, 5) as $index) {
            $media = new Media();
            $media->setName($this->faker->company);
            $manager->persist($media);

            $date = DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-6 months', 'now')
            );
            $mention = new PressMention();
            $mention->setTitle($this->faker->sentence(6))
                ->setExternalLink($this->faker->url)
                ->setType($this->faker->randomElement($types))
                ->setPublishedAt($date)
                ->setMedia($media);
            $manager->persist($mention);
        }
    }

    private function createLinkedInPosts(ObjectManager $manager): void
    {
        foreach (range(1, 4) as $index) {
            $post = new LinkedInPost();
            $post->setTitle($this->faker->sentence(5))
                ->setEmbedLink($this->faker->url);
            $manager->persist($post);
        }
    }

    private function createAssociationSettings(ObjectManager $manager): void
    {
        $association = new Association();
        $association->setAddress('1 rue de l\'Exemple' . "\n" . '75000 Paris')
            ->setPhoneNumber('01 23 45 67 89')
            ->setContactEmail('contact@example.org');
        $manager->persist($association);
    }

    private function createUsers(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@example.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $manager->persist($user);
    }

    /**
     * @param list<string> $paragraphs
     */
    private function formatParagraphs(array $paragraphs): string
    {
        $htmlParagraphs = array_map(
            static fn (string $paragraph) => sprintf('<p>%s</p>', $paragraph),
            $paragraphs
        );

        return implode('', $htmlParagraphs);
    }
}
