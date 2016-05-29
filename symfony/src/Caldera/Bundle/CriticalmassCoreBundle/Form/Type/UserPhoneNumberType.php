<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserPhoneNumberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', 'text', ['required' => false])
        ;
    }

    public function getName()
    {
        return 'subride';
    }
}