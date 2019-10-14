<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class RequestSubscriber
 *
 * @author Sjors Keuninkx <sjors.keuninkx@gmail.com>
 */
class RequestSubscriber implements EventSubscriberInterface
{
    use TargetPathTrait;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest'],
        ];
    }

    /**
     * Handle actions on kernel request
     * Saving last visited url in session to reload directly after login
     *
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest() || $request->isXmlHttpRequest()) {
            return;
        }

        $this->saveTargetPath($this->session, 'main', $request->getUri());
    }
}