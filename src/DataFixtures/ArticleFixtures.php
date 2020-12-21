<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;


class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        for($i = 1; $i < 100; $i++){

            $category = new Category();

            $faker = \Faker\Factory::create('fr_FR');

            $category->setTitle($faker->sentence(mt_rand(4, 6)))
                     ->setDescription($faker->text())
                     ;
            $manager->persist($category);
            
            for($j = 1; $j < mt_rand(50, 120); $j++){

                $article = new Article();

                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                $article->setTitle($faker->sentence(mt_rand(4,6)))
                        ->setContent($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category)
                        ;

                $manager->persist($article);

                for($k = 1; $k < mt_rand(80, 110); $k++){

                    $comment = new Comment();

                    $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $comment->setAuthor($faker->name())
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                            ->setArticle($article)
                            ;
                    $manager->persist($comment);        
                }        
            }
                     
        }

        $manager->flush();

    }
}
