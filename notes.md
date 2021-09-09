# Livraison projet

1. On vérifie que le code fonctionne => tests unitaires et fonctionnels
2. On créé une table de production vide, pour vérifier que notre application fonctionne m$eme si il n'y a pas ou peu de données
   1. Dans le fichier `env.local`, on modifie le nom de la table en rajoutant le suffic `_prod` (`oflix_prod`)
   2. Ensuite on créé la table que l'on remplit avec des fixtures 
      1. `php bin/console d:d:c`
      2. `php bin/console make:migration`
      3. `php bin/console d:mi:mi` ou `php bin/console doctrine:migration:migrate` 
3. On vérifie que le projet a été pushé sur un serveur Git
4. Installer `composer require symfony/apache-pack` pour avoir un .htaccess de configuré dans `/public`