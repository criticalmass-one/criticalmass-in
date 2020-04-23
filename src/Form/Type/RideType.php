<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Ride;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Ride $ride */
        $ride = $builder->getData();

        /** @var string $timezone */
        $timezone = $ride->getCity()->getTimezone();

        $builder
            ->add('title', TextType::class, ['required' => true])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('dateTime', DateTimeType::class, [
                'model_timezone' => 'UTC',
                'view_timezone' => $timezone,
                'date_widget' => 'single_text',
                'date_format' => 'dd.MM.yyyy', //here
                'time_widget' => 'single_text',
                'compound' => true
            ])
            ->add('location', TextType::class, ['required' => false])
            ->add('latitude', HiddenType::class, ['required' => false])
            ->add('longitude', HiddenType::class, ['required' => false]);

        if (!$ride->isEnabled()) {
            $builder->add('enabled', CheckboxType::class, ['required' => false]);
        }

        $builder->add('save', SubmitType::class);
    }

    public function getName(): string
    {
        return 'ride';
    }
}
