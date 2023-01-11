<?php

namespace App\Controller\Admin;

use App\Entity\Recruiter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RecruiterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recruiter::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('fullName', 'Полное имя');
        yield TextField::new('email', 'Email');
        yield TextField::new('username', 'Логин');
        yield TextField::new('password', 'Пароль')->hideOnIndex();
        yield ArrayField::new('roles', 'Роли');
        yield AssociationField::new('company', 'Компания')->setRequired(false);
    }
}
