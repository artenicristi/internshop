<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', CollectionType::class,[
                'allow_add' => true,
                'entry_type' => OrderItemType::class,

                /* this line is required in order to trigger addItem instead of interaction throught getItems() collection */
                // @see https://symfony.com/doc/current/reference/forms/types/collection.html#by-reference
                'by_reference' => false
            ])
            ->add('address', AddressType::class)
            ->add('paymentDetails', PaymentDetailsType::class)
            ->add('creditCardDetails', CreditCardDetailsType::class);
            ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            /** @var array $order */
            $order = $event->getData();
            $form = $event->getForm();
   
            if ($order['paymentDetails']['type'] == 'cash') {
                $form->remove('creditCardDetails');
                unset($order['creditCardDetails']);
                $event->setData($order);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'csrf_protection' => false, /* @fixme later */
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'order',
        ]);
        
    }
}
