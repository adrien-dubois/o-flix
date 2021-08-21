<?php

namespace App\Controller;

use App\Repository\CharacterRepository;
use App\Repository\SeasonRepository;
use App\Repository\TvShowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class TvShowController extends AbstractController
{

    /**
     * @Route("/tvshow/", name="show_list")
     */
    public function index(TvShowRepository $tvShowRepository): Response
    {

        // Get all the series from DB
        $tvshows = $tvShowRepository->findAll();

        return $this->render('tv_show/list.html.twig', [
            'tvshows' => $tvshows,
        ]);
    }

    /**
     * Methods which display a serie by his ID
     *
     * @Route("/tvshow/{id}", name="show_single", requirements={"id"="\d+"})
     * 
     * @param [int] $id
     * @param TvShowRepository $tvShowRepository
     * @return Response
     */
    public function singleShow($id, TvShowRepository $tvShowRepository, SeasonRepository $seasonRepository, SessionInterface $session): Response
    {

        
        // Get the right serie with the asked ID
        $show = $tvShowRepository->find($id);
        
        // Get seasons in relation with the good show
        $season = $seasonRepository->findBy(['tvShow' => $id]);

        $perso = $session->get('perso');
        if(empty($perso)){
            $perso['firstname'] = "Pas de personnage séléctionné";
        }
        
        
        // If someone ask an ID doesn't exists, send a 404
        if(!$show){
            throw $this->createNotFoundException('La série demandée n\'existe pas');
        }      
        
        // dump($show);
        return $this->render('tv_show/single.html.twig',[
            'show'=>$show,
            'seasons'=>$season,
            'perso'=>$perso,
        ]);
    }

    /**
     * Method displaying favorites page
     * 
     * @Route("/tvshow/favorite", name="show_fav")
     *
     * @return void
     */
    public function favs()
    {
        return $this->render('tv_show/favs.html.twig');
    }

}
 