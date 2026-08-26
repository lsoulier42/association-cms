# Association CMS

CMS opensource réutilisable conçu pour les sites d'associations : actualités,
pages personnalisables, agenda, équipe, partenaires, revue de presse, espace
d'administration complet.

> Projet issu d'un site réel d'association, nettoyé et généralisé pour être
> réemployé tel quel sur un nouveau projet.

## Fonctionnalités

- **Actualités** : articles classés par catégories, menu de navigation généré
  automatiquement, pagination.
- **Pages personnalisables** : pages éditables dans l'admin dont le rendu et
  la logique métier sont assurés par des **types de page** plug-gables en
  PHP (contenu simple, contact, équipe, partenaires, agenda, presse).
  Ajouter votre propre type = une classe + un template.
- **Équipe** : annuaire des membres (rôle, biographie, photo, ordre
  d'affichage).
- **Agenda** : liste des rendez-vous avec import Excel.
- **Revue de presse** : mentions presse avec récupération automatique des
  logos des médias.
- **Paramètres de l'association** : adresse, contact, réseaux sociaux.
- **Administration** : EasyAdmin, gestion des utilisateurs et rôles.
- **Module LinkedIn optionnel** : import automatique des posts d'une page
  LinkedIn (désactivé si non configuré).
- **Imports de contenu** : commande CLI CSV/JSON pour migrer vos données
  (articles, catégories, presse, utilisateurs…).

## Stack technique

| Composant | Version |
| --- | --- |
| PHP | 8.4 |
| Symfony | 7.4 |
| PostgreSQL | 18 |
| EasyAdmin | 5 |
| Twig + Bootstrap 5 + Asset Mapper (importmap) | — |
| Docker Compose (php-fpm, nginx, postgres, mailpit) | — |

## Démarrage rapide

Prérequis : Docker, Docker Compose et Make.

```bash
cp .env.example .env   # ajustez si besoin (port, nom du site, base de données)
make install           # build des images + composer + assets + démarrage
```

Puis, pour peupler le site de démonstration :

```bash
make connect
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load
```

| Service | URL |
| --- | --- |
| Application (dev) | http://localhost:8091 |
| Administration | http://localhost:8091/admin |
| Mailpit | http://localhost:1180 |

Créer un administrateur :

```bash
php bin/console app:user:create-admin --email=admin@example.com --password=secret
```

## Personnalisation

- **Nom du site** : variable `SITE_NAME` (disponible dans les templates via
  `site_name`).
- **Logo & favicon** : remplacez `public/images/logo.svg` et
  `public/images/favicon.svg`.
- **Apparence** : `assets/styles/app.css` et `templates/base.html.twig`
  (polices, couleurs Bootstrap…).
- **Ajouter un type de page** : implémentez `App\Page\PageTypeInterface`
  (ou étendez `App\Page\AbstractPageType`), ajoutez un template dans
  `templates/special_page/type/` — l'admin le propose automatiquement.

## Développement

| Commande | Description |
| --- | --- |
| `make start` / `make stop` | Démarrage / arrêt des containers |
| `make connect` | Shell dans le container PHP |
| `make clear` | Vide le cache Symfony |
| `make migrations` | Exécute les migrations |
| `make tests` | Base de test + migrations + PHPUnit |
| `make quality` | PHPStan (niveau 6) + PHPCS (PSR-12) |
| `make composer-update` | Mise à jour des vendors |

Imports de contenu :

```bash
php bin/console app:import:content categories /path/to/categories.csv
php bin/console app:import:content articles /path/to/articles.json
php bin/console app:import:content press_mentions /path/to/presse.csv --dry-run
```

## Module LinkedIn (optionnel)

Le module posts LinkedIn fonctionne sans configuration (saisie manuelle des
liens d'intégration dans l'admin). Pour activer l'import automatique via
l'API, renseignez dans `.env.local` :

```
LINKEDIN_ACCESS_TOKEN=...
LINKEDIN_ORGANIZATION_ID=...
```

## Contribuer

Les contributions sont bienvenues ! Ouvrez une issue ou une pull request.
Pensez à faire passer `make quality` et `make tests` avant de soumettre.

## Licence

[MIT](LICENSE)
