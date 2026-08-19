<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\LinkedInPost;
use App\Entity\PressMention;
use App\Entity\Resource;
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
        $this->createResources($manager);
        $this->createPressMentions($manager);
        $this->createLinkedInPosts($manager);
        $this->createUsers($manager);

        $manager->flush();
    }

    /**
     * @return list<Category>
     */
    private function createCategories(ObjectManager $manager): array
    {
        $categories = [];
        foreach (range(1, 4) as $index) {
            $name = $this->faker->unique()->words(2, true);
            $category = new Category();
            $category->setName($name)
                ->setSlug($this->slugger->slug($name)->lower()->toString());
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
                ->setCategory($this->faker->randomElement($categories));
            $manager->persist($article);
        }
    }

    private function createResources(ObjectManager $manager): void
    {
        foreach (range(1, 6) as $index) {
            $resource = new Resource();
            $resource->setTitle($this->faker->sentence(4))
                ->setDescription($this->faker->paragraphs(2, true))
                ->setLink($this->faker->url)
                ->setAttachmentPath(null);
            $manager->persist($resource);
        }
    }

    private function createPressMentions(ObjectManager $manager): void
    {
        foreach (range(1, 5) as $index) {
            $mention = new PressMention();
            $mention->setTitle($this->faker->sentence(6))
                ->setExternalLink($this->faker->url)
                ->setMediaName($this->faker->company);
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
