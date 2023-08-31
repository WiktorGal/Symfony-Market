<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Item::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            ImageField::new('imageName')
                ->setLabel('Image')
                ->setUploadDir('public/uploads/images') // Set the upload directory
                ->setBasePath('/uploads/images') // Set the base path for displaying images
                ->onlyOnDetail(), // Display only on detail view
            TextEditorField::new('description'),
            AssociationField::new('category'),
            TextField::new('price'),
            BooleanField::new('isSold'),
            AssociationField::new('createdBy'),
        ];
    }

}

