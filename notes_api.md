# API

## Endpoints :

TvShow : 

- Liste des séries `/api/v1/tvshows` - GET
- Une série par ID `/api/v1/tvshows/{id}` - GET
- Créer une série `api/v1/tvshows` - POST
- Mise à jour totale d'une série `api/v1/tvshows/{id}` - PUT
- Mise à jour partielle d'une série (uniquement le title par ex) `api/v1/tvshows/{id}` - PATCH
- Supprimer une série `api/v1/tvshows/{id}` - DELETE

Categories :

- Liste des catégories `/api/v1/categories`
- Catégorie par ID `/api/v1/categories/{id}`


## Liste des meilleurs pratiques pour une API RESTful

>>DOC : https://medium.com/@mwaysolutions/10-best-practices-for-better-restful-api-cbe81b06f291


**L'API Rest a certains codes :**

- Ne pas utiliser de verbes, des mots *Par exemple : /users/*
- Si on est au pluriel, on reste au pluriel. Si singulier, tout doit être au singulier, il est interdit de mixer
- Utiliser un CRUD et respecter le protocole HTTP : par exemple GET ne sers que à la lecture, pas de GET pour un formulaire, on ne modifie pas avec GET

    - GET : lire 

    - POST : créer 

    - PUT : mettre à jour toutes les propriétés

    - PATCH : met à jour partiellement 

    - DELETE : supprime 


- Accéder à des sous ressources, grâce à des sources mères
- HATEOAS : Hypermedia as the Engine of Application State. C'est un principe que les liens doivent être utilisés pour créer une meilleure navigation à travers l'API.
- Penser à versionner : *v1 / v2 etc*
- Gérer les erreurs avec les bons codes status HTTP dont voici la liste

```http
200 — OK — Eyerything is working
201 — OK — New resource has been created
204 — OK — The resource was successfully deleted
304 — Not Modified — The client can use cached data
400 — Bad Request — The request was invalid or cannot be served. The exact error should be explained in the error payload. E.g. „The JSON is not valid“
401 — Unauthorized — The request requires an user authentication
403 — Forbidden — The server understood the request, but is refusing it or the access is not allowed.
404 — Not found — There is no resource behind the URI.
422 — Unprocessable Entity — Should be used if the server cannot process the enitity, e.g. if an image cannot be formatted or mandatory fields are missing in the payload.
500 — Internal Server Error — API developers should avoid this error. If an error occurs in the global catch blog, the stracktrace should be logged and not returned as response.
```

On peut utiliser ce genre de code :

```json
{
  "errors": [
   {
    "userMessage": "Sorry, the requested resource does not exist",
    "internalMessage": "No car found in the database",
    "code": 34,
    "more info": "http://dev.mwaysolutions.com/blog/api/v1/errors/12345"
   }
  ]
}
```

- Autoriser le remplacement de la méthode HTTP

## Créer un nouveau controller

Sans template : 

```bash
php bin/console make:controller --no-template

 Choose a name for your controller class :
 > Api\V1\TvShow
 ```

### Pour récupérer toutes les séries

>>DOC : https://symfony.com/doc/current/components/serializer.html#attributes-groups

 Dans le controller, on va récupérer via le TvShowRepository toutes les séries


 ```php
    public function index(TvShowRepository $tvShowRepository): Response
{
    // We get the Shows in DB and return it in Json
    $TvShows = $tvShowRepository->findAll();

    return $this->json($TvShows);
}
```

Pour éviter de bugger à cause des éléments liés à l'Entity, on va aider le composant serialiser à transformer un tableau JSON, en lui les indiquant les propriétés à appeler dans les entity que l'ont veut récupérer.

**_Exemple_**
En allant dans Entity\TvShow :

Mettre en Use :

`use Symfony\Component\Serializer\Annotation\Groups;`

Et ensuite on ajoute en annotation sur chaque propriété que l'on souhaite récupérer 

*par exemple ici : ID / title/ synopsis / images / nbLikes / publishedAt / createdAt / updatedAt / slug*

Pour le moment pas encore de propriété qui font partie d'une entité étrangère (comme une relation ManytoMany par ex)

`@Groups({"tvshow_list", "tvshow_detail"})`

Retour au controller, et sur le `return $this->json`, nous allons rajouter en argument de sortie, la variable du findAll, le code du statut HTTP (Api Rest), un tableau (headers) vide, et ensuite un tableau de contexte avec d'un côté 'groups' qui permets de récupérer l'annotation dans l'entity et en face le tag du groupe voulu mis en annotation dans l'Entity. 
Ce qui donne concrètement :

```php
return $this->json($TvShows, 200, [], [
    'groups' => 'tvshow_list'
]);
```

Cette entrée au Serializer de transformer les objets en JSON, en allant chercher uniquement les propriétés taggées avec le nom tvshow_list

Les TvShows vont bien passer dans l'API mais sans les catégories, puisque nous n'avons pas encore ajouté de relations.

Donc pour ajouter les catégories, on va ajouter l'annotation `@Groups({"tvshow_list", "tvshow_detail"})` à Catégories dans l'entity TvShows

Et ensuite dans l'Entity Category, ajouter cette annotation sur Title et Id pour afficher les catégories ainsi que le use `use Symfony\Component\Serializer\Annotation\Groups;`

### Pour récupérer une série selon son ID

Donc même protocole avec une nouvelle méthode, mais qui viendras chercher la série voulue via un find($id)

```php
public function show(int $id, TvShowRepository $tvShowRepository)
{
    // We get a show by its ID
    $tvShow = $tvShowRepository->find($id);

    // If the show does not exists, we display a 404
    if (!$tvShow){
        return $this->json([
            'error' => 'La série TV ' .$id . 'n\'existe pas'
        ], 404
        );
    }

    // We return the result with the Json format
    return $this->json($tvShow, 200, [], [
        'groups' => 'tvshow_detail'
    ]);
}
```

### Pour ajouter une série

On créé une nouvelle méthode.

**/!\ Pour rester sur une API REST**, il faut que la route soit la même que celle de GET List donc `http://localhost:8080/api/v1/tvshows/` mais il faut changer la méthode de la route GET en POST, ce qui va donner :

`@Route("/", name="add", methods={"POST"})`

On va commencer par récupérer les datas en Json `$jsonData = $request->getContent();`

Ensuite on va se servir d'un serializer afin de transformer nos données en objet pour la BDD, On va utiliser alors SerializerInterface, pour désérialiser, et ce grâce à 3 arguments :
1 - les datas à desérialiser, donc le Json récupéré
2 - le type d'objet à créer, ici un objet de la class TvShow
3 - le type de data au départ, donc JSON

`$tvShow = $serializer->deserialize($jsonData, TvShow::class, 'json');`

On appelle le manager, on persist / flush, et enfin on return en Json avec un code HTTP 201

```php
public function add(Request $request, SerializerInterface $serializer)
{
    // we take back the JSON
    $jsonData = $request->getContent();

    // We transform the json in object
    // First argument : datas to deserialaize
    // Second : The type of object we want 
    // Last : Start type
    $tvShow = $serializer->deserialize($jsonData, TvShow::class, 'json');

    $em = $this->getDoctrine()->getManager();
    $em->persist($tvShow);
    $em->flush();

    return $this->json($tvShow, 201);
}
```
