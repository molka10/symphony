<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AuthorSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minBooks', IntegerType::class, [
                'required' => false,
                'label' => 'Min nb livres',
            ])
            ->add('maxBooks', IntegerType::class, [
                'required' => false,
                'label' => 'Max nb livres',
            ])
            ->add('search', SubmitType::class, ['label' => 'Filtrer']);
    }
}
