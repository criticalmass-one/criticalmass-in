<?php

namespace AppBundle\Controller\Api;

use AppBundle\Model\CreateEstimateModel;
use AppBundle\Traits\RepositoryTrait;
use AppBundle\Traits\UtilTrait;
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

        var_dump($estimateModel);

        return new Response();
    }
}
