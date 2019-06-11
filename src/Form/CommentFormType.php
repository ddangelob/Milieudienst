<?php

namespace App\Form;

use App\Entity\Comment;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
        $builder->add('message', TextareaType::class, array(
            'attr' => array('class' => 'form-control'),
            'constraints' => [
                new NotBlank([
                    'message' => 'Please fill in a message!'
                ]),
                new Length([
                    'min' => '5',
                    'minMessage' => 'Please fill in a longer message'
                ])
            ]));
        $builder->add('save', SubmitType::class, array('attr' => array('class' => 'btn btn-primary mt-3')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}