# Custom Queries pour formulaire de recherche

## Différents niveaux :

### Récupérer la liste des séries

### Query Builder
### DQL - Doctrine Query Language
### PDO
### Mysql


## Formulaire de recherche

Rediriger l'action du formulaire, et mettre un name :

```
 <form class="d-flex m" action=" {{path('search_index')}} ">
    <input class="form-control me-2" type="search" placeholder="Ex : The mandalorian" aria-label="Search" name="q">
    <button class="btn btn-outline-secondary" type="submit">GO</button>
</form>
```

## Dans le controller récupérer la recherche en GET

Et envoyer la recherche à la méthode codée dans le Repository correspondant

```
$query = $request->query->get('q');
$results = $tvShowRepository->searchTvShowByTitle($query);
```

## Methode 1 Custom Query

```
public function searchTvShowByTitle($title)
{
    return $this->createQueryBuilder('tvshow')
        ->where('tvshow.title Like :title')
        ->setParameter(':title', "%$title%" )
        ->getQuery()
        ->getResult();
}
```

## Methode 2, avec le DQL, en utilisant une requête SQL

```
public function searchTvShowByTitleDQL($title)
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(

        'SELECT tv
        FROM App\Entity\TvShow tv
        WHERE tv.title LIKE :title'
    )->setParameter(':title', "%$title%");

    return $query->getResult();
}
```
