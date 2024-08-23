<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\SeoPage\SeoPageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelEventSubscriber implements EventSubscriberInterface
{
    protected SeoPageInterface $seoPage;

    public function __construct(SeoPageInterface $seoPage)
    {
        $this->seoPage = $seoPage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
        ];
    }

    public function onController(ControllerEvent $controllerEvent): void
    {
        if (!$controllerEvent->isMainRequest()) {
            return;
        }

        $request = $controllerEvent->getRequest();
        $canonical = $this->generateCanonicalUrl($request);

        $this->seoPage->setCanonicalLink($canonical);
    }

    /* @todo this is the most stupid way to setup canonical, but it should do for now */
    protected function generateCanonicalUrl(Request $request): string
    {
        $canonical = $request->getUri();

        $canonical = str_replace([
            'http',
            'www.',
        ], [
            'https',
            '',
        ], $canonical);

        return $canonical;
    }
}
