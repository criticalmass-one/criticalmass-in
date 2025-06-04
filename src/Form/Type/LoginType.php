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
                'label' => 'E-Mail-Adresse',
                'help' => 'Wenn du noch kein Benutzerkonto hast, wird automatisch ein neues mit deiner E-Mail-Adresse erstellt.'
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
