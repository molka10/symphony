<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du livre',
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Science-Fiction' => 'Science-Fiction',
                    'Mystery' => 'Mystery',
                    'Autobiography' => 'Autobiography',
                    'Romance' => 'Romance',
                ],
            ])
            ->add('publicationDate', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('author', EntityType::class, [
    'class' => Author::class,
    'choice_label' => 'username', // ou 'name' si ton entité Author a une propriété 'name'
    'label' => 'Auteur',
])

            ->add('enabled', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
            ])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'username', // ou 'name' selon ce que tu veux afficher
                'label' => 'Auteur',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
