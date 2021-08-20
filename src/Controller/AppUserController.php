<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AppUserController extends AbstractController{

    /**
     * Method displaying connexion
     *
     * @Route("/login", name="appuser_login")
     * 
     * @return 
     */
    public function login()
    {
        return $this->render('appuser/login.html.twig');
    }
}