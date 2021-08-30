# Notes Login / Register :

## Make CRUD User

Donc commencer par `php bin/console make:crud` pour `Backoffice\User`

Le truc c'est que le CRUD ne va pas inclure le hash des mots de passe. Attention penser que le CRUD automatique va générer les mêmes erreurs de slashs sur les routes, que précédemment, donc penser à les modifier.

Créer aussi le formulaire de user : `php bin/console make:form User`

Modifier le UserType, en ajoutant déjà les firstname et lastname :

```
->add('firstname')
->add('lastname')
```

Modifier le password par plainPassword, et ajouter`mapped=>false` dans les options, de cette manière :

```
->add('plainPassword', PasswordType::class, [
    'mapped' => false,
])
```
`Mapped -> false` dit à Symfony que plainPassword n'est pas présent dans Entity pour qu'il ne le cherche pas par erreur.

Ensuite modifier le controller pour récupérer le mot de passe et le hasher.
Donc dans UserController, sur la méthode New :

Ajouter dans les arguments de la méthode : `UserPasswordHasherInterface $userPasswordHasherInterface`

Et donc sous le `if($form->isSubmitted....etc`
Ajouter : 
```
$user->setPassword(
    $userPasswordHasherInterface->hashPassword(
        $user,
        $form->get('plainPassword')->getData()
    )
);
```

On peut aussi ajouter ça à la méthode edit.

## Enregistrement

### - Verification Mot de passe

Après création du formulaire d'inscription, on peut déjà commencer par rajouter des champs qui nous seront utiles, rajoutés à l'Entity User, comme Firstname ou Lastname

Pour ajouter la verification du mot de passe en 2 fois, sur le champ password, remplacer le `PasswordType` par `RepeatedType`, et juste en dessous, rajouter ces lignes *(entre cette première ligne et les options déjà typées)* :
```
'type' => PasswordType::class,
'invalid_message' => 'Les mots de passe doivent être identiques',
'options' => ['attr' => ['class' => 'password-field','placeholder'=>'Mot de passe']],
'required' => true,
'first_options'  => ['label' => 'Mot de passe (6 caractères minimum)'],
'second_options' => ['label' => 'Confirmez votre mot de passe'],
```

### - Connexion automatique après register

Dans le RegistrationController, commencer par remplacer la méthode d'encodage du mot de passe, il est `"deprecated"`, par `UserPasswordHasherInterface` et ajouter à suivre la variable correspondante. Dans la méthode, remplacer l'ancienne variable par la nouvelle, et la fonction `encodePassword` par `hashPassword`

Ensuite, rajouter en argument de la même méthode l'autheticator, selon le nom que vous lui avez donné ainsi que sa variable, pour l'exemple ici : `UserAuthenticatorInterface $authenticator` 
Ainsi que le formAuthanticator : `LoginFormAuthenticator $formAuthenticator ` 

Et donc après l'enregistrement du nouvel User ( après Persist / Flush, addFlash si flash il y a), au return, effacer la Redirection, et remplacer par : 

`return $authenticator->authenticateUser($user, $formAuthenticator, $request);`

Il vous faudra donc utiliser ce que nous venons de rentrer en argument de la méthode pour utiliser cette fonction 

### - Redirection après logout

Dans le `config/packages/security.yaml` à `firewalls : main : logout` décommentez `target` puis mettez la route où vous souhaitez rediriger, pour ma part le formulaire de connexion

## Rôles 

### `security.yaml`

Dans le `security.yaml` à la ligne `access_control` on peut décommenter les lignes avec les regex de routes de rôles.
Il faut commencer par le rôle le plus important à sécuriser, en mettant la route autorisée par ce rôle, suivi du nom du rôle. Par exemple si on a des rôles Super Admin/Admin/User, on va fonctionner de cette façon :

```
access_control:
        - { path: ^/backoffice/user, roles: ROLE_SUPER_ADMIN }
        - { path: ^/backoffice, roles: ROLE_ADMIN }
        - { path: ^/profile, roles: ROLE_USER }
```

Et ensuite on va définir la hiérarchie des rôles. On commence par noter le rôle à qui on souhaite en attribuer un autre. Le rôle définit pour avoir les mêmes droits que le rôle attribué. Si on attribut ensuite un rôle qui est déjà hierarchisé, le rôle qui récupère cette attribution aura donc le rôle attribué, ainsi que le rôle qui était déjà attribué à ce dernier. Pour que ce soit plus parlant :

