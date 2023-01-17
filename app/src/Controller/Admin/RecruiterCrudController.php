<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Recruiter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class RecruiterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recruiter::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Рекрутер')
            ->setEntityLabelInPlural('Рекрутеры')
            ->setSearchFields(['fullName', 'email'])
            ->setDefaultSort(['fullName' => 'ASC'])
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('fullName', 'Полное имя'))
            ->add(TextFilter::new('email', 'Почта'))
            ->add(EntityFilter::new('vacancies', 'Вакансия рекрутера'))
            ->add(EntityFilter::new('company', 'Компания рекрутера'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('fullName', 'Полное имя');
        yield TextField::new('email', 'Email');
        yield TextField::new('username', 'Логин');
        yield TextField::new('password', 'Пароль')->hideOnIndex();
        yield AssociationField::new('company', 'Компания')->setRequired(false);
    }
}
