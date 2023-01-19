<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Vacancy;
use App\Repository\VacancyRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @property Vacancy[] $vacancies
 */
class ResumeInviteType extends AbstractType
{
    public function __construct (Security $security, VacancyRepository $vacancyRepository) {
        $this->vacancies = $vacancyRepository->findBy([
            'recruiter' => $security->getUser(),
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invites', EntityType::class, [
                'label' => 'Выберите вакансию, чтобы пригласить',
                'class' => Vacancy::class,
                'choices' => $this->vacancies,
                'choice_value' => function(Vacancy $vacancy) {
                    return $vacancy->getId();
                },
                'multiple' => true,
                'autocomplete' => true,
                'tom_select_options' => [
                    'maxItems' => 1,
                ],
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Пригласить',
                'attr' => ['class' => 'w-100 mt-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}
