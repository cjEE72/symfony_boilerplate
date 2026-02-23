<<<<<<< HEAD
# ESTREICH Ethan
=======
# BackOffice Symfony — README

Bonjour! Ce fichier décrit comment installer, configurer et tester le projet localement, ainsi que les fonctionnalités principales implémentées.

Toutes les instructions suivantes supposent que vous travaillez sur Windows et que PHP, Composer et une base de données (MySQL par exemple) sont installés.

---

## 1) Prérequis

- PHP 8.1+
- Composer
- Une base de données et l'URL configurée dans `.env` ou `.env.local`
- Symfony CLI pour lancer un serveur local

---

## 2) Installation du projet

Ouvrez un terminal (cmd.exe) dans la racine du projet et exécutez :

```bat
composer install
```

Copiez le fichier d'environnement si besoin et adaptez `DATABASE_URL` :

```bat
copy .env .env.local
rem Editer .env.local et définir DATABASE_URL (par ex. sqlite:///%kernel.project_dir%/var/data.db ou mysql://user:pass@127.0.0.1:3306/dbname)
```

Créer la base (si nécessaire) :

```bat
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Charger les fixtures (utilisateurs, clients, produits) :

```bat
php bin/console doctrine:fixtures:load --no-interaction
```

---

## 3) Fonctionnalités implémentées

Le projet contient les fonctionnalités suivantes :

- Authentification & gestion des rôles (fixtures : `admin@test.com`, `manager@test.com`, `user@test.com` — mot de passe : `password123`).
- Gestion des produits (CRUD) : création, lecture, modification, suppression.
  - Les actions de création/import sont réservées aux administrateurs (ROLE_ADMIN).
- Export CSV des produits (`/product/export/csv`).
- Import CSV des produits (`/product/import`) — uniquement pour les administrateurs.
  - Le service `CsvImporter` est smart: il crée de nouveaux produits si l'`id` est vide, ou met à jour un produit existant si l'`id` est renseigné.
- Gestion des clients : liste, formulaire d'ajout/modif, suppression (accès via voter `CUSTOMER_ACCESS`).
  - Le formulaire de création/modification de client est protégé par validateur pour garantir que les données sont valides (pas d'email double, etc).
- Templates Twig stylisés avec Tailwind (chargé via CDN dans `base.html.twig`- j'ai eu des problèmes avec la compilation de TailWind).
- Tests unitaires pour les entités et pour l'import/export CSV- la **Q**ualité!

---

## 4) Lancer l'application localement

Option A — via PHP built-in server :

```bat
php -S 127.0.0.1:8000 -t public
```

Option B — via Symfony CLI :

```bat
symfony server:start
```

Puis ouvrir `http://localhost:8000` ou `http://127.0.0.1:8000`.

---

## 5) Utilisateurs de test (fixtures)

Après `doctrine:fixtures:load`, les comptes suivants sont disponibles :

- Admin : `admin@test.com` / `password123` 
- Manager : `manager@test.com` / `password123` 
- User : `user@test.com` / `password123` 

---

## 6) Tests unitaires

Les tests unitaires sont dans le dossier `tests/`.

Pour exécuter la suite de tests :

```bat
php bin/phpunit --testdox
```

Si vous préférez exécuter directement le binaire du vendor :

```bat
php vendor\bin\phpunit --testdox
```

>>>>>>> 384dfd8 (feat: client validations, CSV import/export, Tailwind templates, fixtures, and tests; update README and phpunit config)
