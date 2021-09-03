<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('lastname',null,[
                'label'=>'Nom'
            ])
            ->add('firstname',null,[
                'label'=>'Prénom'
            ])
            ->add('roles', ChoiceType::class, [
                'label'=> 'Rôle',
                'choices' => [
                    'Membre' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Boss' => 'ROLE_SUPER_ADMIN'
                ],
                'multiple'  => true,
                'expanded'=> true
            ])




                // On va se brancher à un événement PRE_SET_DATA pour afficher le champ plainPassword en fonction du contexte dans lequel on se trouve :
                // - à la création : on affiche le champs
                // - à l'édition : on ne l'affiche pas

                ;
                $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                    // On récupère les données de l'utilisateur que l'on s'apprête à créer ou éditer
                    $user = $event->getData();
                    $form = $event->getForm();

                    // Si nous sommes dans le cas d'une création de compte utilisateur, alors on ajoute le champs du mote de passe
                    if($user->getId() === null){
                        $form->add('plainPassword', RepeatedType::class, [
                            'type' => PasswordType::class,
                            'invalid_message' => 'Les mots de passe doivent être identiques',
                            'options' => ['attr' => ['class' => 'password-field','placeholder'=>'Mot de passe']],
                            'required' => true,
                            'first_options'  => ['label' => 'Mot de passe (6 caractères minimum)'],
                            'second_options' => ['label' => 'Confirmez votre mot de passe'],
                            'mapped' => false,
                            'attr' => ['autocomplete' => 'new-password',],
                            'constraints' => [
                                new NotBlank([
                                    'message' => 'Merci de renseigner un mot de passe',
                                ]),
                                new Length([
                                    'min' => 6,
                                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                                    // max length allowed by Symfony for security reasons
                                    'max' => 4096,
                                ]),
                            ],
                        ]);
                    }
                    
                });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
