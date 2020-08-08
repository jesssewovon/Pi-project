<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['attr' =>['class' => 'form-control']])
            ->add('prix', NumberType::class, ['attr' =>['class' => 'form-control']])
            ->add('description', TextareaType::class, ['attr' =>['class' => 'form-control', 'rows' => 4]])
            ->add('photo', FileType::class , array(
                'data_class' => null,
                'label' =>'Choisir une image',
                'required' =>false,
                'attr' => array('onchange' => 'loadfile(event)'),
                ))
            ->add('categorie', EntityType::class , array(
                'class'   => Categorie::class,
                'choice_label'    => 'libelle',
                'placeholder' => '--- Choisir la catégorie ---',
                'multiple' => false,
                'error_bubbling' => true,
                'data_class' => null,
                'required' =>false,
                'label' =>'Catégorie',
                'attr' => ['class' => 'form-control'],
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
