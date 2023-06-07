<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Resume;
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

class ResumeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Resume::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('crud.resume.entity_label.singular')
            ->setEntityLabelInPlural('crud.resume.entity_label.plural')
            ->setSearchFields(['specialization', 'owner.name', 'description'])
            ->setDefaultSort(['owner' => 'ASC']);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(NumericFilter::new('salary', 'crud.resume.fields.salary'))
            ->add(TextFilter::new('specialization', 'crud.resume.fields.specialization'))
            ->add(EntityFilter::new('owner', 'crud.resume.fields.owner'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('specialization', 'crud.resume.fields.specialization'),
            TextareaField::new('description', 'crud.resume.fields.description'),
            NumberField::new('salary', 'crud.resume.fields.salary'),
            AssociationField::new('skills', 'crud.resume.fields.skills')->hideOnIndex(),
            AssociationField::new('owner', 'crud.resume.fields.owner'),
            AssociationField::new('invites', 'crud.resume.fields.invites'),
            AssociationField::new('replies', 'crud.resume.fields.replies'),
        ];
    }
}
