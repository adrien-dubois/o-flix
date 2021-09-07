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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        return $this->render('tv_show/list.html.twig', [
            'tvshows' => $tvShowRepository->findBy([],['title'=>'ASC']),
        ]);
    }

    /**
     * Method displaying favorites page
     * 
     * @Route("/favorite", name="fav")
     *
     * @return void
     */
    public function favs(TvShowRepository $tvShowRepository)
    {   
        return $this->render('tv_show/favs.html.twig',[
            'tvshows'=>$tvShowRepository->findAll()
        ]);
    }

    /**
     * @Route("/favorite/add/{id}", name="fav_add", requirements={"id"="\d+"})
     *
     * @param TvShow $tvShow
     * @param [type] $id
     * @return void
     */
    public function addFav(TvShow $tvShow, Request $request)
    {
        if(!$tvShow){
            throw new NotFoundHttpException('Pas de série avec cet identifiant');
        }
        $tvShow->addFavori($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($tvShow);
        $em->flush();

        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);

        return $this->redirect($referer);
    }

    /**
     * @Route("/favorite/remove/{id}", name="fav_remove", requirements={"id"="\d+"})
     *
     * @param TvShow $tvShow
     * @param [type] $id
     * @return void
     */
    public function removeFav(TvShow $tvShow, Request $request)
    {
        if(!$tvShow){
            throw new NotFoundHttpException('Pas de série avec cet identifiant');
        }
        $tvShow->removeFavori($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($tvShow);
        $em->flush();

        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);

        return $this->redirect($referer);
    }

    /**
     * Methods which display a serie by his ID
     *
     * @Route("/{id}", name="single", requirements={"id"="\d+"})
     * @Route("/{slug}", name="single_slug")
     * 
     * @param TvShowRepository $seasonRepository
     * @return Response
     */
    public function singleShow(SeasonRepository $seasonRepository, OmdbApi $omdbApi, TvShow $tvShow): Response
    {

        // TEST OMDB API
        // $tvShowDataArray = $omdbApi->fetch($show->getTitle());
        // dd($tvShowDataArray);
        
        $id = $tvShow->getId();

        // Get seasons in relation with the good show
        $season = $seasonRepository->findBy(['tvShow' => $id]);

        
        
        // If someone ask an ID doesn't exists, send a 404
        if(!$tvShow){
            throw $this->createNotFoundException('La série demandée n\'existe pas');
        }      
        
        // dump($show);
        return $this->render('tv_show/single.html.twig',[
            'show'=>$tvShow,
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




}
 