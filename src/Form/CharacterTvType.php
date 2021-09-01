<?php

namespace App\Form;

use App\Entity\Character;
use App\Entity\TvShow;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CharacterTvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, [
                'label'=>'Prénom du personnage',
                'attr'=>[
                    'placeholder'=>'Prénom'
                ]
            ])
            ->add('lastname', null, [
                'label'=>'Nom du personnage',
                'attr'=>[
                    'placeholder'=>'Nom'
                ]
            ])
            ->add('gender', ChoiceType::class,[
                'label' => 'Sexe du personnage',
                'choices' =>[
                    'Homme'=>'Homme',
                    'Femme'=>'Femme'
                ]
            ])
            ->add('truename', null, [
                'label'=>'Nom/Prénom de l\'acteur'
            ])
            ->add('bio', TextareaType::class)
            ->add('age',null, [
                'label'=> 'Âge',
                'attr'=>[
                    'type'=> 'number',
                    'placeholder'=>'Âge du personnage'
                ]
            ])
            ->add('imgBrut', FileType::class, [
                'label' => 'Choisir une image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Seuls les fichiers images de type JPEG & PNG sont autorisés',
                    ])
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Nouveau personnage',
                'attr'=>[
                    'class'=>'btn btn-secondary mb-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Character::class,
        ]);
    }
}
