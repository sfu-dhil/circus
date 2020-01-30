<?php

namespace AppBundle\Form;

use AppBundle\Services\FileUploader;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClippingType extends AbstractType {
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(FileUploader $fileUploader) {
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('imageFile', FileType::class, array(
            'label' => 'Clipping Image',
            'required' => true,
            'attr' => array(
                'help_block' => "Select a file to upload which is less than {$this->fileUploader->getMaxUploadSize(false)} in size.",
                'data-maxsize' => $this->fileUploader->getMaxUploadSize(),
            ),
        ));

        $builder->add('number', null, array(
            'label' => 'Handwritten Number',
            'required' => false,
            'attr' => array(
                'help_block' => 'Usually found in the corner.',
            ),
        ));
        $builder->add('writtenDate', null, array(
            'label' => 'Written Date',
            'required' => false,
            'attr' => array(
                'help_block' => 'eg. “April 6 98” or “April 1768” for a handwritten date or leave blank for no date.',
            ),
        ));
        $builder->add('date', null, array(
            'label' => 'Date',
            'required' => false,
            'attr' => array(
                'help_block' => 'Standard form: yyyy-mm-dd. Eg. “1768-04-21” or “1768-04-00” if no day is given.',
            ),
        ));
        $builder->add('category', null, array(
            'attr' => array(
                'help_block' => 'Categorize the clipping as text, playbill, or manuscript.',
            ),
        ));

        $builder->add('source', null, array(
            'attr' => array(
                'help_block' => 'Select the source of the clipping.',
            ),
        ));

        $builder->add('transcription', TextareaType::class, array(
            'label' => 'Transcription',
            'required' => false,
            'attr' => array(
                'help_block' => 'If there is any non-standard spelling, please note this by [sic].',
                'class' => 'tinymce',
            ),
        ));
        $builder->add('annotations', TextareaType::class, array(
            'label' => 'Annotations',
            'required' => false,
            'attr' => array(
                'help_block' => 'Not shown to the public.',
                'class' => 'tinymce',
            ),
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Clipping',
        ));
    }
}
