<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@oclock.io');

        $client->loginUser($testUser);

        $client->request('GET', 'backoffice/category/add');

        $client->submitForm('Envoyer',[
            'category[name]' => 'Drame'
        ]);

        $this->assertResponseRedirects('/backoffice/category/');
    }
}