```
    role_hierarchy:
        ROLE_ADMIN: ROLE_USER
        ROLE_SUPER_ADMIN: ROLE_ADMIN
```

Ici le role ROLE_ADMIN pourra avoir les mêmes droits que ROLE_USER. Et en fonctionnant en cascade, on peut en déduire que ROLE_SUPER_ADMIN, aura les mêmes droits que ROLE_ADMIN, donc aussi ceux de ROLE_USER

### Controller

Dans le controller on peut commencer par sécuriser les routes avec l'annotation 

```
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
public function helloAction($name)
{
```

On peut également sécuriser une classe complète pour bloquer la route parente.

Mais on peut aussi sécuriser juste la partie d'une méthode, pour ne pas aller plus loin si le rôle ne le permets pas : 

```
public function helloAction($name)
{
// The second parameter is used to specify on what object the role is tested.
$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Unable to access this page!');
```


### Twig

Pour afficher des infos si connecté :
`{% if app.user %}` *Par exemple pour afficher le prénom du User connecté :`{{app.user.firstname}}`*

Pour afficher des informations selon le rôle du user connecté, il faut utiliser la condition :
`{% if is_granted("ROLE_ADMIN") %}` 

Si on souhaite savoir si un utilisateur s’est connecté avec succès :

```
{% if is_granted('IS_AUTHENTICATED_FULLY') %}
<p>Utilisateur: {{ app.user.firstname }}</p>
{% endif %}
```


## Création mail de confirmation d'inscription

Pour commencer, nous allons modifier notre entité "Users" pour y ajouter la propriété "activation_token", champ de type "string" et pouvant être vide.

On effectue la migration, puis on va modifier le ResgistrationController en y ajoutant cette ligne pour générer le token : `$user->setActivationToken(md5(uniqid()));`
Ligne que l'on ajoute juste après le hash du mot de passe. Maintenant, nous allons avoir un token automatiquement généré à chaque inscription.

On va ensuite ajouter une méthode dans le fichier RegistrationController. Cette méthode pointera vers une route du type "/activation/{token}"

Le lien dans l'e-mail devra diriger vers cette route qui vérifiera si le token existe et ensuite valider le compte correspondant.

```
    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $users)
    {
        // We search in the DB if an user have this token
        $user = $users->findOneBy(['activation_token' => $token]);

        // If no one is associate with it
        if(!$user){
            // We send a 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        // And delete token
        $user->setActivationToken('');
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        
        $this->addFlash(
            'success',
            'Utilisateur activé avec succès'
        );

        //  And redirect to home
        return $this->redirectToRoute('home');
    }
```

Notre route étant créée, nous allons préparer l'e-mail à envoyer à l'utilisateur. Celui-ci devra contenir le lien d'activation.

Nous devrons créer le fichier twig et l'envoi depuis le RegistrationController, Twig que l'on va créer dans `templates/emails`, et qu'on appellera `activation.twig.html`

```
<h1>Activation de votre compte</p>
<p>Vous avez créé un compte sur notre site, veuillez cliquer sur le lien ci-dessous pour l'activer</p>
<p><a href="{{ absolute_url(path('activation', {'token': token})) }}">Activer mon compte</a></p>
```

Pour l'envoi du mail, d'abord installer SwiftMailer avec `composer require symfony/swiftmailer-bundle`

Nous allons ensuite modifier les paramètres de la méthode "register" de notre contrôleur en rajoutant d'abord en argument de la méthode `\Swift_Mailer $mailer` :

Et ensuite sous le Flash message, là où il y a le commentaire concernant l'envoi de mail *(// do anything else you need here, like send an email)* :

```
// do anything else you need here, like send an email
// We create message
$message = (new \Swift_Message('Nouveau compte'))
// Set the expeditor
->setFrom('votre@adresse.fr')
// set the client
->setTo($user->getEmail())
// Create the text with the view
->setBody(
$this->renderView(
        'emails/activation.html.twig', ['token' => $user->getActivationToken()]
),
'text/html'
)
;
$mailer->send($message);
```

