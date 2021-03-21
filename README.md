**Projet Biblio**
=====================

# Infos projet :
Nom du projet : Biblio
Type du projet : Site web sous PHP Symfony avec Bootstrap
Charge de travail : Entretiens avec le client, gestion de projet Scrum, découplage modèle framework, TDD...
Durée : 2 mois
Equipe : 4 personnes
Rôle : Product Owner (sous méthode agile scrum)
But du livrable : Proposer un site permettant aux utilisateurs de lire et de publier leurs bande dessinées
Classement : 3ème sur 12 projets



# Architecture

## Images docker du projet 

L'architecture du projet **MPAPWS** est basée sur 2 images docker :
* **iutlr-info-apache-symfony4-mpapws** : gère le code source Symfony4 de l'application, qui se trouve dans dans le dossier « **mpapws** »

* **iutlr-info-mysql-mpapws** : gère la base de données mysql du projet, qui se trouve dans le dossier « **mysql** »

Ces deux images docker sont instanciées pour démarrer deux conteneurs respectivement :    
* **iutlr-info2-projet-mpapws** : conteneur exécutant le code source du projet  
* **iutlr-info2-mysql-mpapws** : conteneur exécutant la base de données du projet 

## Archiecture physique 

**projet_mpapws/**  
├── README.md     
├── mpapws/     
├── mysql/   
├── build/  
└── docker-compose.yml  

* **mpapws** : le projet MPAPWS en Symfony4, avec :
    * url d'accès : "http://mpapws.loc:9999" 
    * les fronts en twig
    * les données gérées par doctrine à partir du serveur **mysql**

* **mysql** : le serveur mysql gérant la base de données : 
    * url d'accès : en JDBC sur localhost:3306
    * MYSQL_DATABASE: db-mpapws
    * MYSQL_USER: mpapws
    * MYSQL_PASSWORD: mpapws
    * MYSQL_ROOT_PASSWORD: mpapws

* **build** : les fichiers dockerfile pour construire les images nécessaires à partir du registry public de l'IUT : 
    * url du registry : http://registry.univ-lr.fr:80 
    * **IMPORTANT** : ajouter ce registry dans la config du deamon docker, exemple voir, la capture ci-dessous pour la config de docker sous OSX : 
    <div align="center" ><img alt="config registry docker OSX" src="ressources/osx-docker-registry-config.png" width="300" height="300" /></div>

# Mise en oeuvre

## Supprimer les images existantes (optionnel)

* Vérification que les containers sont bien arrêtés 
```
docker ps
```
S'il y a les container _biblio_ et _biblio-mysql_ qui tournent, faire les commandes suivantes
```
docker stop biblio
docker stop biblio-mysql
```

* Vérification que les conatainers n'existent pas
```
docker ps -a
```
S'il y a les container _biblio_ et _biblio-mysql_, faire les commandes suivantes
```
docker container rm biblio
docker container rm biblio-mysql
```

* Suppression des images
```
docker image ls
```
S’il y a _iutlr-info-apache-symfony4-mpapws_ et _iutlr-info-mysql-mpapws_, faire les commandes suivantes
```
docker image rm iutlr-info-apache-symfony4-mpapws
docker image rm iutlr-info-mysql-mpapws
```

## Construction et démarrage 

* Vérifier que le programme docker est démarré 

* Ouvrir un terminal et se déplacer dans le dossier du projet 
``` 
cd projet-mpapws/
```
* Cloner le projet
```
git clone https://forge.iut-larochelle.fr/kmary/2019-2020-info2-mpapws-biblio.git
cd 2019-2020-info2-mpapws-biblio
```

* Construire les images
```
docker-compose build
```

* Lancer les containers (ajouter -d si vous voulez l'executer en mode daemon)
```
docker-compose up
```

* Dans le terminal PHP Storm, se connecter au container
```
docker exec -it biblio bash
```

* Installer les dépendances du projet
```
cd mpapws
composer install
```

* Lancer le serveur web
```
php bin/console server:start 0.0.0.0:80
```

Si vous avez bien ajouté le lien entre _127.0.0.1_ et _mpapws.loc_ dans le fichier _hosts_ comme expliqué à la fin du README, vous pourrez accéder à votre page via l'URL http://mpapws.loc:9999 !

/!\ Attention : Ne pas executer la commande à la fin, celle qui commence par ```composer create-project``` /!\

## Gérer les images/conteneurs docker du projet 

* Ouvrir un terminal et se déplacer dans le dossier du projet 
``` 
cd projet_mpapws/
```

* Consulter le status des conteneurs
```
docker-compose ps 
```

* Consulter quelques informations sur les images et les conteneurs du projet
```
docker-compose images
```

* Arrêter les conteneurs
```
docker-compose stop 
```

* Supprimer les conteneurs
```
docker-compose rm 
```

* Se connecter en bash sur un conteneur démarré
```
docker exec -it (nom du conteneur ou id) bash 
```
ou 
```
docker-compose exec (nom du service) bash 
```

# Déclaration des hosts côté client

* Le conteneur _biblio_ est accessible en http sur le port 9999 via son virtual host attaché à ce conteneur :
    * http://mpapws.loc:9999 

* La configuration du virtual host se trouve dans le dossier « vhosts » dans « build/mpapws/vhosts ». 

* Par conséquence, le client qui souhaite accéder à ce host doit déclarer le vhost dans le fichier « hosts » de sa machine comme suite :
```
127.0.0.1     mpapws.loc
```

# Création du projet Symfony **mpapws**

* Démarrer les conteneurs docker du projet 

* Ouvrir un terminal bash sur le conteneur **iutlr-info2-projet-mpapws**

* Normalement, le répertoire de travail est : « /var/www/html »

* Créer le projet Symfony suivant le modèle website-skeleton, nommé **mpapws** et se restreindre à la version "4.3.*" (attention, mettre le nom réel de votre projet) : 

```
composer create-project symfony/website-skeleton mpapws "4.3.*"
```
