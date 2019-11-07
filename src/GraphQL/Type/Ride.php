<?php declare(strict_types=1);

namespace App\GraphQL\Type;

use Overblog\GraphQLBundle\Annotation as GQL;

/**
 * @GQL\Type(interfaces={"Ride"})
 * @GQL\Description("Ride")
 */
class Ride
{
    /**
     * @GQL\Field(type="Int", description="The id of the ride")
     */
    public $id;

    /**
     * @GQL\Field(type="String", description="The title of the ride")
     */
    public $title;
}