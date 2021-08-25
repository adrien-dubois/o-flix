<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/backoffice/category", name="backoffice_category")
     */
    public function index(): Response
    {
        return $this->render('backoffice/category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
}
