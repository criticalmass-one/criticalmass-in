<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Entity\Ride;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\ResolverInterface;

class RideResolver extends AbstractResolver implements ResolverInterface, AliasedInterface
{
    public function resolve(Argument $args): Ride
    {
        $ride = $this->registry->getRepository(Ride::class)->find($args['id']);

        return $ride;
    }

    public static function getAliases(): array
    {
        return [
            'resolve' => 'Ride'
        ];
    }
}
