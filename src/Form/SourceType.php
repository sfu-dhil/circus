<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Source;
use Nines\UtilBundle\Form\TermType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SourceType extends TermType {
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
            'help' => 'A description of the source so that others may find it.',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('date', null, [
            'label' => 'Publication Date',
            'required' => false,
            'help' => 'Date of publication, if known.',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Source::class,
        ]);
    }
}
