<?php

namespace AppBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Nines\UtilBundle\Form\TermType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends TermType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('name', null, array(
            'label' => 'Name',
            'attr' => array(
                'help_block' => 'Internal use only. Lowercase letters, numbers, and hyphens only please. Should not be changed.',
            ),
        ));
        $builder->add('label', null, array(
            'label' => 'Label',
            'attr' => array(
                'help_block' => 'A proper, human-readable label in English.',
            ),
        ));
        $builder->add('description', CKEditorType::class, array(
            'label' => 'Description',
            'required' => false,
            'attr' => array(
                'help_block' => 'A description of the category.',
            ),
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Category',
        ));
    }
}
