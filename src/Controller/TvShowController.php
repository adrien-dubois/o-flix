<?php

namespace App\Controller;

use App\Repository\TvShowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    public function singleShow($id, TvShowRepository $tvShowRepository): Response
    {
        // Get the right serie with the asked ID
        $show = $tvShowRepository->find($id);

        // If someone ask an ID doesn't exists, send a 404
        if(!$show){
            throw $this->createNotFoundException('La série demandée n\'existe pas');
        }

        return $this->render('tv_show/single.html.twig',[
            'show'=>$show,
        ]);
    }
}
