<?php

namespace Caldera\CriticalmassPlusBundle\Utility;

use Caldera\CriticalmassPlusBundle\Entity\VoucherCode;
use Caldera\CriticalmassPlusBundle\Entity\VoucherClass;

class VoucherCodeGenerator {
    protected $voucherClass;
    protected $doctrine;

    public function __construct(VoucherClass $voucherClass, $doctrine)
    {
        $this->voucherClass = $voucherClass;
        $this->doctrine = $doctrine;
    }

    public function execute($number)
    {
        while ($number > 0)
        {
            $code = $this->voucherClass->getCodePrefix();

            while (strlen($code) < 12)
            {
                $code .= chr(rand(65, 90));
            }

            $voucherCode = new VoucherCode();
            $voucherCode->setVoucherClass($this->voucherClass);
            $voucherCode->setCode($code);

            $em = $this->doctrine->getManager();
            $em->persist($voucherCode);
            $em->flush();
            --$number;
        }
    }
}