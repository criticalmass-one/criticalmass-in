<?php declare(strict_types=1);

use Oneup\FlysystemBundle\Tests\App\AppKernel;

$appKernel = new AppKernel('tests', false);
$appKernel->boot();

return $appKernel->getContainer();
