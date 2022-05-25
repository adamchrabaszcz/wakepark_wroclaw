<?php

namespace App\Controller\Admin;

use App\Entity\Rider;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RiderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rider::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('firstName'),
            TextField::new('surname'),
            TextField::new('phone'),
            AssociationField::new('user')
        ];
    }

}
