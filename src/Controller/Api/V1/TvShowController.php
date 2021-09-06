<?php

namespace App\Controller\Api\V1;

use App\Entity\TvShow;
use App\Repository\TvShowRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/v1/tvshows", name="api_v1_tvshow_", requirements={"id"="\d+"})
 */
class TvShowController extends AbstractController
{
    
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index(TvShowRepository $tvShowRepository): Response
    {
        // We get the Shows in DB and return it in Json
        $TvShows = $tvShowRepository->findAll();

        return $this->json($TvShows, 200, [], [
            // Cette entrée au Serializer de transformer les objets en JSON, en allant chercher uniquement les propriétés taggées avec le nom tvshow_list
            'groups' => 'tvshow_list'
        ]);
    }

    /**
     * Return TvShow infomrations with its ID
     *
     * @Route("/{id}", name="show", methods={"GET"})
     * 
     * @return JsonResponse
     */
    public function show(int $id, TvShowRepository $tvShowRepository)
    {
        // We get a show by its ID
        $tvShow = $tvShowRepository->find($id);

        // If the show does not exists, we display a 404
        if (!$tvShow){
            return $this->json([
                'error' => 'La série TV ' .$id . 'n\'existe pas'
            ], 404
            );
        }

        // We return the result with the Json format
        return $this->json($tvShow, 200, [], [
            'groups' => 'tvshow_detail'
        ]);
    }

    /**
     * Can create a new Show
     * 
     * @Route("/", name="add", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializer)
    {
        // we take back the JSON
        $jsonData = $request->getContent();

        // We transform the json in object
        // First argument : datas to deserialaize
        // Second : The type of object we want 
        // Last : Start type
        $tvShow = $serializer->deserialize($jsonData, TvShow::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($tvShow);
        $em->flush();

        return $this->json($tvShow, 201);
    }

    /**
     * Can edit an existing show by its ID
     * 
     * @Route("/{id}", name="edit", methods={"PUT", "PATCH"})
     *
     * @return JsonResponse
     */
    public function edit(int $id, TvShowRepository $tvShowRepository, Request $request, SerializerInterface $serializer)
    {
        $jsonData = $request->getContent();
        
        $tvShow = $tvShowRepository->find($id);
        if(!$tvShow){
            return $this->json([
                'error' => 'La série TV n\'existe pas'
            ], 404
            );
        }

        $tvShowDatas = $serializer->deserialize($jsonData, TvShow::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE=>$tvShow]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($tvShowDatas);
        $em->flush();

        return $this->json($tvShowDatas, 200, [], [
            'groups' => 'tvshow_detail'
        ]);
    }

    /**
     * To delete a show by its ID
     * 
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param integer $id
     * @param TvShowRepository $tvShowRepository
     * @param Request $request
     * @return void
     */
    public function delete(int $id, TvShowRepository $tvShowRepository, Request $request)
    {
        $tvShow = $tvShowRepository->find($id);
        if(!$tvShow){
            return $this->json([
                'error' => 'Cette série TV n\'existe pas'
            ],404
        );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($tvShow);
        $em->flush();

        return $this->json([
            'ok' => 'La série a bien été effacée'
        ],200
    );
    }
}

