---
name: portfolio-apple-glass
description: >
  Crée DE ZÉRO un portfolio web dev + créatif dans une esthétique Apple/macOS avec
  glassmorphisme léger. Utilise cette skill dès que l'utilisateur veut construire son
  portfolio, son site perso, sa page "à propos" ou sa grille de projets à partir de
  rien, ou demande un style "Apple", "macOS", "glass", "glassmorphisme", "verre
  dépoli", "translucide", "épuré", "premium" — même formulé "fais-moi un portfolio"
  ou "monte-moi un site perso propre". La skill commence TOUJOURS par une courte phase
  de cadrage (elle pose des questions), puis échafaude le projet et applique la couche
  design.
---

# Portfolio — Apple / macOS + Glassmorphisme (from scratch)

Tu construis **de zéro** un portfolio personnel pour un profil
**développeur ET créatif**. Objectif : un site qui respire la qualité « produit
Apple » — sobre, spacieux, typographie nette — rehaussé d'un **glassmorphisme léger**
(panneaux translucides, flou d'arrière-plan, bordures fines lumineuses). Référence
d'ambiance : portfolios UI/UX premium type Behance, fond clair, profondeur subtile.

L'erreur à éviter absolument : le rendu « template IA générique » (dégradé
violet/indigo par défaut, hero centré vide, cartes plates sans hiérarchie, emojis
décoratifs). On s'engage à fond dans l'esthétique choisie.

---

## Étape 0 — Cadrage (poser les questions AVANT de coder)

Projet vierge → on ne devine pas, on demande. Pose ces questions à l'utilisateur,
regroupées et concises. S'il a déjà répondu à certaines plus tôt dans la conversation,
ne les repose pas. S'il dit « surprends-moi / propose », prends des défauts sensés
(notés ci-dessous) et avance.

**A. Identité & contenu** (le minimum pour un vrai portfolio, pas du lorem ipsum)

- Nom + titre (ex. « Développeur & Designer »)
- Bio courte (2–4 phrases)
- Projets : pour chacun → titre, description, technos/outils, lien live + repo,
  visuel/capture si dispo
- Compétences (langages / outils / design)
- Contacts & réseaux (mail, GitHub, LinkedIn, Behance…)

**B. Choix techniques**

