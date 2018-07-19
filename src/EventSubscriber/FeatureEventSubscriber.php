<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\Feature\Annotation\Feature;
use App\Criticalmass\Feature\FeatureManager\FeatureManagerInterface;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FeatureEventSubscriber implements EventSubscriberInterface
{
    /** @var Reader $annotationReader */
    protected $annotationReader;

    /** @var FeatureManagerInterface $featureManager */
    protected $featureManager;

    public function __construct(Reader $annotationReader, FeatureManagerInterface $featureManager)
    {
        $this->annotationReader = $annotationReader;
        $this->featureManager = $featureManager;
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }

    public function onKernelController(FilterControllerEvent $event): void
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            return;
        }

        if (strpos(get_class($controller[0]), 'App\\Controller\\') !== 0) {
            return;
        }

        if ($requiredFeature = $this->findRequiredFeature($event->getRequest()->get('_controller'))) {
            if (!$this->featureManager->isFeatureEnabled($requiredFeature)) {
                throw new AccessDeniedException();
            }
        }
    }

    protected function findRequiredFeature(string $requestPath): ?string
    {
        list($controllerClass, $actionMethod) = explode('::', $requestPath);

        $reflectionMethod = new \ReflectionMethod($controllerClass, $actionMethod);

        $featureAnnotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, Feature::class);

        if ($featureAnnotation) {
            return $featureAnnotation->getName();
        }

        return null;
    }
}
