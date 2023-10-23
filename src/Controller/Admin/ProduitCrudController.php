<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('categorie'),
            TextField::new('titre'),
            SlugField::new('slug')->setTargetFieldName('titre'), 
            TextEditorField::new('description'),
            BooleanField::new('disponible'), 
            MoneyField::new('prix')->setCurrency('EUR'),
            TextField::new('fichierImage')->setFormType(VichImageType::class), 
            ImageField::new('image')->setBasePath('/uploads/images')->onlyOnIndex()
        ];
    }
    
}
