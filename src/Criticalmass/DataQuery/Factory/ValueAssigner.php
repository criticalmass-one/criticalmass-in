<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory;

use App\Criticalmass\DataQuery\Query\QueryInterface;
use App\Criticalmass\DataQuery\QueryProperty\QueryProperty;
use App\Criticalmass\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class ValueAssigner implements ValueAssignerInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
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
                $query = $this->assignEntityValue($request, $query, $property);
                break;
        }

        return $query;
    }

    protected function assignEntityValue(Request $request, QueryInterface $query, QueryProperty $property): QueryInterface
    {
        if ($converter = $this->createParamConverter($property->getType())) {
            $methodName = $property->getMethodName();

            $paramConverterConfiguration = new ParamConverter(['name' => 'city']);

            $converter->apply($request, $paramConverterConfiguration);
            $query->$methodName($request->get('city'));
        }

        return $query;
    }

    protected function createParamConverter(string $fqcn): ?ParamConverterInterface
    {
        $entityShortname = ClassUtil::getShortnameFromFqcn($fqcn);
        $paramConverterNamespace = 'App\\Request\\ParamConverter\\';
        $paramConverterFqcn = sprintf('%s%sParamConverter', $paramConverterNamespace, $entityShortname);

        if (!class_exists($paramConverterFqcn)) {
            return null;
        }

        return new $paramConverterFqcn($this->registry);
    }
}
