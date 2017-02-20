<?php

$baseUrl = 'http://www.yournavigation.org/api/1.0/gosmore.php?format=geojson&v=bicycle&fast=0&layer=mapnik';

$paramUrl = '&flat='.$_GET['flat'].'&flon='.$_GET['flon'].'&tlat='.$_GET['tlat'].'&tlon='.$_GET['tlon'];

$response = file_get_contents($baseUrl . $paramUrl);

echo $response;