<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Repository\RegionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class StandardCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city',
                TextType::class, [
                    'required' => false,
                    'help' => 'Name der Stadt, etwa Hamburg oder Rendsburg.',
                ]
            )
            ->add('title',
                TextType::class, [
                    'required' => false,
                    'help' => 'Gib hier den Titel der Stadt ein, beispielsweise „Critical Mass Hamburg“ oder „Fahrradfreitag Rendsburg“.',
                ]
            )
            ->add('description',
                TextareaType::class, [
                    'required' => false,
                    'help' => 'Eine kurze, aufs Wesentliche reduzierte Beschreibung der Stadt.',
                ]
            )
            ->add('longitude',
                HiddenType::class
            )
            ->add('latitude',
                HiddenType::class
            )
            ->add('cityPopulation',
                IntegerType::class, [
                    'required' => false,
                    'help' => 'Für die Berechnung bestimmter Statistiken wird die Einwohnerzahl einer Stadt benötigt.',
                ]
            )
            ->add('region',
                EntityType::class,
                [
                    'class' => 'App:Region',
                    'query_builder' => function (RegionRepository $er) {
                        $builder = $er->createQueryBuilder('region');

                        $builder->join('region.parent', 'region2');
                        $builder->join('region2.parent', 'region3');

                        $builder->where($builder->expr()->isNotNull('region3.parent'));

                        $builder->orderBy('region2.name', 'ASC');
                        $builder->addOrderBy('region.name', 'ASC');

                        return $builder;
                    },
                    'group_by' => 'parent',
                    'help' => 'Wähle das Bundesland, den Kanton oder das County dieser Stadt aus.',
                ]
            )
            ->add('punchLine',
                TextType::class, [
                    'required' => false,
                    'help' => 'Hier kann ein ganz einprägsamer Satz hin. Sowas wie „We are traffic“ oder „Reclaim the streets“ ist ja schon fast Standard.',
                ]
            )
            ->add('longDescription',
                TextareaType::class, [
                    'required' => false,
                    'help' => 'Hier ist Platz für eine längere und ausführlichere Beschreibung.',
                ]
            )
            ->add('enableBoard',
                CheckboxType::class, [
                    'required' => false,
                    'help' => 'Aktiviere diese Checkbox, um ein Diskussionsforum für diese Stadt zu aktivieren.',
                ]
            )
            ->add('imageFile',
                VichFileType::class, [
                    'required' => false,
                    'help' => 'Lade hier ein Foto für diese Stadt hoch. Bitte beachte, dass du das Urheber- oder Nutzungsrecht an dieser Datei besitzt. Am besten sind Bildabmessungen von 2.280 mal 500 Pixel.',
                ]
            );
    }

    public function getName(): string
    {
        return 'city';
    }
}
