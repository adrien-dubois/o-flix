<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Character;
use App\Entity\TvShow;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class TvShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('synopsis')
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
                    ]),
                    new NotBlank([
                        'message'=>'Merci de fournir une image'
                    ]),
                ]
            ])
            ->add('publishedAt', DateTimeType::class,[
                'label'=>'Date de publication',
                'input'=>'datetime_immutable',
                'widget'=>'single_text'
            ])
            ->add('categories', EntityType::class,[
                'class'=>Category::class,
                'label'=>'Catégories',
                'expanded'=>true,
                'multiple'=>true,
                'choice_label'=>'name',
                'attr'=>[
                    'class'=>'text-center mx-auto'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TvShow::class,
            "allow_extra_fields" => true
        ]);
    }
}
