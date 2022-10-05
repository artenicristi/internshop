<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(['message' => 'Please enter a valid email address.']),
                ],
                'attr' => [
                    'placeholder' => 'Your email',
                ],
            ])
            ->add('userName', TextType::class, [
                'attr' => [
                    'placeholder' => 'Your username',
                ],
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Password',
                ],
            ])
            ->add('confirmedPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Repeat your password',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
