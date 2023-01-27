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
	public static function getEntityFqcn(): string {
		return Vacancy::class;
	}

	public function configureCrud(Crud $crud): Crud {
		return $crud
			->setEntityLabelInSingular('Вакансия')
			->setEntityLabelInPlural('Вакансии')
			->setSearchFields(['specialization', 'recruiter', 'description'])
			->setDefaultSort(['recruiter' => 'ASC']);
	}

	public function configureFilters(Filters $filters): Filters {
		return $filters
			->add(NumericFilter::new('salary', 'Предлагаемая зарплата'))
			->add(TextFilter::new('specialization', 'Специализация'))
			->add(EntityFilter::new('recruiter', 'Владелец вакансии'));
	}

	public function configureFields(string $pageName): iterable {
		yield TextField::new('specialization', 'Специализация');
		yield TextareaField::new('description', 'Описание вакансии');
		yield NumberField::new('salary', 'Предлагаемая ЗП');
		yield AssociationField::new('skills', 'Скиллы')->hideOnIndex();
		yield AssociationField::new('recruiter', 'Владелец');
		yield AssociationField::new('invitedResumes', 'Приглашения');
		yield AssociationField::new('replies', 'Отклики');
	}
}
