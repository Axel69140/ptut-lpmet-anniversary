# Notice de déploiement du logiciel
```
git clone https://github.com/Axel69140/ptut-lpmet-anniversary.git
```

## Création de la base de données

Télécharger MariaDB Server 

https://mariadb.org/download/?t=mariadb&p=mariadb&r=10.11.2&os=windows&cpu=x86_64&pkg=msi&m=mva

Connectez vous avec les identifiants que vous aurez mis lors de l'installation,

Puis entrez la commande 

```
CREATE DATABASE ptut_anniversary;
use ptut_anniversary
```

## Connexion à la base de données

Dans le projet modifier le fichier .env situé dans le dossier back ligne 27

mysql://{username}:{password}@127.0.0.1:3306/{databaseName}

Changer les variables username par le username que vous aurez défini avant, pareil pour le password et mettre le nom de la base de données dans databaseName soit (ptut_anniversary).


## Installation des dépendances

Back : 

```
cd back
composer i
```

Front : 

```
cd front
npm i
```

## Lancer le projet 

Back : 

```
cd back
symfony server:start
```

Front : 

```
cd front
npm run dev
```

En regardant dans la console une url se lance, récupérer celle-ci (exemple : http://127.0.0.1:3000).

## Installer Postman 

Télécharger Postman.

https://www.postman.com/downloads/

Importer la collection lp-anniversary ainsi que l'environnement lp-anniversary disponible sur le dépôt Git.

Pour importer la collection sur Postman, il faut cliquer sur Collections à gauche puis sur IMPORT en haut à gauche de l'application et la même chose pour l'environnement.

Dans l'environnement coller l'url récupéré précédemment dans INITIAL VALUE et dans CURRENT VALUE.


## Documentation

https://documenter.getpostman.com/view/23639194/2s93XsYS3A

## Finalisation

Avant de pouvoir utiliser le site, il vous faudra initialiser les paramètres par défaut de l'administrateur via une requête pré-enregistré dans notre collection lp-anniversary sur Postman.
La requête est disponible sous le nom de : getSettings.

### Profitez bien de notre projet :)

# Notice utilisateurs



