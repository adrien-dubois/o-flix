<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, UserAuthenticatorInterface $authenticator, LoginFormAuthenticator $formAuthenticator, \Swift_Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Génération du token
            $user->setActivationToken(md5(uniqid()));


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            
            // do anything else you need here, like send an email

            $message =(new \Swift_Message('Activation de votre compte'))
                    ->setFrom('admin@oclock.io')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'email/activation.html.twig',
                            ['token'=>$user->getActivationToken()]
                        ),
                        'text/html'
                    )
            ;
            $mailer->send($message);

            $this->addFlash(
                'success',
                'Veuillez activer votre compe par mail'
            )
            ;

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Methode d'activation par mail
     * 
     * @Route("/activation/{token}", name="activation")
     *
     * @param [type] $token
     * @param UserRepository $userRepository
     * @return void
     */
    public function activation($token, UserRepository $userRepository)
    {
        // On recherche l'utilisateur en BDD avec son token unique
        $user = $userRepository->findOneBy(['activation_token'=>$token]);

        // Si l'utilisateur n'existe pas, on bloque
        if(!$user){
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // Sinon on seupprime le token
        $user->setActivationToken('');
        $em = $this->getDoctrine()->getManager()->flush();

        // Conirmation par flash
        $this->addFlash(
            'success',
            'Votre compte a bien été activé'
        );

        // Et redirection
        return $this->redirectToRoute('home');

    }


} 
