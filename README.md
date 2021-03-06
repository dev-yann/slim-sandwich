# API - slim-sandwich
api listant les sandwichs

BDD File => sql/bdd.sql

### 3 serveurs :

**Requetes api public :**
>http://api.lbs.local:10080

**Requete api privée :**
>http://api.lbs.private.local:7081

**Backend de gestion :**
>http://api.lbs.backend:7080

**Adminer : installer la bdd**
>http://api.lbs.local:8080/

### Documentation API

>/private_docs

>/backend_docs

>/public_docs

### Prérequis

Nécéssite Composer

### Installation

Récupération du projet

```
Clone le depot git
```

```
Importation de la BDD 
```

```
Configuration du fichier src/conf/lbs.db.conf.ini pour adminer
```

```
docker-compose up
```

```
docker-compose start
```

```
Dans /src composer install
```

## Fait avec

* [Slim](https://www.slimframework.com/) - Framework PhP
* [Eloquent](https://laravel.com/docs/5.0/eloquent) - ORM
* [Twig](https://twig.symfony.com/) - Template engine for PHP

# Use
## Routes

### Catégories

Accéder à la liste des catégories: (via un get)
>categories

Accéder à une catégorie: (via un get)
>categories/{id}

Créer une catégorie: (via un post)
>categories

Modifier une catégorie: (via un put)
>categories/{id}

### Sandwichs

Accéder à la liste des sandwichs: (via un get)
>sandwichs

Accéder à un sandwich: (via un get)
>sandwichs/{id}

Paramètres possibles:
>sandwichs?type=&img=&page=&size=
