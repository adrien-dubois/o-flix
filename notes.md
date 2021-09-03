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
- Ensuite on le relis à un event *ici nous choisissons : `Kernel.request`*
  
Deux fichiers viennent d'être créées : 
- `\src\Kernel.php`
- `\src\EventSubscriber\RequestDemoSubscriber.php`
  
### La page de class subscriber *RequestDemoSubscriber.php*
  
Si l'event `kernel.request` est déclenché, Symfony va prévenir la classe RequestDemoSubscriber, que l'on viens de créer et appeler la méthode onKernelRequest qui a été créée dans cette class.

On peut BlackLister une adresse IP, pour empêcher l'accès via :
*ici l'adresse `189.121.12.3` est dans la BlackList* 

```php
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
```

Si l'on veut changer la priorité d'un événement il faut s'en occuper dans la méthode `getSubscribedEvents` , ici 3000 sur notre event `kernel.request`:

```php
public static function getSubscribedEvents()
{
return [
    'kernel.request' => ['onKernelRequest', 3000],
];
}
```

## Form Events

>> DOC : https://symfony.com/doc/current/form/events.html

On peut demander à Symfony de se positionner avant ou après le préremplissage du formulaire.
Symfony va découper chaque sous étapes de la création d'un formulaire ( création, récupération de données, préremplissage, affichage, submit), et va nous permettre de nous positionner à chaque sous étape.

Par exemple pour le formulaire de création des users, on voudrait empêcher d'afficher le champ Password quand on est en mode édition

On va commencer par commenter le champ Password du UserType

On va se brancher à un événement PRE_SET_DATA pour afficher le champ plainPassword en fonction du contexte dans lequel on se trouve :
- A la création : on affiche le champs
- A l'édition : on ne l'affiche pas
  
On va ajouter un listener au builder du formulaire donc sur le PRE_SET_DATA, event qui se trouve juste avant le remplissage du formulaire :

`$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event`

On récupère les données de l'utilisateur que l'on s'apprête à créer ou éditer

```php
$user = $event->getData();
$form = $event->getForm();
```

`$user` récupère les données existantes de l'édition
`$form` récupère le formulaire vide

On va effectuer un test sur l'ID du User, et si l'ID est égal à null, donc nous sommes sur une création de formulaire, puisque l'ID n'existe pas, alors on va créer notre champ password, ou enfin on peut récupérer le champ que l'on a commenté au préalable, grâce à `$form->` et on ajoute le champ plainPassword.

Donc voilà ce que va donner cette partie là au complet **_Attention mon champ plainPassword est un peu spécial car j'effectue une double vérification du password, quand on doit taper son MDP 2X afin de vérifier que les MDP sont identiques, mais vous pouvez faire de même avec votre propre champ password, ou aussi bien tester cette manière :)_**

```php
$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
    // On récupère les données de l'utilisateur que l'on s'apprête à créer ou éditer
    $user = $event->getData();
    $form = $event->getForm();

    // Si nous sommes dans le cas d'une création de compte utilisateur, alors on ajoute le champs du mote de passe
    if($user->getId() === null){
        $form->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent être identiques',
            'options' => ['attr' => ['class' => 'password-field','placeholder'=>'Mot de passe']],
            'required' => true,
            'first_options'  => ['label' => 'Mot de passe (6 caractères minimum)'],
            'second_options' => ['label' => 'Confirmez votre mot de passe'],
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password',],
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un mot de passe',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ]);
    }

    });
```

## Doctrine Events

Avec Doctrine on va pouvoir effectuer des callbacks qui seront définis dans une méthode publique directement dans l'Entity que l'on souhaite modifier.

Ici par exemple Category, on veux que createdAt soit mis à jour directement à la date/heure du jour, mais seulement après l'édition.

On va commencer par mettre une annotation de la class de l'entity Category pour définir qu'il y aura une action de Callback :
`@ORM\HasLifecycleCallbacks()` 
Ce qui donnera :

```php
/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Category
```

Puis on va donc créer comme dit au dessus, une méthode publique, que l'on appelera `setupdatedAtValue` , et on ajoutera ensuite en annotation où l'on souhaite se placer, donc ici, juste avant l'action de modification, c'est à dire `@ORM\PreUpdate`

Et enfin dans cette méthode, au même titre que createdAt dans la méthode `__construct`, nous allons mettre un new DatetimeImmutable, en séléctionnant bien sûr createdAt. 
Ce qui donnera cette fois :

```php
/**
 * @ORM\PreUpdate
 * 
 * Cette méthode est appelée lorsque l'event preUpdate est déclenché
 *
 */
public function setUpdateAtValue()
{
    $this->updatedAt = new DateTimeImmutable();
}
```