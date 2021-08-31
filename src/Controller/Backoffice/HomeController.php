<?php

namespace App\Controller\Backoffice;

use App\Repository\TvShowRepository;
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
    public function home(TvShowRepository $tvShowRepository)
    {
        $shows = $tvShowRepository->findBy([],
            ["createdAt"=>"DESC"],
            3,0
    );

        return $this->render('backoffice/home.html.twig',[
            'shows'=>$shows
        ]);
    }

}