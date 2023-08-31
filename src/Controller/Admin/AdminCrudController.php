<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // Import PasswordType from Symfony

class AdminCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Admin::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email'),
            ArrayField::new('roles'),
            TextField::new('password')
                ->setFormType(PasswordType::class) // Use Symfony's PasswordType here
                ->onlyOnForms() // Display only on forms, not in the list view
                ->setRequired(true),
            AssociationField::new('items')
                ->setLabel('Items Created')
                ->onlyOnDetail() // Display only on the detail view
        ];
    }
}
