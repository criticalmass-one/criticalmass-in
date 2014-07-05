<?php

namespace Caldera\CriticalmassCoreBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CityAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Einstellungen', array('class' => 'col-md-6'))
                ->add('enabled', 'checkbox', array('label' => 'Aktiv', 'required' => true))
                ->add('city', 'text', array('label' => 'Stadt', 'required' => true))
                ->add('title', 'text', array('label' => 'Bezeichnung', 'required' => true))
                ->add('description', 'textarea', array('label' => 'Beschreibung', 'required' => false))
            ->end()
            ->with('Social Media', array('class' => 'col-md-6'))
                ->add('url', 'text', array('label' => 'Webseite', 'required' => false))
                ->add('facebook', 'text', array('label' => 'facebook-Seite', 'required' => false))
                ->add('twitter', 'text', array('label' => 'Twitter-Konto', 'required' => false))
            ->end()
            ->with('Geografie', array('class' => 'col-md-6'))
                ->add('latitude', 'text', array('label' => 'Breitengrad', 'required' => true))
                ->add('longitude', 'text', array('label' => 'Längengrad', 'required' => true))
            ->end()
            ->with('Slugs', array('class' => 'col-md-6'))
                ->add('slugs', 'sonata_type_collection', array(), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable'  => 'position',
                'allow_delete' => true
            ))
            ->with('Standard-Tour', array('class' => 'col-md-6'))
                ->add('isStandardable', 'checkbox', array('label' => 'Standard-Werte verwenden', 'required' => true))
                ->add('standardDayOfWeek', 'choice', array('label' => 'Wochentag', 'choices' => array(1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Sonnabend', 0 => 'Sonntag'), 'required' => false))
                ->add('standardWeekOfMonth', 'choice', array('label' => 'Woche im Monat', 'choices' => array(1 => 'Erste Woche im Monat', 2 => 'Zweite Woche im Monat', 3 => 'Dritte Woche im Monat', 4 => 'Vierte Woche im Monat', 0 => 'Letzte Woche im Monat'), 'required' => false))
                ->add('standardTime', 'time', array('label' => 'Startzeit', 'required' => false))
                ->add('standardLocation', 'text', array('label' => 'Treffpunkt', 'required' => false))
                ->add('standardLatitude', 'text', array('label' => 'Breitengrad', 'required' => false))
                ->add('standardLongitude', 'text', array('label' => 'Längengrad', 'required' => false))
            ->end();
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('city')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('city')
            ->add('isStandardable')
        ;
    }
}