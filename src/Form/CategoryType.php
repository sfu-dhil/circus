<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use Nines\UtilBundle\Form\TermType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends TermType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('name', null, [
            'label' => 'Name',
            'help' => 'Internal use only. Lowercase letters, numbers, and hyphens only please. Should not be changed.',
        ]);
        $builder->add('label', null, [
            'label' => 'Label',
            'help' => 'A proper, human-readable label in English.',
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'help' => 'A description of the category.',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
