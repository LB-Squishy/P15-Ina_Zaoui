<img src="public/images/name.webp" alt="InaZaoui" width="300" />

# Ina Zaoui : Directives de contribution

Tout d'abord, bienvenue sur ce projet !

Les directives suivantes ont pour but de définir un cadre de contribution le plus clair possible afin de maintenir une continuité et une unité dans l'ensemble de ce projet.
Ceci reste des lignes directrices, pas des règles. Veuillez faire preuve de discernement et n'hésitez pas à proposer des amélioration ou modifications à ce document si vous remarquez quoi que ce soit pouvant améliorer le processus général.

## ✅ Transition

Bonjour,

Actuellement le projet est prêt sous Docker afin de facilité sa transition vers la nouvelle développeuse interne, cela permettra d'éviter tout problème d'installation et permettra un démarage rapide sans problématique de version.
La procédure d'installation est entièrement détaillée dans le [README.md](https://github.com/LB-Squishy/P15-Ina_Zaoui/blob/main/README.md).

Docker offre la possibilité d'uniformiser l'installation d'un projet, n'hésitez pas à vous l'approprié et à en améliorer la mise en place, restant novice en la matière je suis persuadé qu'à l'usage de nouveau points d'optimisations peuvent être trouvés.

Je vous ai également adressé des suggestions de features et de points que j'ai pu souligner au fur et à mesure de la mise en place des éléments de ma mission. J'en ai solutionné quelques uns mais fautes de temps et ne pouvant m'éloigner du cadre de ma mission je vous les ai listé en bas de ce fichier.

Je vous souhaite un bon démarrage !

Cordialement

## ✅ Sécurité

### 1. Mots de passes:

Afin d'éviter toute fuite de mot de passe ou identifiant liés au projet, veuillez utiliser un gestionnaire de mot de passe avec profil de participant.
Aucun mot de passe ou identifiant ne doit se situer sur un post it positionné sur votre bureau.
A la première mise en place du projet:

- Créez le gestionnaire de mot de passe
- Renouvelez l'intégralité des mots de passes et mot de passe de test
- Enregistrez les dans le gestionnaire et retirez les anciens du projet

### 2. .ENV et variables d'environnement:

Veuillez systématiquement créé des fichiers d'environnement local:
.env.local / .env.test.local

Ceux ci sont en gitignore.
De même, les .local ne doivent en aucun cas être commités sur le dépôt.

Afin de retrouver les valeurs de ceux ci plus facilement et de facilité le travail à plusieurs veuillez également intégrer le contenu des fichiers .env dans le gestionnaire de mot de passe

### 3. Politique de mise à niveau:

