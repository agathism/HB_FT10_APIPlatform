<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private const NB_USERS = 5;
    private const CATEGORIES = [
        'Frontend',
        'Backend',
        'Databases',
        'APIs',
        'Security',
        'Performance',
        'Testing',
        'DevOps',
        'Accessibility',
        'SEO',
        'Content Management',
        'E-commerce',
        'Mobile Web',
        'User Experience',
        'Cloud Computing'];
    private const NB_ARTICLES = 150;
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // USERS
        $users = [];

        for ($i = 0; $i < self::NB_USERS; $i++) {
            $user = new User();
            $user
                ->setEmail("regular$i@mycorp.com")
                ->setPassword($this->hasher->hashPassword($user, 'regular'));

            $manager->persist($user);
            $users[] = $user;
        }

        $adminUser = new User();

        $adminUser
            ->setEmail("admin@mycorp.com")
            ->setPassword($this->hasher->hashPassword($adminUser, "admin"));

        $manager->persist($adminUser);
        $users[] = $adminUser;

        // CATEGORIES
        $categories = [];

        foreach (self::CATEGORIES as $categoryName) {
            $category = new Category();
            $category->setName($categoryName);

            $manager->persist($category);
            $categories[] = $category;
        }

        // ARTICLES
        for ($i = 0; $i < self::NB_ARTICLES; $i++) {
            $article = new Article();

            $article->setTitle($faker->words(
                $faker->numberBetween(4, 8),
                true
            ))
            ->setContent($faker->realTextBetween(250, 1500))
            ->setCreatedAt(DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-3 years')
            ))
            ->setVisible($faker->boolean(85))
            ->setCategory($faker->randomElement($categories))
            ->setAuthor($faker->randomElement($users));

            $manager->persist($article);
        }

        $manager->flush();
    }
}
