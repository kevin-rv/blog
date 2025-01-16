<?php

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testCategoryIndex()
    {
        $client = static::createClient();

        $user = new User;
        $user->setUsername("test");
        $user->setPassword("test");

        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $entityManager->persist($user);
        $entityManager->flush();
        $client->loginUser($user);
        $crawler = $client->request('GET', '/category');

        $this->assertResponseIsSuccessful();
    }
}
