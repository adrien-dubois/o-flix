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
        // dump($content);
        $body = str_replace(
            "<body>", 
            '<body> 
            <div class="alert alert-danger alert-dismissible fade show mt-5 text-center" role="alert">
            <strong>Maintenance prévue!</strong> Le mardi 7 septembre à 17h00.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ', 
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
