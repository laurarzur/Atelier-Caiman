<?php

namespace App\Controller\Admin;

use App\Entity\Realisation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; 
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RealisationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Realisation::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('serie'),
            TextField::new('nom'),
            TextEditorField::new('description'),
            TextField::new('fichierImage')->setFormType(VichImageType::class), 
            ImageField::new('image')->setBasePath('/uploads/images')->onlyOnIndex()
        ];
    }
    
}
