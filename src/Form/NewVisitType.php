<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewVisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [

                'row_attr' => [
                    'class' => 'field',
                ]
            ])
            ->add('specialist', EntityType::class, [
                'class' => User::class,
                'choices' => $options['specialists'],
                'choice_label' => 'name',
                'row_attr' => [
                    'class' => 'field',
                ]

            ])
            ->add('register', SubmitType::class, ['attr' => [
                'class' => 'ui primary button',
            ]]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'specialists' => null,

        ));

    }

}
