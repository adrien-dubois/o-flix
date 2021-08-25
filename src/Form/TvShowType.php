<?php

namespace App\Form;

use App\Entity\TvShow;
use Symfony\Component\Form\AbstractType;
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
            ->add('publishedAt')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('characters')
            ->add('categories')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TvShow::class,
        ]);
    }
}
