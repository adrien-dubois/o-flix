<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class HomeController extends AbstractController
{

    /**
     * @Route("/backoffice/home", name="backoffice_home")
     * @IsGranted("ROLE_ADMIN")
     *
     * @return void
     */
    public function home()
    {
        return $this->render('backoffice/home.html.twig');
    }

}