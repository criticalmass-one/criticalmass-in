<?php

namespace Caldera\CriticalmassGlympseBundle\Tests\Entity;

use Caldera\CriticalmassTrackBundle\Entity\Track;
use PHPUnit_Framework_TestCase;
use Application\Sonata\UserBundle\Entity\User;

class TrackTest extends PHPUnit_Framework_TestCase {

    protected $user;
    protected $track1;
    protected $track2;
    protected $track3;
    protected $track4;
    protected $track5;

    public function setup()
    {
        $this->user = new User();

        $this->track1 = new Track();
        $this->track1->setStartDateTime(new \DateTime('2015-01-01 19:00:00'));
        $this->track1->setUser($this->user);
        $this->user->addTrack($this->track1);

        $this->track2 = new Track();
        $this->track2->setStartDateTime(new \DateTime('2015-01-02 18:00:00'));
        $this->track2->setUser($this->user);
        $this->user->addTrack($this->track2);

        $this->track3 = new Track();
        $this->track3->setStartDateTime(new \DateTime('2015-01-02 19:00:00'));
        $this->track3->setUser($this->user);
        $this->user->addTrack($this->track3);

        $this->track4 = new Track();
        $this->track4->setStartDateTime(new \DateTime('2015-01-02 20:00:00'));
        $this->track4->setUser($this->user);
        $this->user->addTrack($this->track4);

        $this->track5 = new Track();
        $this->track5->setStartDateTime(new \DateTime('2015-01-03 19:00:00'));
        $this->track5->setUser($this->user);
        $this->user->addTrack($this->track5);
    }

    public function testNextPreviousTracks()
    {
        $this->assertEquals($this->track4, $this->track3->getNextTrack());
        $this->assertEquals($this->track2, $this->track3->getPreviousTrack());
    }
} 