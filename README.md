<img src="public/images/name.png" alt="InaZaoui" width="200" />[![forthebadge](/badges/powered-by-coffee.svg)](https://forthebadge.com)[![forthebadge](/badges/docker-container.svg)](https://forthebadge.com)

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

### SetUp Docker (déjà prêt):

PHP 8.2 + Composer + Nginx + PostgresSQL + pgAdmin
Site : http://localhost:8081
pgAdmin : http://localhost:8080/

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

### 3. Configurer la DB:

Créez le .env.local à la racine (dans docker, l'hôte est postgres et non 127.0.0.1) :

```bash
DATABASE_URL="postgresql://postgres:postgres@postgres:5432/ina_zaoui?serverVersion=16&charset=utf8"
```

### 4. Construire les images Docker:

```bash
docker compose up -d --build
```

### 5. Installer les dépendances:

```bash
docker compose exec php composer install
```

### 6. pgAdmin: se connecter au serveur Postgres

**(pour un projet réél utilisez un gestionnaire de mdp pour transmettre les logs):**

Ouvrez pgAdmin : http://localhost:8080/

**Identifiants:**

- Email : admin@admin.com
- Mot de passe : admin

**Ajouter un serveur:**

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

### 7. Créer les tables (migration):

```bash
docker compose exec php php bin/console doctrine:migrations:migrate -n
```

### 8. Importer les données SQL (anonymisées):

Le docker-compose.yml monte le dossier /sql dans le conteneur Postgres à partir du dossier ./database (les fichiers .sql des trois tables anonymisée doivent s'y trouver)
Utiliser les .sql que je fourni. Les images ayant été passées dans xnConvert le lien des images a été changé pour .webp et non plus .jpg
Excecutez ces commandes dans l'ordre indiqué (pour éviter un problème de Foreign Key)

```bash
docker compose exec -T postgres psql -U postgres -d ina_zaoui -v ON_ERROR_STOP=1 -f /sql/01_user.sql
docker compose exec -T postgres psql -U postgres -d ina_zaoui -v ON_ERROR_STOP=1 -f /sql/02_album.sql
docker compose exec -T postgres psql -U postgres -d ina_zaoui -v ON_ERROR_STOP=1 -f /sql/03_media.sql
```

## ✅ Usage

### 1. Démarrer le projet:

1. Lancer Docker Desktop
2. Ensuite à la racine du projet faites:

```bash
docker compose up -d
```

3. Ouvrez pgAdmin: http://localhost:8080/
4. Ouvrez le Site : http://localhost:8081

### 2. Connexion:

Pour se connecter avec le compte de Ina:

- identifiant : `ina`
- mot de passe : `password`

### 3. Arrêter le projet:

```bash
docker compose down
```

## ✅ Tests
