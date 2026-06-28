# Logiloc Immo - Site Web Agence Immobilière

Site web institutionnel et commercial pour une agence immobilière spécialisée dans la location, la gestion locative, les transactions immobilières et les services connexes.

## Fonctionnalités

### Services Principaux
- **Location d'appartements meublés** : Courte, moyenne et longue durée
- **Gestion locative** : Gestion complète de biens immobiliers
- **Transaction immobilière** : Achat, vente et location
- **Syndic de copropriété** : Gestion professionnelle de copropriété
- **Évaluation immobilière** : Estimation précise des biens
- **Aménagement et promotion immobilière** : Conception et réalisation de projets
- **Conseil et accompagnement immobilier** : Expert-conseil personnalisé

### Fonctionnalités du Site
- Présentation institutionnelle de l'agence
- Catalogue d'appartements avec filtrage par durée
- Système de réservation en ligne (via email)
- Génération de leads via formulaire de contact
- Inscription newsletter
- Design responsive (mobile, tablette, desktop)
- Couleurs : Bleu foncé, Jaune et Blanc (conforme au logo)
- Design moderne avec animations et effets visuels

## Technologies Utilisées

- **Frontend** : HTML5, CSS3, JavaScript (Vanilla)
- **Librairies** : Font Awesome (icônes), Google Fonts (Poppins)
- **Images** : Unsplash (images placeholder de haute qualité)

## Structure du Projet

```
gestion_immoblière/
├── index.html                     # Page d'accueil principale
├── Design sans titre (4) (1).svg  # Logo de l'entreprise
├── css/
│   └── style.css                  # Feuille de style principale
├── js/
│   └── main.js                    # JavaScript pour les interactions
├── images/
│   ├── appartements/              # Dossier pour les images d'appartements
│   └── uploads/                   # Dossier pour les fichiers uploadés
└── README.md                      # Ce fichier
```

## Installation

### Prérequis
- Navigateur web moderne (Chrome, Firefox, Safari, Edge)
- Aucun serveur requis (site statique HTML/CSS/JS)

### Étapes d'Installation

1. **Copier les fichiers**
   - Copiez tous les fichiers du projet dans votre dossier web ou ouvrez simplement `index.html` dans votre navigateur

2. **Personnaliser le logo**
   - Le logo actuel est `Design sans titre (4) (1).svg`
   - Remplacez-le par votre propre logo si nécessaire

3. **Personnaliser les images**
   - Les images actuelles proviennent d'Unsplash (placeholder)
   - Remplacez les URLs d'images par vos propres images dans `index.html`
   - Ajoutez vos images dans le dossier `images/appartements/`

4. **Personnaliser les informations de contact**
   - Modifiez l'email dans les formulaires (actuellement `contact@immogestionpro.fr`)
   - Mettez à jour l'adresse, téléphone et autres informations dans la section contact

5. **Tester le site**
   - Ouvrez `index.html` dans votre navigateur
   - Testez les formulaires de réservation et de contact (ils utilisent mailto:)

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
- Modifiez le texte dans `index.html` pour adapter le contenu à votre agence
- Mettez à jour les informations de contact dans la section footer
- Personnalisez les services selon vos offres
- Modifiez les appartements dans la section "Nos Appartements"

### Logo
- Le logo est intégré via le fichier `Design sans titre (4) (1).svg`
- Pour changer le logo, remplacez ce fichier ou modifiez les chemins dans `index.html`

## Fonctionnalités Interactives

- Navigation responsive avec menu hamburger sur mobile
- Filtrage des appartements par durée (courte, moyenne, longue)
- Animations au survol des cartes et boutons
- Effets de shimmer et pulse sur les sections hero et réservation
- Logo flottant en bas à droite pour retour en haut de page
- Formulaires avec validation JavaScript

## Support

Pour toute question ou problème technique, contactez :
- Email : contact@logilocimmo.fr
- Téléphone : +33 1 23 45 67 89

## Licence

Ce projet est propriété de Logiloc Immo. Tous droits réservés.

---

**Version** : 2.0.0 (Version HTML Statique)  
**Date de création** : Avril 2024  
**Dernière mise à jour** : Avril 2024
