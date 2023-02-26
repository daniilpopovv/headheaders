<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query_text', SearchType::class, [
                'label' => 'search.query.text.label',
                'attr' => ['class' => 'form-control search-field'],
                'required' => false,
            ])
            ->add('query_skills', EntityType::class, [
                'label' => 'search.query.skills.label',
                'class' => Skill::class,
                'multiple' => true,
                'autocomplete' => true,
                'no_more_results_text' => 'search.query.skills.no_more_results_text',
                'no_results_found_text' => 'search.query.skills.no_results_found_text',
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'search.submit.label',
                'attr' => ['class' => 'w-100 mt-3'],
            ]);
    }
}
