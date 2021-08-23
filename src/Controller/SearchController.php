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

        // Get the search request with the get method
        $query = $request->query->get('q');

        // Send it to the method that is in the repository
        $results = $tvShowRepository->searchTvShowByTitle($query);
        // dd($results);

        return $this->render('search/search.html.twig', [
            'search'=>$results,
            'query'=>$query,
        ]);
    }
}
