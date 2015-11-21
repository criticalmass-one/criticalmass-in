<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Strava\API\OAuth;
use Strava\API\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackImportController extends AbstractController
{
    public function stravaAction(Request $request, $code = null)
    {
        try {
            $options = array(
                'clientId'     => 1234,
                'clientSecret' => 'APP-TOKEN',
                'redirectUri'  => 'http://my-app/callback.php'
            );
            $oauth = new OAuth($options);

            if (!isset($_GET['code'])) {
                return new Response('<a href="'.$oauth->getAuthorizationUrl().'">connect</a>');
            } else {
                $token = $oauth->getAccessToken('authorization_code', array(
                    'code' => $_GET['code']
                ));
                print $token;
            }
        } catch(Exception $e) {
            print $e->getMessage();
        }
    }
}
