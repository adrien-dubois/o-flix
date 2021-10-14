# Livraison projet

1. On vérifie que le code fonctionne => tests unitaires et fonctionnels
2. On créé une table de production vide, pour vérifier que notre application fonctionne m$eme si il n'y a pas ou peu de données
   1. Dans le fichier `env.local`, on modifie le nom de la table en rajoutant le suffic `_prod` (`oflix_prod`)
   2. Ensuite on créé la table que l'on remplit avec des fixtures 
      1. `php bin/console d:d:c`
      2. `php bin/console make:migration`
      3. `php bin/console d:mi:mi` ou `php bin/console doctrine:migration:migrate` 
3. Installer `composer require symfony/apache-pack` pour avoir un .htaccess de configuré dans `/public`
4. On vérifie que le projet a été pushé sur un serveur Git et d'être sur la branch `master`


```bash
cp oflixvm.pem /home/student/.ssh/

cd

chmod 400 .ssh/oflixvm.pem

ssh -i .ssh/oflixvm.pem ubuntu@ec2-3-89-75-58.compute-1.amazonaws.com

sudo apt-get update
sudo apt-get install apache2
sudo apt-get install php7.4 php7.4-common php7.4-cli php7.4-mysql libapache2-mod-php7.4 php7.4-mbstring php7.4-json php7.4-xml
sudo apt-get install mysql-server

sudo mysql

CREATE USER 'explorateur'@'localhost' IDENTIFIED BY 'Ereul9Aeng';
CREATE USER 'explorateur'@'localhost' IDENTIFIED BY 'Ereul9Aeng';
exit

cd /var/www/html/

sudo chown -Rf ubuntu:www-data /var/www/html/

rm index.html

sudo apt-get install zip

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer

sudo a2enmod rewrite

sudo nano /etc/apache2/apache2.conf

<Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
</Directory>


```