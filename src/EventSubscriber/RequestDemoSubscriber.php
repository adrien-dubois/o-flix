<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestDemoSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $server = $request->server;

        // Si l'adresse IP est dans une blacklist, on peut afficher un message d'erreur
        if($server->get('REMOTE_ADDR') === "189.121.12.3") {
            dd("Vous ne passerez pas");
        }

        dump($request->server->get('REMOTE_ADDR'));
    }

    public static function getSubscribedEvents()
    {
        // si l'event kernel.request est déclenché, Symfony va prévenir la classe RequestDemoSubscriber et appeler la méthode onKernelRequest
        return [
            'kernel.request' => ['onKernelRequest',],
        ];
    }
}
