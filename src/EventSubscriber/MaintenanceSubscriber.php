<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $content = $response->getContent();
        dump($event);
        $body = str_replace(
            "</nav>", 
            $_ENV['BONUS_MESSAGE'], 
            $content
        );
        $response->setContent($body);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
