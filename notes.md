# Events

Dans le cycle d'une requête, Symfo passe par plusieurs étapes apellées `évènements`. 
A chaque étape, Symfo envoie une notification, il préviens toutes les personnes qui se sont abonnées, les `subscribers`, à l'évènement.

Il existe 3 events Symfony :

- Kernel events : kernel.request, kernelRequest....
- Doctrine events : prePersist, postPersist.....
- Form events : preSetData, preSubmit, postSubmit....

## Kernel Events

>> DOC : https://symfony.com/doc/current/event_dispatcher.html

Pour s'abonner à un event du kernel on lance la commande :

`php bin/console make:subscriber`

- On lui choisis un nom *par exemple `RequestDemoSubscriber`* 
- Ensuite on le relis à un event *ici : `Kernel.request`*
  
Deux fichiers viennent d'être créées : 
- `\src\Kernel.php`
- `\src\EventSubscriber\nom_choisi.php`
  
si l'event `kernel.request` est déclenché, Symfony va prévenir la classe RequestDemoSubscriber, que l'on viens de créer et appeler la méthode onKernelRequest qui a été créée dans cette class.

On peut BlackLister une adresse IP, pour empêcher l'accès via :
*ici l'adresse `189.121.12.3` est dans la BlackList* 

```php
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
```

Si l'on veut changer la priorité d'un événement il faut s'en occuper dans la méthode `getSubscribedEvents` , ici 3000 :

```php
public static function getSubscribedEvents()
{
return [
    'kernel.request' => ['onKernelRequest', 3000],
];
}
```