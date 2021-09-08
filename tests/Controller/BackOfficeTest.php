<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BackOfficeTest extends WebTestCase
{
    /**
     * Testing TvShowList in Dahsboard for a user not connected
     *
     */
    public function testBackOfficePublic(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/backoffice/tvshow/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /**
     * Testing the BackOffice with a User access
     *
     */
    public function testBackOfficeUser()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('user@oclock.io');

        $client->loginUser($testUser);
        $client->request('GET', '/backoffice/tvshow/');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     * Testing BackOffice with an Admin access
     *
     */
    public function testBackOfficeAdmin()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('admin@oclock.io');

        $client->loginUser($testUser);
        $client->request('GET', '/backoffice/tvshow/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
