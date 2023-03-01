<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Skill;
use App\Entity\Vacancy;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VacancyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('specialization', TextType::class, [
                'label' => 'vacancy.form.specialization.label',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'vacancy.form.description.label',
                'required' => false,
            ])
            ->add('salary', NumberType::class, [
                'label' => 'vacancy.form.salary.label',
            ])
            ->add('skills', EntityType::class, [
                'label' => 'vacancy.form.skills.label',
                'class' => Skill::class,
                'multiple' => true,
                'autocomplete' => true,
                'no_more_results_text' => 'vacancy.form.skills.no_more_results_text',
                'no_results_found_text' => 'vacancy.form.skills.no_results_found_text',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'vacancy.form.submit.label',
                'attr' => ['class' => 'w-100 mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vacancy::class,
        ]);
    }
}
