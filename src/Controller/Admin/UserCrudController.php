<?php

namespace App\Controller\Admin;

use App\Entity\User;
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
	public static function getEntityFqcn(): string {
		return User::class;
	}

	public function configureCrud(Crud $crud): Crud {
		return $crud
			->setEntityLabelInSingular('Рекрутер')
			->setEntityLabelInPlural('Рекрутеры')
			->setSearchFields(['fullName', 'email'])
			->setDefaultSort(['fullName' => 'ASC']);
	}

	public function configureFilters(Filters $filters): Filters {
		return $filters
			->add(TextFilter::new('fullName', 'Полное имя'))
			->add(TextFilter::new('email', 'Почта'))
			->add(EntityFilter::new('vacancies', 'Вакансия рекрутера'))
			->add(EntityFilter::new('company', 'Компания рекрутера'))
			->add(ArrayFilter::new('roles', 'Роли')->setChoices([
				'Пользователь' => 'ROLE_USER',
				'Соискатель' => 'ROLE_SEEKER',
				'Рекрутер' => 'ROLE_RECRUITER',
				'Админ' => 'ROLE_ADMIN'
			]));
	}

	public function configureFields(string $pageName): iterable {
		yield TextField::new('fullName', 'Полное имя');
		yield TextField::new('email', 'Email');
		yield TextField::new('username', 'Логин');
		yield TextField::new('password', 'Пароль')->hideOnIndex();
		yield AssociationField::new('company', 'Компания')->setRequired(false);
		yield ArrayField::new('roles', 'Роли');
	}
}