<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\CityList;

class ParticipationCityList implements \Countable
{
    /** @var array $list */
    protected $list = [];

    public function addCityItem(ParticipationCityListItem $participationCityListItem): ParticipationCityList
    {
        $key = $participationCityListItem->getCity()->getId();

        if (array_key_exists($key, $this->list)) {
            $this->list[$key]->incCounter();
        } else {
            $this->list[$key] = $participationCityListItem;
        }

        return $this;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function setList(array $list): ParticipationCityList
    {
        $this->list = $list;

        return $this;
    }

    public function count(): int
    {
        return count($this->list);
    }
}
