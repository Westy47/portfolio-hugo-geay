# Portfolio — Hugo Geay

Portfolio personnel en HTML/CSS/JS pur. Aucune dépendance, aucun build.

## Lancer en local

Ouvrir `index.html` directement dans le navigateur, **ou** via un serveur local :

```bash
npx serve .
# ou
python3 -m http.server 8080
```

Puis ouvrir `http://localhost:8080`.

## Éditer le contenu

Tout le contenu est dans `index.html`. Chercher les balises `[À compléter …]` :

| Zone              | Chercher dans `index.html`  |
|-------------------|-----------------------------|
| Tagline hero      | `hero-tagline`              |
| Bio               | `about-text`                |
| Projets (×3)      | `project-card`              |
| Compétences       | `skills-grid`               |
| Email / réseaux   | `contact-card`              |

## Ajouter des images de projets

1. Placer les fichiers dans `assets/` (`.webp` recommandé)
2. Remplacer le `<div class="img-placeholder">` par :

```html
<img src="assets/mon-projet.webp"
     alt="Description du projet"
     loading="lazy"
     width="800" height="450">
```

## Personnaliser la couleur d'accent

Dans `css/tokens.css`, modifier `--accent` et `--accent-hover`. La couleur se propage partout (boutons, tags, liens, focus).

## Architecture

```
portfolio-hugo/
├── index.html          ← contenu et structure
├── css/
│   ├── tokens.css      ← custom properties (couleurs, verre, espacement)
│   ├── base.css        ← reset, typographie, helpers
│   ├── components.css  ← .glass, .btn, .tag, .avatar, .img-placeholder
│   ├── layout.css      ← nav, hero, about, projects, skills, contact
│   └── animations.css  ← reveal scroll, hover cartes, fallbacks
├── js/
│   ├── nav.js          ← densification nav au scroll
│   └── animations.js   ← IntersectionObserver scroll reveal
└── assets/             ← portrait, captures projets
```
