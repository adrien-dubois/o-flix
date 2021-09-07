<?php

namespace App\Controller\Backoffice;

use App\Repository\TvShowRepository;
use App\Repository\UserRepository;
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
    public function home(TvShowRepository $tvShowRepository, UserRepository $userRepository)
    {
        $shows = $tvShowRepository->findBy([],
            ["createdAt"=>"DESC"],
            3,0
        );

        // $var = $this->getParameter('maintenance_mode');
        
        

        $user = $userRepository->findOneBy([], ["createdAt"=>"DESC"]);

        $like = $tvShowRepository->findOneBy([],['nbLikes'=>"DESC"]);

        $recent = $tvShowRepository->findOneBy([],['publishedAt'=>"DESC"]);

        return $this->render('backoffice/home.html.twig',[
            'shows'=>$shows,
            'user'=>$user,
            'like'=>$like,
            'recent'=>$recent
        ]);
    }

}