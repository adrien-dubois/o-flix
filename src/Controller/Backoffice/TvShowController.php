<?php

namespace App\Controller\Backoffice;

use App\Entity\TvShow;
use App\Form\TvShowType;
use App\Repository\TvShowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/backoffice/tvshow", requirements={"id":"\d+"})
 * @IsGranted("ROLE_ADMIN")
 */
class TvShowController extends AbstractController
{
    /**
     * @Route("/", name="backoffice_tv_show_index", methods={"GET"})
     */
    public function index(TvShowRepository $tvShowRepository): Response
    {
        return $this->render('backoffice/tv_show/index.html.twig', [
            'tv_shows' => $tvShowRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="backoffice_tv_show_new", methods={"GET","POST"}, priority=2)
     */
    public function new(Request $request): Response
    {
        $tvShow = new TvShow();
        $form = $this->createForm(TvShowType::class, $tvShow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tvShow);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La série TV a bien été crée'
            );

            return $this->redirectToRoute('backoffice_tv_show_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/tv_show/new.html.twig', [
            'tv_show' => $tvShow,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backoffice_tv_show_show", methods={"GET"})
     * @Route("/{slug}", name="backoffice_tv_show_show_slug")
     */
    public function show(TvShow $tvShow): Response
    {
        return $this->render('backoffice/tv_show/show.html.twig', [
            'tv_show' => $tvShow,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="backoffice_tv_show_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TvShow $tvShow, SluggerInterface $sluggerInterface): Response
    {
        $form = $this->createForm(TvShowType::class, $tvShow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $title = $tvShow->getTitle();
            $slug = $sluggerInterface->slug(strtolower($title));
            $tvShow->setSlug($slug);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backoffice_tv_show_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/tv_show/edit.html.twig', [
            'tv_show' => $tvShow,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="backoffice_tv_show_delete", methods={"POST"})
     */
    public function delete(Request $request, TvShow $tvShow): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tvShow->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tvShow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('backoffice_tv_show_index', [], Response::HTTP_SEE_OTHER);
    }
}
