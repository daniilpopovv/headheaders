<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\RoleEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('crud.user.entity_label.singular')
            ->setEntityLabelInPlural('crud.user.entity_label.plural')
            ->setSearchFields(['fullName', 'email'])
            ->setDefaultSort(['fullName' => 'ASC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('fullName', 'crud.user.fields.fullName'))
            ->add(TextFilter::new('email', 'crud.user.fields.email'))
            ->add(EntityFilter::new('company', 'crud.user.fields.company'))
            ->add(ArrayFilter::new('roles', 'crud.user.fields.roles.label')->setChoices([
                'crud.user.fields.roles.user' => RoleEnum::user->value,
                'crud.user.fields.roles.seeker' => RoleEnum::seeker->value,
                'crud.user.fields.roles.recruiter' => RoleEnum::recruiter->value,
                'crud.user.fields.roles.admin' => RoleEnum::admin->value,
            ]));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('fullName', 'crud.user.fields.fullName'),
            TextField::new('email', 'crud.user.fields.email'),
            TextField::new('username', 'crud.user.fields.username'),
            TextField::new('password', 'crud.user.fields.password')->hideOnIndex(),
            AssociationField::new('company', 'crud.user.fields.company')->setRequired(false),
            ArrayField::new('roles', 'crud.user.fields.roles.label'),
        ];
    }
}
