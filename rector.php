<?php declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Symfony\Set\JMSSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src'
    ]);

    $rectorConfig->sets([
        JMSSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);

    $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');
    $rectorConfig->symfonyContainerPhp(__DIR__ . '/tests/symfony-container.php');
};
