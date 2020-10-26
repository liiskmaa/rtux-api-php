<?php declare(strict_types=1);
namespace Boxalino\RealTimeUserExperienceApi\Service\Api;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ApiCookieSubscriber
 * Sets the API cookies for tracking and content personalization
 *
 * @package Boxalino\RealTimeUserExperienceApi\Service\Api
 */
class ApiCookieSubscriber implements EventSubscriberInterface
{
    const BOXALINO_API_COOKIE_SESSION = "cems";
    const BOXALINO_API_COOKIE_VISITOR = "cemv";

    const BOXALINO_API_INIT_SESSION = "cems-init";
    const BOXALINO_API_INIT_VISITOR = "cemv-init";

    const VISITOR_COOKIE_TIME = 31536000;

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                ['setApiCookie', -2500],
            ],
        ];
    }

    /**
     * @param ResponseEvent $event
     */
    public function setApiCookie(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if($request->cookies->has(self::BOXALINO_API_INIT_SESSION))
        {
            $response->headers->setCookie(Cookie::create(self::BOXALINO_API_COOKIE_SESSION,
                $request->cookies->get(self::BOXALINO_API_INIT_SESSION), 0,
                "/",  null,false, false)
            );
            $response->headers->removeCookie(self::BOXALINO_API_INIT_SESSION);
        }

        if($request->cookies->has(self::BOXALINO_API_INIT_VISITOR))
        {
            $response->headers->setCookie(Cookie::create(self::BOXALINO_API_COOKIE_VISITOR,
                $request->cookies->get(self::BOXALINO_API_INIT_VISITOR), time() + self::VISITOR_COOKIE_TIME,
                "/", null, false, false )
            );
            $response->headers->removeCookie(self::BOXALINO_API_INIT_VISITOR);
        }
    }

}