- Stack : HTML/CSS/JS pur (défaut si pas d'avis — facile à héberger), ou
  React/Vite, Next, Astro, Svelte…
- Structure : une page à scroll (défaut) ou multi-pages
- Mode sombre en plus du clair ? (défaut : clair + bascule sombre auto)

**C. Direction visuelle**

- Couleur d'accent (une seule, tenue partout) — défaut : bleu Apple `#0071e3`
- Portrait / logo à intégrer ? (sinon, placeholders élégants)

Si du contenu manque, prévois des **placeholders propres et explicitement marqués**
(`[À compléter]`) plutôt que d'inventer — l'utilisateur les remplira.

Une fois les réponses obtenues, récapitule brièvement le plan (stack + structure +
sections) puis lance l'échafaudage.

---

## Étape 1 — Échafaudage du projet

- Initialise la stack choisie proprement (ex. `npm create vite@latest` pour React,
  ou un simple `index.html` + `styles.css` + `main.js` pour le HTML pur).
- Mets en place le point d'entrée des styles globaux et **injecte les tokens
  ci-dessous une seule fois** (custom properties CSS, thème Tailwind…), pas de
  valeurs en dur dispersées.
- Crée une structure de composants/sections claire (voir « Structure des sections »).
- Ajoute un README court : comment lancer en local, où mettre les images, où éditer
  le contenu.

---

## Direction artistique

### Principes Apple/macOS

- **Espace négatif généreux.** Marges larges, sections aérées.
- **Typographie comme matière première.** Titres larges, graisses contrastées,
  `letter-spacing` légèrement négatif sur les gros titres (`-0.02em` à `-0.03em`).
- **Hiérarchie claire** plutôt que décoration. Peu de couleurs, beaucoup de nuances.
- **Mouvement discret et « springy ».** Transitions douces, jamais clinquantes.
- **Cohérence obsessionnelle** des rayons, ombres et espacements.

### Glassmorphisme léger (la signature)

Les panneaux de verre n'ont de sens que posés sur un fond qui a de la matière
(léger dégradé ou mesh coloré très subtil) — sinon le flou ne se voit pas.

- Fond translucide : `rgba(255,255,255,0.6)` en clair / `rgba(22,22,24,0.5)` en sombre
- Flou : `backdrop-filter: blur(20px) saturate(180%)` (+ `-webkit-` pour Safari/iOS)
- Bordure fine lumineuse : `1px solid rgba(255,255,255,0.18)`
- Ombre douce + léger highlight interne en haut du panneau
- Usage parcimonieux : barre de nav, cartes projet, modales. Pas TOUT en verre.

---

## Design tokens (à adapter à la stack)

```css
:root {
  /* Surfaces — light (défaut) */
  --bg: #f5f5f7; /* gris Apple */
  --bg-gradient: radial-gradient(
    120% 120% at 50% 0%,
    #ffffff 0%,
    #f5f5f7 45%,
    #ececf0 100%
  );
  --text: #1d1d1f;
  --text-soft: #6e6e73;
  --accent: #0071e3; /* défini à l'étape de cadrage */

  /* Verre */
  --glass-bg: rgba(255, 255, 255, 0.6);
  --glass-border: rgba(255, 255, 255, 0.5);
  --glass-blur: 20px;

  /* Forme & profondeur */
  --radius-lg: 22px; /* cartes */
  --radius-md: 14px; /* boutons, tags */
  --shadow-soft: 0 8px 30px rgba(0, 0, 0, 0.06);
  --shadow-card: 0 16px 40px rgba(0, 0, 0, 0.08);

  /* Rythme */
  --section-pad: clamp(64px, 10vw, 140px);
  --ease: cubic-bezier(0.22, 1, 0.36, 1); /* "spring" doux */

  /* Type */
  --font:
    -apple-system, BlinkMacSystemFont, "SF Pro Display", "Inter", system-ui,
    sans-serif;
}

/* Mode sombre — activer selon le choix de cadrage */
@media (prefers-color-scheme: dark) {
  :root {
    --bg: #000000;
    --bg-gradient: radial-gradient(120% 120% at 50% 0%, #1c1c1e 0%, #000 60%);
    --text: #f5f5f7;
    --text-soft: #98989d;
    --glass-bg: rgba(28, 28, 30, 0.5);
    --glass-border: rgba(255, 255, 255, 0.12);
  }
}

.glass {
  background: var(--glass-bg);
  -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(180%);
  backdrop-filter: blur(var(--glass-blur)) saturate(180%);
  border: 1px solid var(--glass-border);
  border-radius: var(--radius-lg);
  box-shadow:
    var(--shadow-card),
    inset 0 1px 0 rgba(255, 255, 255, 0.4); /* highlight haut */
}
```

---

## Structure des sections

Portfolio dev + créatif, par défaut en **une page à scroll** :

1. **Nav** — barre fine en verre, sticky, qui se densifie au scroll.
2. **Hero** — nom + titre, tagline courte, 1–2 CTA (voir projets / contact).
   Fond dégradé subtil + éventuel orbe flou en arrière-plan pour donner de la matière
   au verre.
3. **À propos** — bio courte, portrait optionnel, dans un panneau de verre.
4. **Projets** — grille de cartes de verre : visuel/capture, titre, description,
   tags techno, liens (live + repo). Hover : légère élévation + `scale(1.02)`.
   Placeholders élégants si une image manque.
5. **Compétences** — regroupées (langages / outils / design), pastilles discrètes.
6. **Contact / footer** — liens réseaux (mail, GitHub, LinkedIn, Behance…).

---

## Animations

- Apparition au scroll : `opacity` + `translateY(20px)` → 0, via IntersectionObserver
  (ou Framer Motion / `@motionone` selon la stack). Stagger léger entre les cartes.
- Hover cartes : transition `transform` + `box-shadow` sur `var(--ease)`, ~200ms.
- **Respecte `prefers-reduced-motion`** : coupe les animations si demandé.
- Jamais de carrousel auto-play tape-à-l'œil ni de parallaxe agressive.

---

## Responsive & accessibilité

- Mobile-first. La grille projets passe en 1 colonne, le verre reste lisible
  (contraste du texte sur fond translucide).
- Fallback glassmorphisme : `@supports not (backdrop-filter: blur(1px))` →
  fond plus opaque.
- Contraste texte AA minimum. `--text-soft` ne descend pas trop bas sur le verre.
- Navigation clavier complète, focus visibles (ring discret cohérent avec l'accent).
- Images : `alt` réels, `loading="lazy"`, dimensions définies.

---

## Checklist avant de livrer

- [ ] Phase de cadrage faite : contenu réel récupéré, choix techniques et accent fixés
- [ ] Projet échafaudé proprement + README (lancer en local, où éditer le contenu)
- [ ] Tokens injectés une seule fois au bon endroit
- [ ] Le verre est posé sur un fond avec matière (sinon le flou est invisible)
- [ ] Préfixe `-webkit-backdrop-filter` présent (Safari / iPhone)
- [ ] `prefers-reduced-motion` et fallback `@supports` gérés
- [ ] Une seule couleur d'accent, tenue partout
- [ ] Placeholders `[À compléter]` là où le contenu manque (jamais de faux contenu inventé)
- [ ] Aucun marqueur « template IA » (dégradé violet par défaut, hero vide, emojis déco)
- [ ] Rendu vérifié en clair ET (si activé) sombre, desktop + mobile
