<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'registration.username.label',
                'required' => true,
                'attr' => [
                    'autofocus' => true,
                ],
            ])
            ->add('fullName', TextType::class, [
                'label' => 'registration.fullName.label',
                'required' => true,
            ])
            ->add('email', TextType::class, [
                'label' => 'registration.email.label',
                'required' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'registration.password.invalid_message',
                'first_options' => [
                    'label' => 'registration.password.label',
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'registration.password.constraints.notBlank',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'registration.password.constraints.length.minMessage',
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'registration.password.repeatPass.label',
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'required' => true,
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'registration.password.submit.label',
                'attr' => ['class' => 'w-100 mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
