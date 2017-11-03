<?php

namespace Criticalmass\Bundle\AppBundle\Form\Type;

use AppBundle\Entity\CityCycle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['flow_step']) {
            case 1:
                $builder
                    ->add('city')
                    ->add('latitude', HiddenType::class)
                    ->add('longitude', HiddenType::class)
                ;

                break;

            case 2:
                $builder
                    ->add('title')
                    ->add('description')
                    ->add('punchLine')
                    ->add('longDescription')
                    ->add('cityPopulation')
                ;

                break;

            case 3:
                $builder
                    ->add('url')
                    ->add('facebook')
                    ->add('twitter');

                break;

            case 4:
                $builder
                    ->add('enableBoard')
                    ->add('timezone', TimezoneType::class);

                break;
            /*
                        case 6:
                            $builder
                                ->add('imageFile', 'vich_file', array('required' => false))
                            ;
            
                            break;
            */
        }
    }

    public function getBlockPrefix()
    {
        return 'city';
    }
}
