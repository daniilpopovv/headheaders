<?php

namespace App\Controller\Admin;

use App\Entity\Vacancy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;

class VacancyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Vacancy::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Вакансия')
            ->setEntityLabelInPlural('Вакансии')
            ->setSearchFields(['specialization'])
            ->setDefaultSort(['specialization' => 'ASC'])
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(NumericFilter::new('salary'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('specialization', 'Специализация');
        yield TextareaField::new('description', 'Описание вакансии');
        yield NumberField::new('salary', 'Предлагаемая ЗП');
        yield AssociationField::new('skills', 'Скиллы')->hideOnIndex();
        yield AssociationField::new('recruiter', 'Владелец');
        yield AssociationField::new('invitedResumes', 'Приглашения');
        yield AssociationField::new('responses', 'Отклики');
    }
}