Veuillez vous montrer attentif vis à vis des montée de version afin de ne pas avoir à accuser une trop grosse montée de version d'un coup.
A chaque évolution, pensez à réactualiser le [README.md](https://github.com/LB-Squishy/P15-Ina_Zaoui/blob/main/README.md)

_Important: Pensez également à actualiser les versions en CI et les images Docker_

## ✅ Workflow Github

### 1. Dévelopement d'une nouvelle fonctionnalité

#### Nouvelle branche

Lorsque vous démarrez une intervention sur le code (fix, documentation, nouvelle feature).
Veuillez créer une nouvelle branche à partir de la main afin d'être à jour :

```bash
git checkout -b my-branch
```

Et la nommer de cette manière (liste d'exemple ci-dessous):

- feat/miseEnPlaceDuneNouvelleFeature
- fix/nomDuProblemeFixé
- ...

#### Commit

Ne jamais commit dans la main ou dans la preprod.
Veillez toujours à effectuez votre commit dans votre branche de travail en la nommant de cette manière:

- feat(enUnMotDomaineDintervention): Description de votre intervention
- ...

**Appliquez cs-fixer**

```bash
docker compose exec php composer test:cs:fix
```

**Executer les tests PHPUnit**

```bash
docker compose exec php composer test:phpunit
```

**Executer les tests PHPStan**

```bash
docker compose exec php composer test:phpstan
```

Une fois le commit effectuez, vérifiez toujours la validation des tests lancés sur la branche qui vient d'être push sur github.
_Chaque echec au test doit être examiné manuellement afin d'en déterminer la cause_

**Si vous voulez effectuer un test global rapide avant chaque commit c'est l'idéal (cs-check + phpunit + phpstan):**

```bash
docker compose exec php composer test:quick
```

#### Pull Request

Une fois votre feature ou autre intervention terminée, si vous voulez l'intégrer au projet, veuillez effectuer une Pull Request vers la Preprod.

- Vérifier le passage des tests avant envoi de la pull request

#### Merge de la Pull Request

Seul le ou la chef(fe) de projet sera habilité à merge les nouvelles features.

- Résolvez les éventuels conflits
- Vérifier le passage aux tests (chaque echec au test doit être examiné manuellement afin d'en déterminer la cause)
- Effectuez le merge
- Contrôler le bon fonctionnement sur la version du site déployé en préproduction
- Réitérez la pull request puis merge vers la main depuis la preprod
- Vérifier le bon fonctionnement du site

### 2. Tests automatisés et pipeline d'intégration

#### Tests Unitaire et Fonctionnels

Afin de déceller tout problème en amont, un test est effectué à chaque envoi sur la branche créé.
Ce test comprend :

- La résolution des tests PHPUnit unitaires
- La résolution des tests PHPUnit fonctionnels
  Ce test est relancé à chaque merge afin de déceller toute problématique à chaque étapes par précaution.

#### Analyse Statique

Afin de déceller tout problème en amont, un test est effectué à chaque envoi sur la branche créé.
Ce test comprend :

- La résolution des tests PHP Stan de Niveau 6
- La résolution d'un check par cs-fixer
  Ce test est relancé à chaque merge afin de déceller toute problématique à chaque étapes par précaution.

#### Rapport de test ou test-coverage

Le test coverage ou rapport de couverture est créé et déployé sur github-page après merge sur la preprod: [Lien](https://lb-squishy.github.io/P15-Ina_Zaoui/)
Le chef de projet, seul habilité à merge sur la preprod et sur la main devra contrôler celui-ci systématiquement.
Un taux de 70% de couverture est demandé à titre indicatif. Cependant cela reste indicatif, veillez à vérifier la pertinance de tout test nouvellement créé.

#### Commandes Utiles:

L'ensemble des commande utiles de test sont disponibles sur le [README.md](https://github.com/LB-Squishy/P15-Ina_Zaoui/blob/main/README.md)
Cf. section : Scripts Utiles (Composer)

## ✅ Bonnes pratiques

### 1. README & CONTRIBUTING

N'hésitez pas à enrichir ces deux sections et à contrôler périodiquement leur bonne adéquation avec le projet et son évolution.
Veuillez respecter la hiérarchisation des titres afin de s'y repérer facilement (pensez à ajouter systématiquement l'icône ✅ afin de faire ressortir les titres de niveau 2 et d'en faciliter la lecture).
Le/la chef(fe) de projet reste seule habilité à valider toute modifications de ces deux fichiers.

### 2. Testing

A chaque nouvelle feature veuillez à créer les tests fonctionnels et unitaires nécessaire.
Prenez systématiquement le temps d'essayer de projeter le parcours de votre utilissateur et de vos données cela accroitra la pertinance de vos tests.

- Afin de créer un nouveau test sous docker, saisissez :

```bash
docker compose exec php php bin/console make:test
```

- Sélectionnez le type de test (webTestCase pour les tests fonctionnels / testCase pour les tests unitaires)
- Concernant les tests fonctionnels, une série de helpers sont disponibles dans le fichiers FunctionalTestCase qui extends deja WebTestCase
- Veuillez remplacer "extends WebTestCase" par "extends FunctionalTestCase" pour pouvoir les utiliser

### 3. Commentaires

Prenez le temps de toujours commenter votre code. Afin d'en faciliter la lecture à l'avenir, il est attendu :

- un commentaire au dessus de toute fonction expliquant son rôle
- un commentaire inline avant toute action spécifique dans la fonction expliquant ce qui est effectué

Un code bien commenté est un code facilitant l'intégration de toute nouvelle personne à votre projet, ceci vous facilitera également alégrement la tâche lors d'une intervention sur un code que vous avez écris il y 3 mois.

## ✅ Suggestions de Développement

### 1. Modèle de donnée

Actuellement les Utilisateurs et leurs contenu sont supprimés d'un tenant tel que demandé.
Cependant je préconise la mise en place d'une suppression en 2 temps:

- passage en "is_archived" en BDD
- suppression des données définitivement et fichiers physique dans un second temps au travers une tâche CRON

En effet, il n'est pas rare qu'une fausse manipulation ai lieu et il est mieux de pouvoir récupérer les données au besoin.
De même cela permet de bien dissocier la suppression des images en BDD et la suppression physique des fichiers, cependant actuellement ces deux logiques sont mélangées.

Si ceci est mis en place par la suite, bien réfléchir à la durée de conservation.

### 2. Activation/Désactivation d'accès

Actuellemnt l'activation/Désactivation d'un utilisateur lui retire son accès admin ainsi que l'affichage de ses médias sur le site.
Il est préférable de manière générale de séparer ces deux éléments en créant deux rôles distinct et la possibilité de désactiver l'un sans l'autre.

### 3. Mot de Passe oublié

Il est noté l'absence de possibilité pour un utilisateur de pouvoir modifier son mot de passe sans passer par l'administrateur. Afin de faciliter le flux quotidien, je recommande le développement de cette feature.

### 4. Favicon & Metadata

Le favicon étant absent j'ai repris le logo, cependant il est préferable de prendre le temps de le penser afin de le remplacer.
De même prenez le temps de préparer des meta pertinantes sur chaques pages afin de bien veillez au bon référencement du site.

### 5. Images

Les images ont été reformatées au format .webp
Privilégiez ce format pour le web. Etant sur un site de photographe je n'ai pas changé les images des portfolios afin de pouvoir les voir en entier.

Mais je recommande l'intégration d'un outil permettant de cropper les images à l'envoi des fichiers et d'intégrer une features sur le site permettant d'afficher les images croppées sur les portfolios et au clic sur l'image d'aller chercher l'image entière pour un affichage pleine page.

De même je préconise l'hébergement des images sur un CDN.

## Bonne Contribution à vous !
