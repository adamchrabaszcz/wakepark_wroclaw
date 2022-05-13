<?php

namespace App\Form;

use App\Entity\Option;
use App\Entity\Slot;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('beginAt', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'choice',
                'hours' => [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
                'minutes' => [0, 15, 30, 45],
                'label' => 'Start'
            ])
            ->add('endAt', DateTimeType::class, [
                'date_widget'=>'single_text',
                'time_widget' => 'choice',
                'hours' => [9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20],
                'minutes' => [0, 15, 30, 45],
                'label' => 'Koniec'
            ])
            ->add('rider', EntityType::class, [
                'class' => User::class,
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('options', EntityType::class, [
                'class' => Option::class,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Opcje dodatkowe'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Slot::class,
            'slotTime' => '0-15'
        ]);
    }
}
