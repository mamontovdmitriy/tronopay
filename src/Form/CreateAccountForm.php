<?php

namespace App\Form;

use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Asserts;

class CreateAccountForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr'        => [
                    'placeholder' => 'email',
                ],
                'help'        => 'email_help',
                'required'    => true,
                'constraints' => [
                    new Asserts\NotBlank(),
                    new Asserts\Email(),
                ],
            ])
            ->add('captcha', CaptchaType::class, [
                'length'   => 5,
                'required' => true,
                'attr'     => [
                    'placeholder' => 'captcha',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('label', false)
            ->setDefault('error_bubbling', false);
    }

    public function getBlockPrefix(): ?string
    {
        return '';
    }
}
