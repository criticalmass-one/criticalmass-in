<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\RideEstimate;
use AppBundle\Model\CreateEstimateModel;
use AppBundle\Traits\RepositoryTrait;
use AppBundle\Traits\UtilTrait;
use JMS\Serializer\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\User\UserInterface;

class EstimateController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="This is a description of your API method"
     * )
     */
    public function createAction(Request $request, UserInterface $user): Response
    {
        $estimateModel = $this->deserializeRequest($request, CreateEstimateModel::class);

        $rideEstimation = $this->createRideEstimate($estimateModel);

        return new Response();
    }

    protected function createRideEstimate(CreateEstimateModel $model): RideEstimate
    {
        $estimate = new RideEstimate();

        $estimate
            ->setEstimatedParticipants($model->getEstimation())
            ->setCreationDateTime($model->getDateTime())
        ;

        return $estimate;
    }
}
