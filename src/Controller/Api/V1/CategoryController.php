<?php

namespace App\Controller\Api\V1;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/v1/categories", name="api_v1_category_", requirements={"id"="\d+"})
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     *
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     */
    public function index(CategoryRepository $categoryRepository): JsonResponse
    {   
        $categories = $categoryRepository->findAll();

        return $this->json($categories,200,[],[
            'groups' => 'tvshow_list'
        ]);
    }

    /**
     * Return Category information from an ID
     * 
     * @Route("/{id}", name="show", methods={"GET"})
     *
     * @param integer $id
     * @param CategoryRepository $categoryRepository
     * @return void
     */
    public function show(int $id, CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->find($id);

        if(!$categories){
            return $this->json([
                'error' => 'La catégorie ' .$id . 'n\'existe pas'
            ], 404
            );
        }

        return $this->json($categories, 200, [],[
            'groups'=> 'tvshow_detail'
        ]);
    }

    /**
     * Can create a new categorie
     *
     * @Route("/", name="add", methods={"POST"})
     * 
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return void
     */
    public function add(Request $request, SerializerInterface $serializer)
    {
        $jsonData = $request->getContent();

        $categories = $serializer->deserialize($jsonData, Category::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($categories);
        $em->flush();

        return $this->json($categories, 201);

    }

    /**
     * Method tu update a category
     * 
     * @Route("/{id}", name="edit", methods={"PUT", "PATCH"})
     *
     * @param integer $id
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return void
     */
    public function edit(int $id, CategoryRepository $categoryRepository, Request $request, SerializerInterface $serializer)
    {
        $jsonData = $request->getContent();

        $categories = $categoryRepository->find($id);
        if(!$categories){
            return $this->json([
                'error' => 'La catégorie ' .$id . 'n\'existe pas'
            ], 404
            );
        }

        $categoryDatas = $serializer->deserialize($jsonData, Category::class,'json', [AbstractNormalizer::OBJECT_TO_POPULATE=>$categories]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($categoryDatas);
        $em->flush();

        return $this->json($categoryDatas, 200 ,[],[
            'groups' => 'tvshow_detail'
        ]);
    }

    
    /**
     * Delete a category by its ID
     * 
     * @Route("/{id}", name="delete", methods={"DELETE"})
     *
     * @param integer $id
     * @param CategoryRepository $categoryRepository
     * @return void
     */
    public function delete(int $id, CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->find($id);
        if(!$categories){
            return $this->json([
                'error'=>'Cette série TV n\'existe pas'
            ], 404
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($categories);
        $em->flush();

        return $this->json([
            'ok' => 'La série a bien été effacée'
        ],200
        );
    }
}
