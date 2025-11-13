<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('orderNumber', TextType::class, [
                'label' => 'order.form.order_number',
                'attr' => ['placeholder' => 'order.form.placeholder.order_number'],
            ])
            ->add('total', NumberType::class, [
                'label' => 'order.form.total',
                'attr' => ['placeholder' => 'order.form.placeholder.total'],
            ])
            ->add('createdAt', DateTimeType::class, [
                'label' => 'order.form.created_at',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'order.form.user',
                'choice_label' => fn(User $user) => $user->getEmail() ?: 'User #'.$user->getId(),
                'placeholder' => 'order.form.placeholder.user',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'translation_domain' => 'forms', // important pour ranger les clés dans forms.*
        ]);
    }
}
