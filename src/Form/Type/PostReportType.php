<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\PostReport;
use App\Enum\PostReportReasonEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PostReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reason', EnumType::class, [
                'class' => PostReportReasonEnum::class,
                'choice_label' => fn(PostReportReasonEnum $reason) => $reason->label(),
                'expanded' => true,
                'label' => 'Was stimmt mit diesem Beitrag nicht?',
            ])
            ->add('explanation', TextareaType::class, [
                'required' => false,
                'label' => 'Erklärung',
                'help' => 'Bei rechtswidrigen Inhalten und bei „Etwas anderes“ brauchen wir eine kurze Erklärung, sonst können wir nicht entscheiden.',
                'attr' => ['rows' => 4, 'maxlength' => 2000],
            ]);

        // Gaeste brauchen eine Adresse, damit sie Bestaetigung und Entscheidung erhalten.
        if ($options['guest']) {
            $builder->add('reporterEmail', EmailType::class, [
                'label' => 'Deine E-Mail-Adresse',
                'help' => 'Wir schicken dir eine Eingangsbestätigung und später unsere Entscheidung. Für nichts anderes.',
                'constraints' => [new Assert\NotBlank(message: 'Bitte gib eine E-Mail-Adresse an.')],
            ]);
        }

        $builder->add('goodFaith', CheckboxType::class, [
            'mapped' => false,
            'label' => 'Ich bin nach bestem Wissen überzeugt, dass meine Angaben richtig und vollständig sind.',
            'constraints' => [new Assert\IsTrue(message: 'Bitte bestätige, dass deine Angaben nach bestem Wissen richtig sind.')],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostReport::class,
            'guest' => false,
        ]);

        $resolver->setAllowedTypes('guest', 'bool');
    }
}
