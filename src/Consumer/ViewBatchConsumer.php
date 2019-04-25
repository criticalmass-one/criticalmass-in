<?php declare(strict_types=1);

namespace App\Consumer;

use App\Criticalmass\ViewStorage\ViewStoragePersisterInterface;
use OldSound\RabbitMqBundle\RabbitMq\BatchConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ViewBatchConsumer implements BatchConsumerInterface
{
    /** @var ViewStoragePersisterInterface $viewSotragePersister */
    protected $viewStoragePersister;

    public function __construct(ViewStoragePersisterInterface $viewStoragePersister)
    {
        $this->viewStoragePersister = $viewStoragePersister;
    }

    public function batchExecute(array $messages): array
    {
        $valueList = [];
        $resultList = [];

        /** @var AMQPMessage $message */
        foreach ($messages as $message) {
            $valueList[] = unserialize($message->getBody());

            $resultList[(int) $message->delivery_info['delivery_tag']] = true;
        }

        $this->viewStoragePersister->persistViews($valueList);

        return $resultList;
    }
}
