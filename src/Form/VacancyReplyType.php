<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Resume;
use App\Repository\ResumeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class VacancyReplyType extends AbstractType
{
    private array $resumes;

    public function __construct(Security $security, ResumeRepository $resumeRepository)
    {
        $this->resumes = $resumeRepository->findByOwner($security->getUser());
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('replies', EntityType::class, [
                'label' => 'negotiation.reply.form.label',
                'class' => Resume::class,
                'choices' => $this->resumes,
                'choice_value' => function (Resume $resume) {
                    return $resume->getId();
                },
                'multiple' => true,
                'autocomplete' => true,
                'tom_select_options' => [
                    'maxItems' => 1,
                ],
                'no_more_results_text' => 'negotiation.reply.form.no_more_results_text',
                'no_results_found_text' => 'negotiation.reply.form.no_results_found_text',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'negotiation.reply.form.submit',
                'attr' => ['class' => 'w-100 mt-3']
            ]);
    }
}
