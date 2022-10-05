<?php

namespace App\Form;

use App\Entity\PaymentDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'cash/card',
                ],
                'choice_attr' => [
                    'With cash' => ['checked' => 'checked'],                
                ],
                'choices' => [
                    'With cash' => 'cash',
                    'With credit card' => 'credit-card'
                ],
                'choice_attr' => [
                    'With cash' => ['checked' => 'checked'],
                ],
                'expanded' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PaymentDetails::class,
        ]);
    }
}
