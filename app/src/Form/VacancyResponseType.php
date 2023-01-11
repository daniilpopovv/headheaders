<?php

namespace App\Form;

use App\Entity\Resume;
use App\Entity\Vacancy;
use App\Repository\ResumeRepository;
use App\Repository\SeekerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @property Resume[] $resumes
 */
class VacancyResponseType extends AbstractType
{
    public function __construct (Security $security, ResumeRepository $resumeRepository, SeekerRepository $seekerRepository) {

        $seeker = $seekerRepository->findOneBy(['username' => $security->getUser()->getUserIdentifier()]);
        $this->resumes = $resumeRepository->findBy(['seeker' => $seeker]);
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('responses', EntityType::class, [
                'label' => 'Выберите резюме, чтобы откликнуться на вакансию',
                'class' => Resume::class,
                'choices' => $this->resumes,
                'choice_value' => function(Resume $resume) {
                    return $resume->getId();
                },
                'multiple' => true,
                'autocomplete' => true,
                'tom_select_options' => [
                    'maxItems' => 1,
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Откликнуться',
                'attr' => ['class' => 'w-100 mt-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
//            'data_class' => Vacancy::class,
        ]);
    }
}
