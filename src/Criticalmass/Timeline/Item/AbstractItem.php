<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\User;

abstract class AbstractItem implements ItemInterface
{
    protected string $uniqId;

    protected ?\DateTime $dateTime = null;

    protected ?User $user = null;

    protected string $tabName = 'standard';

    public function __construct()
    {
        $this->uniqId = uniqid();
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): AbstractItem
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getUniqId(): string
    {
        return $this->uniqId;
    }

    /**
     * Der Autor darf fehlen — und tut es meistens.
     *
     * 24.464 der 26.820 Touren stammen aus dem Tourengenerator und haben nie
     * jemanden gehabt, der sie angelegt hat; dazu kommen alte Staedte aus der
     * Fruehzeit und Inhalte geloeschter Konten. Die Eigenschaft und der Getter
     * waren immer nullbar, nur dieser Setter nicht: Er hat den Aufruf mit null
     * in einen TypeError verwandelt und damit die Timeline zerlegt, sobald man
     * weit genug zurueckblaetterte.
     */
    public function setUser(?User $user): AbstractItem
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getTabName(): string
    {
        return $this->tabName;
    }
}
