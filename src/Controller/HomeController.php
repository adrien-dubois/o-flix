<?php

namespace App\Controller;

use App\Repository\TvShowRepository;
use App\Service\CallApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController{


    
    /**
     * Methods displaying homepage with the third last series
     *
     * @Route("/", name="home")
     * 
     * @param TvShowRepository $tvShowRepository
     * @return Response
     */
    public function index(TvShowRepository $tvShowRepository, CallApiService $callApiService):Response
    {
        // Get the three last entries in the DB with findBy()
        $shows = $tvShowRepository->findBy([],
            ['createdAt' => "DESC"],
            3,0
        );

        // dd($callApiService->getData());
        
        return $this->render('home/home.html.twig',[
            'shows'=>$shows,
        ]);

    }
}