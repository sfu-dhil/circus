<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use Nines\UtilBundle\Form\TermType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SourceType extends TermType
{
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
            'attr' => [
                'help_block' => 'A description of the source so that others may find it.',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('date', null, [
            'label' => 'Publication Date',
            'required' => false,
            'attr' => [
                'help_block' => 'Date of publication, if known.',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Source',
        ]);
    }
}
