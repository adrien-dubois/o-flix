<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Character;
use App\Entity\TvShow;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TvShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('synopsis')
            ->add('image')
            ->add('nbLikes')
            ->add('publishedAt', DateTimeType::class,[
                'label'=>'Date de publication',
                'input'=>'datetime_immutable',
                'widget'=>'single_text'
            ])
            ->add('characters', EntityType::class, [
                'class'=>Character::class,
                'label'=>'Personnages',
                'expanded'=>true,
                'multiple'=>true,
                'choice_label'=>'fullname'
            ])
            ->add('categories', EntityType::class,[
                'class'=>Category::class,
                'label'=>'Catégories',
                'expanded'=>true,
                'multiple'=>true,
                'choice_label'=>'name'
            ])
            ->add('submit', SubmitType::class,[
                'label'=>'Ajouter',
                'attr'=>[
                    'class'=>'btn btn-dark mb-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TvShow::class,
        ]);
    }
}
