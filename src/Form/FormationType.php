<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de création ou modification d'une formation
 */
class FormationType extends AbstractType
{
    /**
     * Construction du formulaire
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (facultatif)',
                'required' => false,
            ])
            ->add('publishedAt', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
            ])
            ->add('videoId', TextType::class, [
                'label' => 'Identifiant vidéo YouTube',
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'label' => 'Playlist',
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Catégories (facultatif)',
                'required' => false,
            ]);
    }

    /**
     * Configuration des options du formulaire
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
