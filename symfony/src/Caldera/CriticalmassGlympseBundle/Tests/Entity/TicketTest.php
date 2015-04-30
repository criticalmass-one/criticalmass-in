<?php

namespace Caldera\CriticalmassGlympseBundle\Tests\Entity;

use Caldera\CriticalmassCoreBundle\Entity\Ride;
use Caldera\CriticalmassGlympseBundle\Entity\Ticket;
use PHPUnit_Framework_TestCase;

class TicketTest extends PHPUnit_Framework_TestCase {

    public function testNoStandardableRide()
    {
        $ride1 = new Ride();
        $ride1->setDateTime(new \DateTime('2015-01-01 19:00:00'));

        $ride2 = new Ride();
        $ride2->setDateTime(new \DateTime('2015-01-02 19:00:00'));

        $ticket = new Ticket();
        $ticket->setCreationDateTime(new \DateTime('2015-01-01 18:31:14'));

        $this->assertTrue($ticket->belongsToRide($ride1));
        $this->assertFalse($ticket->belongsToRide($ride2));
    }
} 