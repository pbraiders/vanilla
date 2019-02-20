# Pbraiders

Pbraiders est une application web de gestion de réservations à destination des gérants de terrains de paintball. PbRaiders a été développé dans un souci de simplicité, rapidité, performance, robustesse et sécurité.

## Pré-requis

Pbraiders fonctionne grâce au couple MySQL et PHP sur un serveur web. MySQL est un gestionnaire de base de données et PHP un langage de scripts qui permet de produire des pages web dynamiques.

Les versions PHP et MySQL minimums sont :
PHP: version 5.2 ou supérieure. Les modules modules suivants doivent être activés:
     - GD version 2.0 ou supérieure.
     - PDO (driver pour MySQL)
MYSQL: version 5.0 ou supérieure.

PBRaiders utilise aussi les procédures stockées MySQL. Une procédure stockée est un ensemble d'instructions SQL pré-compilées, stockées sur le serveur, directement dans la base de données. Pendant l'installation, PBRaiders va tenter de les installer. Mais, à cause des restrictions imposées par les hébergements mutualisés, il est possible que PBRaiders n'y arrive pas. Dans ce cas, PAS DE PANIQUE: PBRaiders est configuré, par défaut, pour ne pas les utiliser et est complètement opérationnel sans.

## Installation

1- Créez une base de données pour PBRaiders sur votre serveur web.

  a- Créez la base de données (par exemple: pbraidersdb) avec les options suivantes si votre hébergeur vous le permet:
   CHARACTER (ou "jeu de caractères" ) = utf8
   COLLATE (ou " interclassement" ) = utf8_general_ci

  b- Si votre hébergeur vous le permet, créez un utilisateur (par exemple: pbraidersdbuser) avec les privilèges suivants sur cette base de données:
   Données: SELECT , INSERT , UPDATE , DELETE
   Structure: CREATE , DROP , INDEX , ALTER , CREATE TEMPORARY TABLES

2- Décompressez l'archive PBRaiders.zip sur votre ordinateur. Le dossier nouvellement créé contient les dossiers et les fichiers nécessaires à l'installation et au bon fonctionnement de PBRaiders.

3- Copiez le fichier config-sample.php en config.php.

4- Ouvrez le fichier config.php dans un éditeur de texte et complétez les informations suivantes.

  a- Remplacez votre_url dans "define('PBR_URL','votre_url');" par l'adresse d'accès au site. (Par exemple: <https://www.votre-site.com/pbraiders/> ). ATTENTION: n'oubliez pas le caractère slash "/" à la fin.

  b- Remplacez votre_nom_de_bdd dans "define('PBR_DB_DBN','votre_nom_de_bdd');" par le nom de votre base de données que vous avez créé en 1. (Par exemple: pbraidersdb )

  c- Remplacez votre_utilisateur dans "define('PBR_DB_USR','votre_utilisateur');" par le nom d'utilisateur de votre base de données que vous avez créé en 1. (Par exemple: pbraidersdbuser )

  d- Remplacez votre_mot_de_passe dans "define('PBR_DB_PWD','votre_mot_de_passe');" par le mot de passe de l'utilisateur de votre base de données que vous avez créé en 1.

  e- Remplacez localhost dans "define('PBR_DB_HOST','localhost');" l’adresse de votre base de données. Dans la plupart des cas, vous n'avez pas à modifier cette valeur.

5- Enregistrez le fichier config.php (au format UTF-8 sans BOM ou ANSI) pour sauvegarder vos modifications.

6- Créez un dossier sur votre serveur. (Par exemple: pbraiders).

7- Copiez tout le contenu du dossier PBRaiders situé sur votre ordinateur dans le dossier que vous venez de créer sur votre serveur. Ne changez rien à ces fichiers, ni leur nom ni leur organisation dans les dossiers.

8- Vous devez donner les droits en écriture à PHP (chmod) sur les dossiers export/ et log/

9- Ouvrez la page install.php dans votre navigateur. (Par exemple: <https://www.votre-site.com/pbraiders/install.php>)
   Suivez les instructions. Si vous obtenez une erreur, vérifiez le contenu du fichier config.php, et réessayez.
   Si cela échoue encore une fois, contactez moi par email (pbraiders@netcourrier.com) et soyez le plus précis possible dans vos explications.

10- Une fois l'installation terminée, supprimez le fichier install.php et le répertoire includes-install\ de votre serveur.

11- Accédez au site avec votre navigateur. (Par exemple: <https://www.votre-site.com/pbraiders/>)
