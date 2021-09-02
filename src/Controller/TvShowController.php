<?php

namespace App\Controller;

use App\Entity\TvShow;
use App\Repository\SeasonRepository;
use App\Repository\TvShowRepository;
use App\Service\OmdbApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/tvshow", name="show_")
 * @IsGranted("ROLE_USER")
 */
class TvShowController extends AbstractController
{

    /**
     * @Route("/", name="list")
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
     * @Route("/{id}", name="single", requirements={"id"="\d+"})
     * 
     * @param [int] $id
     * @param TvShowRepository $tvShowRepository
     * @return Response
     */
    public function singleShow($id, TvShowRepository $tvShowRepository, SeasonRepository $seasonRepository, OmdbApi $omdbApi): Response
    {
    
        // Get the right serie with the asked ID
        $show = $tvShowRepository->find($id);

        // TEST OMDB API
        // $tvShowDataArray = $omdbApi->fetch($show->getTitle());
        // dd($tvShowDataArray);
        
        // Get seasons in relation with the good show
        $season = $seasonRepository->findBy(['tvShow' => $id]);

        dump($show);
        
        // If someone ask an ID doesn't exists, send a 404
        if(!$show){
            throw $this->createNotFoundException('La série demandée n\'existe pas');
        }      
        
        // dump($show);
        return $this->render('tv_show/single.html.twig',[
            'show'=>$show,
            'seasons'=>$season,
        ]);
    }


    /**
     * Method adding likes to the choosen tv show
     *
     * @Route("/like/{id}", name="like", requirements={"id"="\d+"})
     * 
     * @param [int] $id
     * @return Redirect
     */
    public function addLike($id, Request $request)
    {
        // Get the tv show that we click on the thumbs up with
        $like = $this->getDoctrine()->getRepository(TvShow::class);
        $like = $like->find($id);

        // Testing if the serie exists
        if(!$like){
            throw $this->createNotFoundException('La série choisie n\'existe pas.' );
        }

        // Then increment plus one on the right Tv Show
        $likeAdd = $like->setNbLikes($like->getNbLikes() +1) ;
        
        // Call the manager and update it before redirect to the same page automaticly
        $em = $this->getDoctrine()->getManager();
        $em->persist($likeAdd);
        $em->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }


    /**
     * Method displaying favorites page
     * 
     * @Route("/favorite", name="fav")
     *
     * @return void
     */
    public function favs()
    {
        return $this->render('tv_show/favs.html.twig');
    }

}
 