<?php

namespace Caldera\Bundle\CriticalmassAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class RideAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Stadt', array('class' => 'col-md-6 col-lg-4', 'description' => 'Wähle hier die Stadt, in der diese Tour stattfindet.'))
                ->add('city', null, array('label' => 'Stadt', 'required' => true))
            ->end()
            ->with('Zusatzinformationen', array('class' => 'col-md-6 col-lg-4', 'description' => 'Optionale Zusatzangaben zur Tour. Werden diese Felder nicht ausgefüllt, wird als Titel „Critical Mass“ verwendet, eine Beschreibung wird nicht angezeigt.'))
                ->add('title', 'text', array('label' => 'Titel', 'required' => false))
                ->add('description', 'textarea', array('label' => 'Beschreibung', 'required' => false))
            ->end()
            ->with('Datum und Uhrzeit', array('class' => 'col-md-6 col-lg-4', 'description' => 'Lege hier das Datum und die Uhrzeit der Tour fest.'))
                ->add('hasTime', 'checkbox', array('label' => 'Uhrzeit anzeigen?', 'required' => true, 'help' => 'Bei Bedarf kann die Angabe der Uhrzeit unterdrückt werden, etwa wenn bereits das Datum, aber noch nicht die genaue Uhrzeit feststehen.'))
                ->add('dateTime', 'datetime', array('label' => 'Uhrzeit', 'required' => true, 'help' => 'Bezeichnet den Zeitpunkt, an dem sich die Teilnehmer am Treffpunkt einfinden sollen.'))
                ->add('expectedStartDateTime', 'datetime', array('label' => 'Ungefährer Startzeitpunkt', 'required' => true, 'help' => 'Ungefähre Uhrzeit des Zeitpunktes, an dem sich die Masse in Bewegung setzt. Ab diesem Zeitpunkt wird die Karte nicht mehr automatisch den Treffpunkt, sondern die Position der Teilnehmer anzeigen.'))
            ->end()
            ->with('Sichtbarkeit', array('class' => 'col-md-6 col-lg-4', 'description' => 'Da die meisten Touren automatisch generiert werden, legt dieser Sichtbarkeitsbereich fest, in welchem Zeitraum sie auf der Karte angezeigt werden. Normalerweise sollte eine Tour noch eine Woche nach ihrem Termin auf der Karte verbleiben, anschließend wird die jeweils nächste Tour angezeigt.'))
                ->add('visibleSince', 'datetime', array('label' => 'Sichtbar ab'))
                ->add('visibleUntil', 'datetime', array('label' => 'Sichtbar bis'))
            ->end()
            ->with('Treffpunkt', array('class' => 'col-md-6 col-lg-4', 'description' => 'Der Treffpunkt wird mit der Angabe des Längen- und Breitengrades festgestellt. Nutze <a href="http://itouchmap.com/latlong.html">iTouchMap</a>, um diese Werte zu ermitteln.'))
                ->add('hasLocation', 'checkbox', array('label' => 'Treffpunkt anzeigen?', 'required' => true, 'help' => 'Die Anzeige des Treffpunktes kann unterdrückt werden. In diesem Fall sind die folgenden Angaben optional.'))
                ->add('location', 'text', array('label' => 'Treffpunkt', 'required' => false, 'help' => 'Gib hier eine aussagekräftige Bezeichnung des Treffpunktes ein.'))
                ->add('latitude', 'text', array('label' => 'Breitengrad', 'required' => false))
                ->add('longitude', 'text', array('label' => 'Längengrad', 'required' => false))
                ->add('weatherForecast', 'text', array('label' => 'Wettervorhersage', 'required' => false))
            ->end()
            ->with('Social Media', array('class' => 'col-md-6 col-lg-4', 'description' => 'Verlinke hier auf soziale Netzwerke, auf denen sich die Teilnehmer über die Tour informieren können.'))
                ->add('url', 'text', array('label' => 'Link zu einer Webseite', 'required' => false))
                ->add('facebook', 'text', array('label' => 'Link zu einer Veranstaltung auf facebook', 'required' => false))
                ->add('twitter', 'text', array('label' => 'Link zu einem Tweet', 'required' => false))
            ->end()
            ->with('Statistik', array('class' => 'col-md-6 col-lg-4', 'description' => 'In einer späteren Version von criticalmass.in werden die Teilnehmer Schätzungen über die Touren abgeben können, deren Mittelwerte für statistische Werte einer Tour benutzt werden. Mit den folgenden Werten lassen sich die Schätzungen der Benutzer überschreiben. Bedenke, dass in den folgenden Eingabefeldern keine Angaben der Einheiten zulässig sind.'))
                ->add('estimatedParticipants', 'text', array('label' => 'ungefähre Teilnehmerzahl', 'required' => false, 'help' => 'Die Teilnehmerzahl wird ohne Punkt oder Komma eingegeben.'))
                ->add('estimatedDistance', 'text', array('label' => 'ungefähre Fahrstrecke', 'required' => false, 'help' => 'Die Fahrstrecke wird in Kilometern angegeben, beispielsweise 23,54 für 23 Kilometer und 540 Meter.'))
                ->add('estimatedDuration', 'text', array('label' => 'ungefähre Fahrtdauer', 'required' => false, 'help' => 'Die Fahrtdauer wird in Stunden angegeben, beispielsweise 2,25 für 2 Stunden und 15 Minuten'))
            ->end()
            ->with('Teaserbild', array('class' => 'col-md-6 col-lg-4'))
                ->add('file', 'file', array('required' => false))
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('city')
            ->add('location')
            ->add('dateTime')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('dateTime')
            ->addIdentifier('city')
            ->addIdentifier('hasLocation')
            ->addIdentifier('location')
            ->addIdentifier('hasTime')
        ;
    }
}