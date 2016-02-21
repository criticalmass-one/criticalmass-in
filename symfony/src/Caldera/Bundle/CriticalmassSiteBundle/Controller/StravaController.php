<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Pest;
use Strava\API\Client;
use Symfony\Component\HttpFoundation\Request;
use Strava\API\OAuth;
use Strava\API\Service\REST;

class StravaController extends AbstractController
{
    public function authAction(Request $request)
    {
        try {
            $options = array(
                'clientId'     => $this->getParameter('strava.client_id'),
                'clientSecret' => $this->getParameter('strava.token'),
                'redirectUri'  => 'http://criticalmass.cm/app_dev.php/'
            );

            $oauth = new OAuth($options);

        } catch(Exception $e) {
            print $e->getMessage();
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Strava:auth.html.twig',
            [
                'authorizationUrl' => $oauth->getAuthorizationUrl()
            ]
        );
    }

    public function listridesAction(Request $request, $citySlug, $rideDate)
    {
// REST adapter (We use `Pest` in this project)
        $adapter = new Pest('https://www.strava.com/api/v3');
// Service to use (Service\Stub is also available for test purposes)
        $service = new REST($this->getParameter('strava.token'), $adapter);

// Receive the athlete!
        $client = new Client($service);

        $activities = $client->getAthleteActivities();

        print_r($activities);
    }
}
