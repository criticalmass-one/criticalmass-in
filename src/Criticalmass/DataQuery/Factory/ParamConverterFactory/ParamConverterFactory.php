<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Factory\ParamConverterFactory;

use App\Criticalmass\Util\ClassUtil;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Doctrine\Persistence\ManagerRegistry;

class ParamConverterFactory implements ParamConverterFactoryInterface
{
    const PARAMCONVERTER_NAMESPACE = 'App\\Request\\ParamConverter\\';
    const PARAMCONVERTER_SUFFIX = 'ParamConverter';

    /** @var ManagerRegistry $registry */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function createParamConverter(string $fqcn): ?ParamConverterInterface
    {
        $entityShortname = ClassUtil::getShortnameFromFqcn($fqcn);
        $paramConverterFqcn = sprintf('%s%s%s', self::PARAMCONVERTER_NAMESPACE, $entityShortname, self::PARAMCONVERTER_SUFFIX);

        if (!class_exists($paramConverterFqcn)) {
            return null;
        }

        return new $paramConverterFqcn($this->registry);
    }
}
