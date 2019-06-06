<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities\TwigExtension;

use App\Criticalmass\OrderedEntities\OrderedEntityInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OrderedEntityExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('previous_entity', [$this, 'previousEntity'], ['is_safe' => ['html']]),
            new TwigFunction('next_entity', [$this, 'nextEntity'], ['is_safe' => ['html']]),
        ];
    }

    public function previousEntity(OrderedEntityInterface $entity): OrderedEntityInterface
    {
        return $entity;
    }


    public function nextEntity(OrderedEntityInterface $entity): OrderedEntityInterface
    {
        return $entity;
    }

    public function getName(): string
    {
        return 'ordered_entity_extension';
    }
}