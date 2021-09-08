<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    /**
     * Homepage test offline
     *
     * @return void
     */
    public function testHomePagePublic(): void
    {
        // On va se mettre dans la peau d'un navigateur
        // Et tenter d'accéder à la page d'accueil ("/")
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // On vérifie si la page existe bien
        // Si c'est OK, alors la page est potentiellement fonctionnelle
        $this->assertResponseIsSuccessful();

        // On vérifie s'il existe une balise h1 avec le contenu : "Séries TV et bien plus en illimité."
        $this->assertSelectorTextContains('h1', 'Séries TV et bien plus en illimité');
    }

    /**
     * Homepage test online
     *
     * @return void
     */
    public function testHomePageConnected()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class); 

        $testUser = $userRepository->findOneByEmail('adrien@oclock.io');
       

        $client->loginUser($testUser);

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('p.lead.fst-italic', $testUser->getFirstname() . ', bienvenue sur votre compte');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
