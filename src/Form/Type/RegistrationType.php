<?php declare(strict_types=1);

namespace App\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends RegistrationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('captcha', CaptchaType::class, [
            'as_url' => true,
            'reload' => true,
            'length' => 5,
            'max_front_lines' => 3,
            'max_behind_lines' => 3,
        ]);
    }
}
