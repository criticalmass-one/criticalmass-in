<?php declare(strict_types=1);

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as AbstractFrameworkController;

abstract class AbstractController extends AbstractFrameworkController
{
    public function __construct(
        protected readonly ManagerRegistry $managerRegistry
    )
    {

    }
}
