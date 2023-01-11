<?php

namespace App\Controller\Admin;

use App\Entity\Seeker;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SeekerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Seeker::class;
    }


    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('fullName', 'Полное имя');
        yield TextField::new('email', 'Email');
        yield TextField::new('username', 'Логин');
        yield TextField::new('password', 'Пароль')->hideOnIndex();
        yield ArrayField::new('roles', 'Роли');
    }
}
