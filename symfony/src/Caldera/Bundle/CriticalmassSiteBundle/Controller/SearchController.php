<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    public function queryAction(Request $request) {
        $result = [
            'Hamburg',
            'Critical Mass Hamburg',
            'Critical Mass Hamburg (29. Januar 2016)'
        ];

        return new Response(
            json_encode($result),
            200,
            [
                'Content-Type' => 'text/json'
            ]
        );
    }
}
