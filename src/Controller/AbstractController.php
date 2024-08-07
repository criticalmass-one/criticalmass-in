<?php declare(strict_types=1);

namespace App\Controller;

use App\Traits\RepositoryTrait;
use App\Traits\UtilTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as AbstractFrameworkController;

abstract class AbstractController extends AbstractFrameworkController
{
    use RepositoryTrait;
    use UtilTrait;
}
