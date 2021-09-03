<?php

namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestDemoSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $server = $request->server;
        $remoteIp = $server->get('REMOTE_ADDR');

        // Si l'adresse IP est dans une blacklist, on peut afficher un message d'erreur
        if(in_array( $remoteIp ,['189.121.12.3', '125.12.3.6'])) {
            $response = new Response('<h1> Vous ne passerez pas </h1>', 403);
            $event->setResponse($response);
            
        }
        // Sinon Symfony poursuit les étapes dans son ordre.
    }

    public static function getSubscribedEvents()
    {
        // si l'event kernel.request est déclenché, Symfony va prévenir la classe RequestDemoSubscriber et appeler la méthode onKernelRequest
        return [
            'kernel.request' => ['onKernelRequest',],
        ];
    }
}
