# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Lancer en local

```bash
# Pour le formulaire PHP (contact.php) :
php -S localhost:8000

# Sans PHP :
python3 -m http.server 8080
```

Ouvrir `http://localhost:8000`. Pas de build, pas de bundler, pas de dépendances.

## Architecture CSS — ordre de chargement obligatoire

Les feuilles doivent être chargées dans cet ordre :

1. `tokens.css` — toutes les custom properties (`--accent`, `--glass-bg`, `--nav-h`, etc.)
2. `base.css` — reset, typographie, `.container`, `.page-scroller`
3. `components.css` — `.glass`, `.btn`, `.tag`, `.avatar`, `.filter-btn`
4. `layout.css` — nav, sections hero/about/projects/skills/contact, bento grid
5. `animations.css` — `.reveal`, `@keyframes`, fallbacks `@supports`

Les pages de détail projet ajoutent `project-detail.css` en 5e position (à la place de layout.css qu'elles n'utilisent pas).

## Scroll snap — architecture critique

- Le snap est sur `<main class="page-scroller">`, **pas** sur `html` (bug Chromium avec le scroll bidirectionnel).
- `html[data-snap]` et `html[data-snap] body` ont `overflow: hidden` — cet attribut n'est présent que sur `index.html`.
- Les pages de détail projet (`projects/*/index.html`) n'ont **pas** `data-snap` et scrollent normalement.
- Ne pas ajouter `overscroll-behavior-y: contain` sur `.section-scroll` : ça bloque le scroll vers le haut.

## JS — règle IIFE

Tout JS qui a besoin d'un `return` anticipé (ex. `if (!form) return`) doit être enveloppé dans une IIFE :

```js
(() => {
  const form = document.getElementById('contact-form');
  if (!form) return;
  // ...
})();
```

Un `return` au top-level d'un `<script>` est une SyntaxError dans les navigateurs.

## Ajouter un projet

```bash
cp -r projects/_template/ projects/mon-projet/
```

Puis dans `projects/mon-projet/index.html` : remplacer tous les `[placeholders]` et ajouter les images dans `images/` (`cover.jpg` en premier, ensuite `01.jpg`, `02.jpg`…).

Dans `index.html`, ajouter une `<article class="project-card glass" data-category="web|creation|autre">` dans la section `#projects` avec un lien `href="projects/mon-projet/"`.

Catégories disponibles : `web`, `creation`, `autre`.

## Formulaire contact

`contact.php` répond uniquement aux POST et retourne du JSON `{ ok: true }` ou `{ ok: false, errors: [...] }`. Nécessite un hébergement PHP avec `mail()` activé (pas fonctionnel en local sans serveur mail configuré).

`js/contact.js` gère la soumission AJAX, les états loading/success/error, et affiche les messages dans `.form-status`.

## Couleur d'accent

Modifier `--accent` et `--accent-hover` dans `css/tokens.css` — la couleur se propage partout.

## Contenu à compléter

Chercher `[À compléter` dans `index.html` pour trouver les zones de contenu vides (bio, tagline, stats, compétences, liens sociaux). Placer le CV dans `assets/cv-hugo-geay.pdf` et la photo de profil dans `assets/photo.jpg`.
