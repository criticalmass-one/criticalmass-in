<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\Query\BoundingBoxQuery;
use Symfony\Component\HttpFoundation\Request;

class QueryFactory
{
    public function createFromRequest(Request $request): array
    {
        $queryList = [];

        if ($request->query->get('bbWestLongitude') && $request->query->get('bbEastLongitude') && $request->query->get('bbNorthLatitude') && $request->query->get('bbSouthLatitude')) {
            $westLongitude = (float)$request->query->get('bbWestLongitude');
            $eastLongitude = (float)$request->query->get('bbEastLongitude');
            $northLatitude = (float)$request->query->get('bbNorthLatitude');
            $southLatitude = (float)$request->query->get('bbSouthLatitude');

            $queryList[] = new BoundingBoxQuery($northLatitude, $southLatitude, $eastLongitude, $westLongitude);
        }
        
        return $queryList;
    }
}
