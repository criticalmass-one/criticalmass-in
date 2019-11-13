<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\QueryProperty\QueryProperty;
use App\Criticalmass\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class ValueAssigner implements ValueAssignerInterface
{
    /** @var ParamConverterFactoryInterface $paramConverterFactory */
    protected $paramConverterFactory;

    public function __construct(ParamConverterFactoryInterface $paramConverterFactory)
    {
        $this->paramConverterFactory = $paramConverterFactory;
    }

    public function assignPropertyValue(Request $request, QueryInterface $query, QueryProperty $property): QueryInterface
    {
        $methodName = $property->getMethodName();
        $parameter = $request->query->get($property->getParameterName());
        $type = $property->getType();

        switch ($type) {
            case 'float':
                $query->$methodName((float)$parameter);
                break;

            case 'int':
                $query->$methodName((int)$parameter);
                break;

            case 'string':
                $query->$methodName((string)$parameter);
                break;

            default:
                $query = $this->assignEntityValueFromParamConverter($request, $query, $property);
                break;
        }

        return $query;
    }

    protected function assignEntityValueFromParamConverter(Request $request, QueryInterface $query, QueryProperty $property): QueryInterface
    {
        if ($converter = $this->paramConverterFactory->createParamConverter($property->getType())) {
            $methodName = $property->getMethodName();
            $newParameterName = ClassUtil::getLowercaseShortnameFromFqcn($property->getType());

            $paramConverterConfiguration = new ParamConverter(['name' => $newParameterName]);

            $converter->apply($request, $paramConverterConfiguration);
            $query->$methodName($request->get($newParameterName));
        }

        return $query;
    }
}
