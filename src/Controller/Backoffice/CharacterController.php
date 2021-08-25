<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CharacterController extends AbstractController
{
    /**
     * @Route("/backoffice/character", name="backoffice_character")
     */
    public function index(): Response
    {
        return $this->render('backoffice/character/index.html.twig', [
            'controller_name' => 'CharacterController',
        ]);
    }
}
