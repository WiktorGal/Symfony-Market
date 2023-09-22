<?php

namespace App\Form;

use App\Entity\Item;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType; 
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class EditItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class, 
                'choice_label' => 'name', 
                'attr' => [
                    'class' => 'w-full py-4 px-6 rounded-xl border',
                ],
            ])
            ->add('name', TextType::class, [
                'attr' => ['class' => 'INPUT_CLASSES'],
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'INPUT_CLASSES'],
            ])
            ->add('price', NumberType::class, [
                'attr' => ['class' => 'INPUT_CLASSES'],
                'html5' => true,
            ])
            ->add('imageFile', VichImageType::class, [
                'attr' => ['class' => 'INPUT_CLASSES'],
                'required' => false, 
            ])
            ->add('isSold', CheckboxType::class, [
                'label' => 'Is Sold', 
                'required' => false, 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}
