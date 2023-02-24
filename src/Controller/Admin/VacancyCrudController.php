<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Vacancy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class VacancyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Vacancy::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('crud.vacancy.entity_label.singular')
            ->setEntityLabelInPlural('crud.vacancy.entity_label.plural')
            ->setSearchFields(['specialization', 'owner', 'description'])
            ->setDefaultSort(['owner' => 'ASC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(NumericFilter::new('salary', 'crud.vacancy.fields.salary'))
            ->add(TextFilter::new('specialization', 'crud.vacancy.fields.specialization'))
            ->add(EntityFilter::new('owner', 'crud.vacancy.fields.owner'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('specialization', 'crud.vacancy.fields.specialization'),
            TextareaField::new('description', 'crud.vacancy.fields.description'),
            NumberField::new('salary', 'crud.vacancy.fields.salary'),
            AssociationField::new('skills', 'crud.vacancy.fields.skills')->hideOnIndex(),
            AssociationField::new('owner', 'crud.vacancy.fields.owner'),
            AssociationField::new('invites', 'crud.vacancy.fields.invites'),
            AssociationField::new('replies', 'crud.vacancy.fields.replies'),
        ];
    }
}
