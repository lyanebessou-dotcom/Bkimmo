# Logiloc Immo - Application Web de Gestion Immobilière

Application web complète pour une agence immobilière spécialisée dans la location, la gestion locative, les transactions immobilières et les services connexes. Développée pour être déployée sur Infinity Free.

## Fonctionnalités

### Services Principaux
- **Location d'appartements meublés** : Courte, moyenne et longue durée
- **Gestion locative** : Gestion complète de biens immobiliers
- **Transaction immobilière** : Achat, vente et location
- **Syndic de copropriété** : Gestion professionnelle de copropriété
- **Évaluation immobilière** : Estimation précise des biens
- **Aménagement et promotion immobilière** : Conception et réalisation de projets
- **Conseil et accompagnement immobilier** : Expert-conseil personnalisé

### Fonctionnalités de l'Application
- Catalogue d'appartements dynamique depuis la base de données
- Système d'authentification (inscription/connexion)
- Panel d'administration pour gérer les biens et réservations
- Système de réservation en ligne
- Gestion des utilisateurs et des services
- Design responsive (mobile, tablette, desktop)
- Couleurs : Bleu foncé, Jaune et Blanc (conforme au logo)

## Technologies Utilisées

- **Backend** : PHP 7.4+
- **Base de données** : MySQL (via Infinity Free)
- **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
- **Librairies** : Font Awesome (icônes), Google Fonts (Poppins)
- **Images** : Unsplash (images placeholder de haute qualité)

## Structure du Projet

```
Bkimmo/
├── config.php                    # Configuration de la base de données
├── index.php                     # Page d'accueil principale
├── login.php                     # Page de connexion
├── register.php                  # Page d'inscription
├── admin.php                     # Panel d'administration
├── logout.php                    # Déconnexion
├── database.sql                  # Script SQL pour créer les tables
├── Design sans titre (4) (1).svg  # Logo de l'entreprise
├── css/
│   ├── style.css                 # Feuille de style principale
│   └── admin.css                 # Styles pour l'admin
├── js/
│   ├── main.js                   # JavaScript principal
│   ├── auth.js                   # JavaScript pour l'authentification
│   └── admin.js                  # JavaScript pour l'admin
├── api/
│   ├── biens.php                 # API pour les biens
│   ├── auth.php                  # API pour l'authentification
│   └── reservations.php          # API pour les réservations
├── images/
│   ├── appartements/             # Dossier pour les images d'appartements
│   └── uploads/                  # Dossier pour les fichiers uploadés
└── README.md                     # Ce fichier
```

## Déploiement sur Infinity Free

### Étape 1 : Créer un compte Infinity Free

1. Allez sur [https://infinityfree.net/](https://infinityfree.net/)
2. Créez un compte gratuit
3. Créez un nouveau site web

### Étape 2 : Configurer la base de données

1. Connectez-vous à votre panel Infinity Free
2. Allez dans **MySQL Databases**
3. Créez une nouvelle base de données
4. Notez les informations suivantes :
   - **MySQL Host** (ex: sqlxxx.infinityfree.com)
   - **Username** (ex: if0_xxxxxxxx)
   - **Password** (celui que vous avez défini)
   - **Database Name** (ex: if0_xxxxxxxx_logiloc_immo)

### Étape 3 : Importer la base de données

1. Allez dans **phpMyAdmin** depuis le panel Infinity Free
2. Sélectionnez votre base de données
3. Cliquez sur l'onglet **Import**
4. Sélectionnez le fichier `database.sql`
5. Cliquez sur **Exécuter**

### Étape 4 : Configurer le fichier config.php

Ouvrez le fichier `config.php` et remplacez les valeurs par vos informations Infinity Free :

```php
define('DB_HOST', 'sqlxxx.infinityfree.com'); // Votre MySQL Host
define('DB_USER', 'if0_xxxxxxxx'); // Votre Username MySQL
define('DB_PASS', 'votre_mot_de_passe'); // Votre mot de passe MySQL
define('DB_NAME', 'if0_xxxxxxxx_logiloc_immo'); // Votre nom de base de données
define('SITE_URL', 'https://votre-site.infinityfreeapp.com'); // Votre URL de site
```

### Étape 5 : Uploader les fichiers

1. Allez dans **Online File Manager** depuis le panel Infinity Free
2. Uploader tous les fichiers du projet dans le dossier `htdocs`
3. Assurez-vous que la structure des dossiers est conservée

### Étape 6 : Tester le déploiement

1. Ouvrez votre site dans le navigateur
2. Testez l'inscription : `https://votre-site.infinityfreeapp.com/register.php`
3. Testez la connexion : `https://votre-site.infinityfreeapp.com/login.php`
4. Connectez-vous avec le compte admin par défaut :
   - Email : `admin@logilocimmo.fr`
   - Mot de passe : `password` (à changer après première connexion)

### Étape 7 : Changer le mot de passe admin

Connectez-vous à phpMyAdmin et exécutez cette requête SQL pour changer le mot de passe admin :

```sql
UPDATE utilisateurs SET password = '$2y$10$NouveauHashIci' WHERE email = 'admin@logilocimmo.fr';
```

Remplacez `$2y$10$NouveauHashIci` par le hash de votre nouveau mot de passe (utilisez `password_hash('votre_mot_de_passe', PASSWORD_DEFAULT)` en PHP pour le générer).

## Personnalisation

### Couleurs
Les couleurs sont définies dans `css/style.css` dans les variables CSS :
```css
:root {
    --primary-blue: #0a1628;
    --secondary-blue: #1e3a5f;
    --accent-blue: #2563eb;
    --primary-yellow: #d97706;
    --secondary-yellow: #b45309;
    --white: #ffffff;
}
```

### Contenu
- Modifiez le texte dans `index.php` pour adapter le contenu à votre agence
- Mettez à jour les informations de contact dans la section footer
- Personnalisez les services selon vos offres
- Ajoutez/modifiez les appartements via le panel admin ou directement dans la base de données

### Logo
- Le logo est intégré via le fichier `Design sans titre (4) (1).svg`
- Pour changer le logo, remplacez ce fichier ou modifiez les chemins dans les fichiers PHP

## Fonctionnalités Interactives

- Navigation responsive avec menu hamburger sur mobile
- Filtrage des appartements par durée (courte, moyenne, longue)
- Animations au survol des cartes et boutons
- Système d'authentification sécurisé avec sessions PHP
- Panel d'administration avec statistiques en temps réel
- API REST pour la gestion des données

## Support

Pour toute question ou problème technique, contactez :
- Email : contact@logilocimmo.fr
- Téléphone : +33 1 23 45 67 89

## Sécurité

- Les mots de passe sont hashés avec `password_hash()` (bcrypt)
- Les entrées utilisateur sont nettoyées avec `htmlspecialchars()`
- Les requêtes SQL utilisent des statements préparés
- Protection contre les attaques CSRF et XSS

## Licence

Ce projet est propriété de Logiloc Immo. Tous droits réservés.

---

**Version** : 3.0.0 (Version PHP/MySQL pour Infinity Free)  
**Date de création** : Avril 2024  
**Dernière mise à jour** : Juillet 2024
