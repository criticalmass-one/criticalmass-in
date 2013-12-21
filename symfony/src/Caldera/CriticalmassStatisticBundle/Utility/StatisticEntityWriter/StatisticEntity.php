<?php

namespace Caldera\CriticalmassStatisticBundle\Utility\StatisticEntityWriter;

interface StatisticEntity
{
    public function setRemoteAddr($remoteAddr);
    public function setRemoteHost($remoteHost);
    public function setReferer($referer);
    public function setQuery($query);
    public function setEnvironment($environment);
    public function setHost($host);
    public function setAgent($agent);
    public function setDateTime($dateTime);
    public function setUser($user = null);
    public function setCity($city = null);
}