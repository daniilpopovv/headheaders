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
	public static function getEntityFqcn(): string {
		return Resume::class;
	}

	public function configureCrud(Crud $crud): Crud {
		return $crud
			->setEntityLabelInSingular('Резюме')
			->setEntityLabelInPlural('Резюме')
			->setSearchFields(['specialization', 'seeker', 'description'])
			->setDefaultSort(['seeker' => 'ASC']);
	}

	public function configureFilters(Filters $filters): Filters {
		return $filters
			->add(NumericFilter::new('salary', 'Желаемая зарплата'))
			->add(TextFilter::new('specialization', 'Специализация'))
			->add(EntityFilter::new('seeker', 'Владелец резюме'));
	}

	public function configureFields(string $pageName): iterable {
		yield TextField::new('specialization', 'Специализация');
		yield TextareaField::new('description', 'О себе');
		yield NumberField::new('salary', 'Желаемая ЗП');
		yield AssociationField::new('skills', 'Скиллы')->hideOnIndex();
		yield AssociationField::new('seeker', 'Владелец');
		yield AssociationField::new('invites', 'Приглашения');
		yield AssociationField::new('respondedVacancies', 'Отклики');
	}
}
