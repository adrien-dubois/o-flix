<?php

namespace App\Controller;

use App\Repository\TvShowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search", name="search_")
 */
class SearchController extends AbstractController
{
    /**
     * Method to display research results
     * 
     * @Route("/", name="index")
     */
    public function index(Request $request, TvShowRepository $tvShowRepository): Response
    {

        $query = $request->query->get('q');

        $results = $tvShowRepository->searchTvShowByTitle($query);
        // dd($results);

        return $this->render('search/search.html.twig', [
            'search'=>$results,
            'query'=>$query,
        ]);
    }
}
