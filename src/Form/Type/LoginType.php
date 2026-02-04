<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'form.login.email_label',
                'help' => 'form.login.email_help',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.login.submit',
            ])
        ;
    }

    public function getName(): string
    {
        return 'login';
    }
}
