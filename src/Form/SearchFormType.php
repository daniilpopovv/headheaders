<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query_text', SearchType::class, [
                'label' => 'Введите запрос',
                'attr' => ['class' => 'form-control search-field'],
                'required' => false,
            ])
            ->add('query_skills', EntityType::class, [
                'label' => 'Выберите навыки',
                'class' => Skill::class,
                'multiple' => true,
                'autocomplete' => true,
                'no_more_results_text' => 'Больше навыков нет',
                'no_results_found_text' => 'Навык не найден',
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Поиск',
                'attr' => ['class' => 'w-100 mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
