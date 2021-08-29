<?php

namespace App\Controller\Backoffice;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/backoffice/category", name="backoffice_category_", requirements={"id"="\d+"})
 * @IsGranted("ROLE_ADMIN")
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
     * Display the category details
     *
     * @Route("/{id}", name="show")
     * 
     * @param integer $id
     * @param CategoryRepository $categoryRepository
     * @return void
     */
    public function show(Category $category)
    {
        return $this->render('backoffice/category/show.html.twig', [
            'category' => $category
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
    public function update(Request $request, Category $category)
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
     * @Route("/delete/{id}", name="delete" )
     *
     * @param [type] $id
     * @return void
     */
    public function delete(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash(
            'danger',
            'La catégorie a bien été supprimée'
        );

        return $this->redirectToRoute('backoffice_category_home');
    }


}
