<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Source;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClippingSearchType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('transcription', TextType::class, [
            'label' => 'Search query',
            'required' => false,
            'help' => 'Search within the transcription',
        ]);

        $builder->add('date', null, [
            'label' => 'Date',
            'required' => false,
            'help' => 'Standard form: yyyy-mm-dd. Eg. “1768-04-21” or “1768-04-00” if no day is given',
            'attr' => [
                'placeholder' => '1768-04-21',
            ],
        ]);

        $builder->add('writtenDate', null, [
            'label' => 'Written Date',
            'required' => false,
            'help' => 'eg. “April 6 98” or “April 1768” for a handwritten date',
            'attr' => [
                'placeholder' => 'April 6 98',
            ],
        ]);

        $builder->add('number', null, [
            'label' => 'Handwritten Number',
            'required' => false,
            'help' => 'Usually found in the corner',
        ]);

        $builder->add('category', EntityType::class, [
            'label' => 'Category',
            'class' => Category::class,
            'expanded' => true,
            'multiple' => true,
            'help' => 'The type of the clipping',
        ]);

        $builder->add('source', EntityType::class, [
            'label' => 'Source',
            'class' => Source::class,
            'expanded' => true,
            'multiple' => true,
            'help' => 'Select the source of the clipping',
        ]);

        $builder->add('order', ChoiceType::class, [
            'label' => 'Sort by',
            'choices' => [
                'Unsorted' => null,
                'Date' => 'date',
                'Handwritten Number' => 'number',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        parent::configureOptions($resolver);
    }
}
