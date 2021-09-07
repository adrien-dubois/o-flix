<?php

namespace App\Controller\Api\V1;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/characters", name="api_v1_character_", requirements={"id"="\d+"})
 */
class CharacterController extends AbstractController
{
    
    /**
     * Display all characters list
     * 
     * @Route("/", name="index", methods={"GET"})
     *
     * @return Response
     */
    public function index(CharacterRepository $characterRepository): JsonResponse
    {   
        $characters = $characterRepository->findAll();

        return $this->json($characters,200,[],[
            'groups' => 'character_list'
        ]);
    }

    /**
     * Return character information from an ID
     * 
     * @Route("/{id}", name="show", methods={"GET"})
     *
     * @param integer $id
     * @return void
     */
    public function show(int $id, CharacterRepository $characterRepository)
    {
        $characters = $characterRepository->find($id);

        if(!$characters){
            return $this->json([
                'error' => 'Le personnage ' .$id . 'n\'existe pas'
            ], 404
            );
        }

        return $this->json($characters, 200, [],[
            'groups'=> 'character_detail'
        ]);
    }

    /**
     * Can create a new character
     *
     * @Route("/", name="add", methods={"POST"})
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return void
     */
    public function add(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $jsonData = $request->getContent();

        $characters = $serializer->deserialize($jsonData, Character::class, 'json');

        $errors = $validator->validate($characters);
        if(count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($characters);
        $em->flush();

        return $this->json($characters, 201);
    }

    /**
     * Method tu update a character
     * 
     * @Route("/{id}", name="edit", methods={"PUT", "PATCH"})
     *
     * @param integer $id
     * @param CharacterRepository $characterRepository
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return void
     */
    public function edit(int $id, CharacterRepository $characterRepository, Request $request, SerializerInterface $serializer)
    {
        $jsonData = $request->getContent();

        $characters = $characterRepository->find($id);
        if(!$characters){
            return $this->json([
                'errors' => ['message'=>'Le personnage ' .$id . 'n\'existe pas']
            ], 404
            );
        }

        $serializer->deserialize($jsonData, Character::class,'json', [AbstractNormalizer::OBJECT_TO_POPULATE=>$characters]);

        $this->getDoctrine()->getManager()->flush();

        return $this->json(["message"=>"Le personnage a bien été modifié"], 200 ,[],[
            'groups' => 'character_detail'
        ]);
    }

        /**
     * Delete a character by its ID
     * 
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param integer $id
     * @param CharacterRepository $characterRepository
     * @return void
     */
    public function delete(int $id, CharacterRepository $characterRepository)
    {
        $characters = $characterRepository->find($id);
        if(!$characters){
            return $this->json([
                'error'=>'Ce personnage n\'existe pas'
            ], 404
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($characters);
        $em->flush();

        return $this->json([
            'ok' => 'Le personnage a bien été effacée'
        ],200
        );
    }
}
