<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClippingType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('imageFile', FileType::class, array(
            'label' => 'Clipping Image',
            'required' => true,
            'attr' => array(
                'help_block' => 'Select a file to upload.',
            ),
        ));
        
        $builder->add('number', null, array(
            'label' => 'Number',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('writtenDate', null, array(
            'label' => 'Written Date',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('date', null, array(
            'label' => 'Date',
            'required' => true,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('transcription', null, array(
            'label' => 'Transcription',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('annotations', null, array(
            'label' => 'Annotations',
            'required' => false,
            'attr' => array(
                'help_block' => '',
            ),
        ));
        $builder->add('category');
        $builder->add('source');
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Clipping'
        ));
    }

}
