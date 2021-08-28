# Notes Login / Register :

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

## Twig

Pour afficher des infos si connecté :
`{% if app.user %}` *Par exemple pour afficher le prénom du User connecté :`{{app.user.firstname}}`*

Pour afficher des informations selon le rôle, il faut utiliser la condition :
`{% if is_granted("ROLE_ADMIN") %}` 