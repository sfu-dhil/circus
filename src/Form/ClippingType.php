<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Clipping;
use App\Services\FileUploader;
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

    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('imageFile', FileType::class, [
            'label' => 'Clipping Image',
            'required' => true,
            'help' => "Select a file to upload which is less than {$this->fileUploader->getMaxUploadSize(false)} in size.",
            'attr' => [
                'data-maxsize' => $this->fileUploader->getMaxUploadSize(),
            ],
        ]);

        $builder->add('number', null, [
            'label' => 'Handwritten Number',
            'required' => false,
            'help' => 'Usually found in the corner.',
        ]);
        $builder->add('writtenDate', null, [
            'label' => 'Written Date',
            'required' => false,
            'help' => 'eg. “April 6 98” or “April 1768” for a handwritten date or leave blank for no date.',
        ]);
        $builder->add('date', null, [
            'label' => 'Date',
            'required' => false,
            'help' => 'Standard form: yyyy-mm-dd. Eg. “1768-04-21” or “1768-04-00” if no day is given.',
        ]);
        $builder->add('category', null, [
            'label' => 'Category',
            'help' => 'Categorize the clipping as text, playbill, or manuscript.',
        ]);

        $builder->add('source', null, [
            'label' => 'Source',
            'help' => 'Select the source of the clipping.',
        ]);

        $builder->add('transcription', TextareaType::class, [
            'label' => 'Transcription',
            'required' => false,
            'help' => 'If there is any non-standard spelling, please note this by [sic].',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('annotations', TextareaType::class, [
            'label' => 'Annotations',
            'required' => false,
            'help' => 'Not shown to the public.',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Clipping::class,
        ]);
    }
}
