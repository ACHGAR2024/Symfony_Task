<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'Titre'])
            ->add('description', null, ['label' => 'Description'])
            ->add('dueDate', null, ['label' => 'Date d\'échéance'])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'A faire' => Task::STATUS_NEW,
                    'En cours' => Task::STATUS_IN_PROGRESS,
                    'Terminée' => Task::STATUS_DONE,

                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}