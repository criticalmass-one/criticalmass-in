<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ValueAssigner;

use App\Criticalmass\DataQuery\Exception\ParameterConverterException;
use App\Criticalmass\DataQuery\Factory\ParamConverterFactory\ParamConverterFactoryInterface;
use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Property\ParameterProperty;
use App\Criticalmass\DataQuery\Property\QueryProperty;
use App\Criticalmass\DataQuery\Query\QueryInterface;
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

    public function assignQueryPropertyValue(Request $request, QueryInterface $query, QueryProperty $property): QueryInterface
    {
        $methodName = $property->getMethodName();
        $value = $request->query->get($property->getParameterName());
        $type = $property->getType();

        switch ($type) {
            case 'float':
                $query->$methodName((float)$value);
                break;

            case 'int':
                $value = $this->convertToInt($value, $property->getParameterName());
                $query->$methodName($value);
                break;

            case 'string':
                $query->$methodName((string)$value);
                break;

            case 'mixed':
                $query->$methodName($value);
                break;

            default:
                $query = $this->assignEntityValueFromParamConverter($request, $query, $property);
                break;
        }

        return $query;
    }

    public function assignParameterPropertyValue(Request $request, ParameterInterface $parameter, ParameterProperty $property): ParameterInterface
    {
        $methodName = $property->getMethodName();
        $value = $request->query->get($property->getParameterName());
        $type = $property->getType();

        switch ($type) {
            case 'float':
                $parameter->$methodName((float)$value);
                break;

            case 'int':
                $value = $this->convertToInt($value, $property->getParameterName());
                $parameter->$methodName($value);
                break;

            case 'string':
                $parameter->$methodName((string)$value);
                break;

            case 'mixed':
                $parameter->$methodName($value);
                break;
        }

        return $parameter;
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

    protected function convertToInt(string $stringValue, string $parameterValue): int
    {
        if (!ctype_digit($stringValue)) {
            throw new ParameterConverterException('int', $stringValue, $parameterValue);
        }

        return (int)$stringValue;
    }
}
