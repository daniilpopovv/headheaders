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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class ResumeInviteType extends AbstractType
{
    private array $vacancies;

    public function __construct(Security $security, VacancyRepository $vacancyRepository)
    {
        $this->vacancies = $vacancyRepository->findByOwner($security->getUser());
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('invites', EntityType::class, [
                'label' => 'negotiation.invite.form.label',
                'class' => Vacancy::class,
                'choices' => $this->vacancies,
                'choice_value' => function (Vacancy $vacancy) {
                    return $vacancy->getId();
                },
                'multiple' => true,
                'autocomplete' => true,
                'tom_select_options' => [
                    'maxItems' => 1,
                ],
                'required' => true,
                'no_more_results_text' => 'negotiation.invite.form.no_more_results_text',
                'no_results_found_text' => 'negotiation.invite.form.no_results_found_text',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'negotiation.invite.form.submit',
                'attr' => ['class' => 'w-100 mt-3']
            ]);
    }
}
