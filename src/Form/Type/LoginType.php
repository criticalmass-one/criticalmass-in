<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-Mail-Adresse',
                'help' => 'Noch kein Konto? Es wird automatisch angelegt.',
                'attr' => [
                    // Der `webauthn`-Zusatz schaltet die Conditional UI frei: Der Browser
                    // bietet einen vorhandenen Passkey direkt im Autofill dieses Feldes
                    // an. Ohne Passkey verhält sich das Feld wie bisher.
                    'autocomplete' => 'username webauthn',
                ],
            ])
            ->add('remember_me', CheckboxType::class, [
                'label' => 'Eingeloggt bleiben',
                'required' => false,
                'data' => true,
                'help' => 'Du bleibst dann auf diesem Gerät ein Jahr lang angemeldet.',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Link zusenden',
            ])
        ;
    }

    public function getName(): string
    {
        return 'login';
    }
}
