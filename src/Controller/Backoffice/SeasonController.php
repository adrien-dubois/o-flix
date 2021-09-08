<?php

namespace App\Controller\Backoffice;

use App\Entity\Season;
use App\Entity\TvShow;
use App\Form\SeasonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/season", name="backoffice_season_", requirements={"id"="\d+"})
 */
class SeasonController extends AbstractController
{
    
    /**
     * @Route("/{id}", name="index")
     *
     * @return Response
     */
    public function index(TvShow $tvShow): Response
    {
        return $this->render('backoffice/season/index.html.twig', [
            'TvShow' => $tvShow,
        ]);
    }

    /**
     * Add a new season
     * 
     * @Route("/{id}/add", name="add")
     * @return void
     */
    public function add(TvShow $tvShow, Request $request)
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //TODO : gérer les doublons dans les saisons
            //TODO : Tester l'ordre des numéros de saisons

            $tvShow->addSeason($season);

            $em = $this->getDoctrine()->getManager();
            $em->persist($season);
            $em->flush();
            
            $this->addFlash(
                'success',
                'La saison numéro ' . $season->getSeasonNumber() . ' a bien été associée à la série ' . $tvShow->getTitle()
            );

            return $this->redirectToRoute('backoffice_season_index', [
                'id' => $tvShow->getId()
            ]);
        }

        return $this->render('backoffice/season/add.html.twig',[
            'formView' => $form->createView(),
            'TvShow' => $tvShow
        ]);
    }

    /**
     * delete a season
     *
     * @Route("/{id}/delete", name="delete")
     * 
     * @return void
     */
    public function delete($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $season = $this->getDoctrine()->getRepository(Season::class);
        $season = $season->find($id);

        if(!$season){
            throw $this->createNotFoundException('Cette saison n\'existe pas');
        }

        $em->remove($season);
        $em->flush();

        $this->addFlash(
            'danger',
            'Saison supprimée'
        );

        $referer = filter_var($request->headers->get('referer'), FILTER_SANITIZE_URL);

        return $this->redirect($referer);
    }
}
