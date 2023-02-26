<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Resume;
use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResumeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('specialization', TextType::class, [
                'label' => 'resumes.form.specialization.label',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'resumes.form.description.label',
                'required' => false,
            ])
            ->add('salary', NumberType::class, [
                'label' => 'resumes.form.salary.label',
            ])
            ->add('skills', EntityType::class, [
                'label' => 'resumes.form.skills.label',
                'class' => Skill::class,
                'multiple' => true,
                'autocomplete' => true,
                'no_more_results_text' => 'resumes.form.skills.no_more_results_text',
                'no_results_found_text' => 'resumes.form.skills.no_results_found_text',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'resumes.form.submit.label',
                'attr' => ['class' => 'w-100 mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resume::class,
        ]);
    }
}
