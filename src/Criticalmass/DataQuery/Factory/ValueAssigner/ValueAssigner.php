<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ValueAssigner;

use App\Criticalmass\DataQuery\Exception\ParameterConverterException;
use App\Criticalmass\DataQuery\Factory\ParamConverterFactory\ParamConverterFactoryInterface;
use App\Criticalmass\DataQuery\FieldList\ParameterFieldList\ParameterField;
use App\Criticalmass\DataQuery\FieldList\QueryFieldList\QueryField;
use App\Criticalmass\DataQuery\Parameter\ParameterInterface;
use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;
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

    public function assignQueryPropertyValueFromRequest(RequestParameterList $requestParameterList, QueryInterface $query, QueryField $queryField): QueryInterface
    {
        if (!$requestParameterList->has($queryField->getParameterName())) {
            return $query;
        }

        $methodName = $queryField->getMethodName();
        $value = $requestParameterList->get($queryField->getParameterName());
        $type = $queryField->getType();

        switch ($type) {
            case 'float':
                $query->$methodName((float)$value);
                break;

            case 'int':
                $value = $this->convertToInt($value, $queryField->getParameterName());
                $query->$methodName($value);
                break;

            case 'string':
                $query->$methodName((string)$value);
                break;

            case 'mixed':
                $query->$methodName($value);
                break;

            default:
                $query = $this->assignEntityValueFromParamConverter($requestParameterList, $query, $queryField);
                break;
        }

        return $query;
    }

    public function assignParameterPropertyValueFromRequest(RequestParameterList $requestParameterList, ParameterInterface $parameter, ParameterField $parameterField): ParameterInterface
    {
        if (!$parameterField->hasParameterName() || !$requestParameterList->has($parameterField->getParameterName())) {
            return $parameter;
        }

        $methodName = $parameterField->getMethodName();
        $value = $requestParameterList->get($parameterField->getParameterName());
        $type = $parameterField->getType();

        switch ($type) {
            case 'float':
                $parameter->$methodName((float)$value);
                break;

            case 'int':
                $value = $this->convertToInt($value, $parameterField->getParameterName());
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

    protected function assignEntityValueFromParamConverter(RequestParameterList $requestParameterList, QueryInterface $query, QueryField $queryField): QueryInterface
    {
        if ($converter = $this->paramConverterFactory->createParamConverter($queryField->getType())) {
            $methodName = $queryField->getMethodName();
            $newParameterName = ClassUtil::getLowercaseShortnameFromFqcn($queryField->getType());

            $paramConverterConfiguration = new ParamConverter(['name' => $newParameterName]);

            $request = new Request($requestParameterList->getList());

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
