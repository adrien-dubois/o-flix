<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $request = $event->getRequest();
        $server = $request->server;
        $maintenance = $server->get('MAINTENANCE_MODE');
        $maintenanceMessage = $server->get('MAINTENANCE_MESSAGE');
        // $server->set('maintenance_mode', 1);
        
        if ($maintenance == 1) {
            $response = $event->getResponse();
            $content = $response->getContent();

            $body = str_replace(
                '</nav>',
                $maintenanceMessage,
                $content
            );
            $newResponse = $response->setContent($body);
            $event->setResponse($newResponse);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
