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

            // Generate activation token
            $user->setActivationToken(md5(uniqid()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            
            // do anything else you need here, like send an email

            $message = (new \Swift_Message('Activation de votre compte'))
                ->setFrom('admin@oflix.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'email/activation.html.twig', ['token'=>$user->getActivationToken()]
                    ),
                    'text/html'
                )
            ;

            $mailer->send($message);

            $this->addFlash(
                            'success',
                            'Veuillez activez votre compte par mail.'
                        );
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Activate user
     *  
     * @Route("/activation/{token}", name="activation")
     *
     */
    public function activation($token, UserRepository $userRepository)
    {
        // We check if a user has this token
        $user = $userRepository->findOneBy(['activation_token' => $token]);

        // If no users has this token
        if(!$user){
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // We delete the token
        $user->setActivationToken('');
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash(
            'success',
            'Votre compte a bien été activé'
        );

        return $this->redirectToRoute('home');
    }

} 
