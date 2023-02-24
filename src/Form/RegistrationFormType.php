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
                'label' => 'Логин',
                'required' => true,
                'attr' => [
                    'autofocus' => true,
                ],
            ])
            ->add('fullName', TextType::class, [
                'label' => 'Введите ФИО',
                'required' => true,
            ])
            ->add('email', TextType::class, [
                'label' => 'Ваш email',
                'required' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Пароли должны совпадать',
                'first_options' => [
                    'label' => 'Введите пароль',
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Пожалуйста, введите пароль',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Пароль должен содержать не менее {{ limit }} символов.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Повторите пароль',
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'required' => true,
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Зарегистрироваться',
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
