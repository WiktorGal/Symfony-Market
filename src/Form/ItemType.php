<?php

namespace App\Form;

use App\Entity\Item;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\CategoryRepository; 
use Symfony\Polyfill\Intl\Icu\NumberFormatter;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
class ItemType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = $this->loadCategories();
    
        $builder
            ->add('category', ChoiceType::class, [
                'choices' => $categories,
                'choice_label' => 'name', 
                'choice_value' => 'id',
                'attr' => [
                    'class' => 'w-full py-4 px-6 rounded-xl border',
                ],
            ])
            ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'w-full py-4 px-6 rounded-xl border',
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'w-full py-4 px-6 rounded-xl border',
                ],
            ])
            ->add('price', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'class' => 'w-full py-4 px-6 rounded-xl border',
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false, 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }

    private function loadCategories(): array
    {
        $categories = $this->categoryRepository->findAll();
        $choices = [];
    
        foreach ($categories as $category) {
            $choices[$category->getName()] = $category; 
        }
    
        return $choices;
    }
}
