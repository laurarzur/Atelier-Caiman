<?php

namespace App\Controller\Admin;

use App\Entity\Serie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController; 
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SerieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Serie::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre')
        ];
    }
    
}
