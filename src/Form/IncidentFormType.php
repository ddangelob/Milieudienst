<?php

namespace App\Form;

use App\Entity\Incident;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class IncidentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('category', EntityType::class, array(
            'class' => 'App:Category',
            'attr' => array('class' => 'form-control')));
        $builder->add('location', EntityType::class, array('class' => 'App:Location', 'attr' => array('class' => 'form-control')));
        $builder->add('status', EntityType::class, array('class' => 'App:Status', 'attr' => array('class' => 'form-control')));
        $builder->add('priority', IntegerType::class, array(
            'attr' => array('class' => 'form-control'),
            'constraints' => [
            new NotBlank([
                'message' => 'Please fill in a priority!'
            ]),
            new Regex([
                'pattern' => '/^[0-9].{0}$/',
                'message' => 'Select an priority between 0 and 9'
            ])
        ]));
        $builder->add('title', TextType::class, array(
            'attr' => array('class' => 'form-control'),
            'constraints' => [
                new NotBlank([
                    'message' => 'Please fill in a title!'
                ]),
                new Length([
                    'min' => '5',
                    'minMessage' => 'Please fill in a longer title'
                ])
        ]));
        $builder->add('description', TextareaType::class, array(
            'attr' => array('class' => 'form-control'),
            'constraints' => [
                new NotBlank([
                    'message' => 'Please fill in a description!'
                ]),
                new Length([
                    'min' => '5',
                    'minMessage' => 'Please fill in a longer description'
                ])
        ]));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary mt-3')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Incident::class,
        ]);
    }
}