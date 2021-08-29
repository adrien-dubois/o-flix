<?php

namespace App\Controller\Backoffice;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/backoffice/character", name="backoffice_character_")
 * @IsGranted("ROLE_ADMIN")
 */
class CharacterController extends AbstractController
{

    /**
     * Method displaying index of characters
     *
     * @Route("/", name="home")
     * 
     * @return Response
     */
    public function index(CharacterRepository $characterRepository): Response
    {
        return $this->render('backoffice/character/character.html.twig', [
            'characters' => $characterRepository->findAll(),
        ]);
    }

    /**
     * Method to add a new character
     * 
     * @Route("/add", name="add")
     *
     * @param Request $request
     * @return void
     */
    public function add(Request $request)
    {
        $character = new Character();

        $form = $this->createForm(CharacterType::class, $character);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($character);
            $em->flush();

            $this->addFlash(
                'success',
                'Le personnage a bien été créé'
            );

            return $this->redirectToRoute('backoffice_character_home');
        }

        return $this->render('backoffice/character/add.html.twig', [
            'formView' => $form->createView(),
        ]);
    }

    /**
     * Edit an existing character
     *
     * @Route("/update/{id}", name="update", requirements={"id"="\d+"} )
     * 
     * @param INT $id
     * @param Request $request
     * @param Character $character
     * @return void
     */
    public function update($id, Request $request, Character $character)
    {
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                'Personnage modifié avec succès'
            );

            return $this->redirectToRoute('backoffice_character_home');
        }

        return $this->render('backoffice/character/update.html.twig',[
            'character' => $character,
            'formView'=>$form->createView(),
        ]);

    }

    /**
     * Delete a character by its ID
     * 
     * @Route("/delete/{id}", name="delete", requirements={"id"="\d+"})
     *
     * @param [type] $id
     * @return void
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $character = $this->getDoctrine()->getRepository(Character::class);
        $character = $character->find($id);

        if(!$character){
            throw $this->createNotFoundException('Il n\'y a pas de personnage avec cet identifiant: ' . $id);

        }

        $em->remove($character);
        $em->flush();

        $this->addFlash(
            'danger',
            'Le personnage a bien été supprimée'
        );

        return $this->redirectToRoute('backoffice_character_home');
    }
}
