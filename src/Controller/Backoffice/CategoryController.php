<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/category", name="backoffice_category_")
 */
class CategoryController extends AbstractController
{

    /**
     * Method displaying index of category backoffice page
     *
     * @Route("/", name="home")
     * 
     * @return Response
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('backoffice/category/category.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * Method to add a new category
     * 
     *@Route("/add", name="add")
     * @param Request $request
     * @return void
     */
    public function add(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash(
                'success',
                'La catégorie a bien été crée'
            );

            return $this->redirectToRoute('backoffice_category_home');
        }

        return $this->render('backoffice/category/add.html.twig',[
            'formView'=>$form->createView(),
        ]);

    }

    /**
     * Edit an existing category
     *
     * @Route("/update/{id}", name="update", requirements={"id"="\d+"})
     * 
     * @param INT $id
     * @return void
     */
    public function update($id, Request $request, Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'Catégorie changée avec succès'
            );

            return $this->redirectToRoute('backoffice_category_home');
        }

        return $this->render('backoffice/category/update.html.twig',[
            'category' => $category,
            'formView'=>$form->createView(),
        ]);
    }

    /**
     * Delete a selected category
     * 
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"} )
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()->getRepository(Category::class);
        $category = $category->find($id);

        if(!$category){
            throw $this->createNotFoundException('Il n\'y a pas de catégories avec cet identifiant: ' . $id);

        }

        $em->remove($category);
        $em->flush();

        $this->addFlash(
            'danger',
            'La catégorie a bien été supprimée'
        );

        return $this->redirectToRoute('backoffice_category_home');
    }


}
