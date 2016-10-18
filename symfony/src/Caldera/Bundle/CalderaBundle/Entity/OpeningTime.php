<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="opening_time")
 * @ORM\Entity()
 */
class OpeningTime
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $weekday;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $openDateTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $closeDateTime;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): integer
    {
        return $this->id;
    }

    /**
     * Get weekday
     *
     * @return integer
     */
    public function getWeekday(): integer
    {
        return $this->weekday;
    }

    /**
     * Set weekday
     *
     * @param int $weekday
     * @return OpeningTime
     */
    public function setWeekday(int $weekday): OpeningTime
    {
        $this->weekday = $weekday;

        return $this;
    }

    /**
     * Get openDateTime
     *
     * @return \DateTime
     */
    public function getOpenDateTime(): \DateTime
    {
        return $this->openDateTime;
    }

    /**
     * Set openDateTime
     *
     * @param \DateTime $openDateTime
     * @return OpeningTime
     */
    public function setOpenDateTime($openDateTime): OpeningTime
    {
        $this->openDateTime = $openDateTime;

        return $this;
    }

    /**
     * Get closeDateTime
     *
     * @return \DateTime
     */
    public function getCloseDateTime(): \DateTime
    {
        return $this->closeDateTime;
    }

    /**
     * Set closeDateTime
     *
     * @param \DateTime $closeDateTime
     * @return OpeningTime
     */
    public function setCloseDateTime($closeDateTime): OpeningTime
    {
        $this->closeDateTime = $closeDateTime;

        return $this;
    }
}
