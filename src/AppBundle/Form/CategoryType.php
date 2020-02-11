<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Form;

use Nines\UtilBundle\Form\TermType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends TermType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('name', null, [
            'label' => 'Name',
            'attr' => [
                'help_block' => 'Internal use only. Lowercase letters, numbers, and hyphens only please. Should not be changed.',
            ],
        ]);
        $builder->add('label', null, [
            'label' => 'Label',
            'attr' => [
                'help_block' => 'A proper, human-readable label in English.',
            ],
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'help_block' => 'A description of the category.',
                'class' => 'tinymce',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Category',
        ]);
    }
}
