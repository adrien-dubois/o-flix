<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/backoffice/user", name="user_")
 * 
 */
class UserController extends AbstractController
{
    
    /**
     * Method displaying users in backoffice
     * 
     * @Route("/", name="home")
     *
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        
        return $this->render('backoffice/user/user.html.twig', [
            'users'=>$userRepository->findAll(),
        ]);
    }

    /**
     * Method which add a User in backoffice
     * 
     * @Route("/add", name="add")
     *
     * @param Request $request
     * @return void
     */
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'Utilisateur ajouté avec succès'
            );

            return $this->redirectToRoute('user_home');
        }

        return $this->render('backoffice/user/add.html.twig',[
            'formView'=>$form->createView(),
        ]);
    }


    /**
     * Display a detail User
     * 
     * @Route("/{id}", name="show", methods={"GET"})
     *
     * @param User $user
     * @return void
     */
    public function show(User $user)
    {
        return $this->render('backoffice/user/show.html.twig',[
            'user'=>$user,
        ]);
    }

    /**
     * Method updating an existing user in backoffice
     * 
     * @Route("/{id}/update", name="update")
     *
     * @param Request $request
     * @param User $user
     * @return void
     */
    public function update(Request $request, User $user, UserPasswordHasherInterface $userPasswordHasherInterface )
    {

        $this->denyAccessUnlessGranted('edit', $user, 'Vous ne passerez pas!!');
        
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            // $user->setPassword(
            //     $userPasswordHasherInterface->hashPassword(
            //         $user,
            //         $form->get('plainPassword')->getData()
            //     )
            // );
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'Utilisateur modifié avec succès'
            );

            return $this->redirectToRoute('user_home');
        }

        return $this->render('backoffice/user/update.html.twig',[
            'user'=>$user,
            'formView'=>$form->createView(),
        ]);
    }

    /**
     * Deleting a user in backoffice
     * 
     * @Route("/{id}", name="delete", methods={"POST"})
     *
     * @param User $user
     * @param Request $request
     * @return void
     */
    public function delete(User $user, Request $request)
    {
        if($this->isCsrfTokenValid('delete'.$user->getId(),$request->request->get('_token'))){
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }
        
        return $this->redirectToRoute('user_home',[], Response::HTTP_SEE_OTHER);
    }
}
