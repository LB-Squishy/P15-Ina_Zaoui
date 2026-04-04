<img src="public/images/name.webp" alt="InaZaoui" width="300" />

# Ina Zaoui : Refactorisez le code d'un site pour l'optimiser

## 📋 Contenu:

En tant qu’indépendant, vous venez de décrocher un nouveau contrat de prestation pour Ina Zaoui, une photographe spécialisée dans les photos de paysages du monde entier. Elle est connue pour son mode de déplacement eco-friendly (à dos d'animal, à pied, en vélo ou bateau à voile et montgolfière...).

Il vous est demandé de mettre à jour et corriger son site. Vous remplacez la personne chargée du développement et de la maintenance du site, le temps qu’Ina trouve un remplaçant permanent.

**Au programme:**

- la migration de version symfony
- l'implémentation de nouvelles fonctionnalités
- la correction des bugs
- la rédaction de la documentation
- la mise en place d'une pipeline d'intégration continue

### Prérequis:

- Docker Desktop installé et lancé
- Git
- VS Code + extension Docker

### SetUp Docker:

- Symfony 7.4 LTS [Lien](https://symfony.com/releases)
- PHP 8.3 [Lien](https://www.php.net/supported-versions.php)
- PHPUnit 12.5 [Lien](https://phpunit.de/supported-versions.html)
- Composer
- Nginx
- PostgresSQL
- pgAdmin

### Accès:

- Site : http://localhost:8081
- pgAdmin : http://localhost:8080/

### Liens Utiles:

Veuillez suivre le lien suivant afin de prendre connaissance des directives liées aux contributions: [Lien](https://github.com/LB-Squishy/P15-Ina_Zaoui/blob/main/CONTRIBUTING.md).
Pour consulter le Rapport de taux de couverture des tests une fois le code arrivé en pre-production suivre ce lien: [Lien](https://lb-squishy.github.io/P15-Ina_Zaoui/).

## ✅ Installation

### 1. Cloner le projet:

```bash
git clone https://github.com/LB-Squishy/P15-Ina_Zaoui.git
cd P15-Ina_Zaoui
```

### 2. Récupérer les Images (dossier Uploads via GitHub Releases):

Le temps de mise en place d'une solution d'hébergement des images, le contenu du dossier 'public/uploads' est trop lourd.
Afin de limiter le poid des commit, les images sont fournies via **GitHub Releases**.

- Téléchargez le dossier compressé depuis: https://github.com/LB-Squishy/P15-Ina_Zaoui/releases/download/v1.0.0/uploads.zip
- Décompressez le contenu du dossier vers 'public/uploads'

### 3. Configurer la DB principale:

Créez le .env.local à la racine (dans docker, l'hôte est postgres et non 127.0.0.1) :

```bash
DATABASE_URL="postgresql://postgres:postgres@postgres:5432/ina_zaoui?serverVersion=16&charset=utf8"
```

### 4. Configurer la DB test:

Créez le .env.test.local à la racine (dans docker, l'hôte est postgres et non 127.0.0.1) :

```bash
DATABASE_URL="postgresql://postgres:postgres@postgres:5432/ina_zaoui?serverVersion=16&charset=utf8"
```

### 5. Construire les images Docker:

- Téléchargez Docker Desktop puis lancez le.

- Ensuite construisez les images avec la commande suivante:

```bash
docker compose up -d --build
```

### 6. Installer les dépendances:

```bash
docker compose exec php composer install
```

## ✅ Base de donnée

### 1. Créer et alimenter la DB principale:

**Cette commande faisant un drop par sécurité veillez à couper toutes éventuelle connexion à cette base si elle existe déjà**

```bash
docker compose down
docker compose up -d
```

**Puis:**

```bash
docker compose exec php composer db:reset
```

_Note : Cette commande comprend un drop de la table si existante, sa creation, la migration ainsi que son alimentation à partir des fixtures (cf. section scripts utiles ci-dessous ou fichier composer.json)_

### 2. Créer et alimenter la DB test:

**Cette commande faisant un drop par sécurité veillez à couper toutes éventuelle connexion à cette base si elle existe déjà**

```bash
docker compose down
docker compose up -d
```

**Puis:**

```bash
docker compose exec php composer db:test:reset
```

_Note : Cette commande comprend un drop de la table test si existante, sa creation, la migration ainsi que son alimentation à partir des fixtures (cf. section scripts utiles ci-dessous ou fichier composer.json)_

## ✅ pgAdmin(optionnel): se connecter au serveur Postgres

_Note : (pour un projet réél utilisez un gestionnaire de mdp pour transmettre les logs et les générer)_

Ouvrez pgAdmin : http://localhost:8080/

### 1. Identifiants:

- Email : admin@admin.com
- Mot de passe : admin

### 2. Ajouter un serveur:

- Dans pgAdmin => "Ajouter un serveur"
- Onglet "General":
    - Nom : "Projet OC"
- onglet "Connection":
    - Nom d'hôte : postgres
    - Port : 5432
    - BDD de maintenance : postgres
    - Identifiant : postgres
    - Mot de passe : postgres
    - "Cocher enregistrer le mdp"

## ✅ Utilisation courante

### 1. Démarrer le projet:

1. Lancer Docker Desktop
2. Ensuite à la racine du projet faites:

```bash
docker compose up -d
```

### 2. Accéder au projet:

1. Ouvrez pgAdmin: http://localhost:8080/
2. Ouvrez le Site : http://localhost:8081

### 3. Connexion à l'admin:

_Note : (pour un projet réél utilisez un gestionnaire de mdp pour transmettre les logs et les générer)_

Pour se connecter avec le compte de Ina(SUPER_ADMIN):

- email : `ina@zaoui.com`
- mot de passe : `password`

Pour se connecter avec le compte de l'invité 2(ADMIN accès activé):

- email : `invite+2@example.com`
- mot de passe : `password`

Pour se connecter avec le compte de l'invité 1(ADMIN accès desactivé):

- email : `invite+1@example.com`
- mot de passe : `password`

### 4. Arrêter le projet:

```bash
docker compose down
```

## ✅ Scripts Utiles (Composer)

_Note : Utilisant Docker, pensez à ajouter "docker compose exec php composer"_

```bash
docker compose exec php composer
```

### 1. Manipulation de la DB

**Base Principale:**

```bash
docker compose exec php composer db:drop
docker compose exec php composer db:create
docker compose exec php composer db:migrate
docker compose exec php composer db:fixtures
```

```bash
docker compose exec php composer db:reset
```

**Base de Test:**

```bash
docker compose exec php composer db:test:drop
docker compose exec php composer db:test:create
docker compose exec php composer db:test:migrate
docker compose exec php composer db:test:fixtures
```

```bash
docker compose exec php composer db:test:reset
```

## ✅ Tests

### 1. Rapport de couverture de code

**Générer le rapport**

```bash
docker compose exec -e XDEBUG_MODE=coverage php composer test:coverage
```

_Note : Le rapport doit être regénéré après chaque modification du code ou des tests._

**Ouvrir le rapport dans le navigateur**

```bash
start ./public/test-coverage/index.html
```

### 2. Utilisation de CS Fixer

**Checker le projet**:

```bash
docker compose exec php composer test:cs:check
```

**Fixer le projet**:

```bash
docker compose exec php composer test:cs:fix
```
