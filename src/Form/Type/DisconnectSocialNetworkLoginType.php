<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\User;
use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

class DisconnectSocialNetworkLoginType extends ChangePasswordFormType
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('current_password')
            ->add('email', EmailType::class, [
                'label' => 'E-Mail-Adresse',
                'translation_domain' => 'FOSUserBundle',
                'attr' => [
                    'autocomplete' => 'email',
                ],
            ]);
    }
}