<?php declare(strict_types=1);

namespace App\Criticalmass\UploadableDataHandler;

use Vich\UploaderBundle\Mapping\PropertyMappingFactory;

abstract class AbstractUploadableDataHandler implements UploadableDataHandlerInterface
{
    /** @var PropertyMappingFactory $propertyMappingFactory */
    protected $propertyMappingFactory;

    protected $propertyCallList = [
        'size' => 'filesize',
        'mimeType' => 'mime_content_type',
    ];

    public function __construct(PropertyMappingFactory $propertyMappingFactory)
    {
        $this->propertyMappingFactory = $propertyMappingFactory;
    }
}