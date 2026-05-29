<?php
session_start();
require __DIR__ . '/config.php';

$error = '';
$success = '';

function slug(string $s): string {
    $s = mb_strtolower($s);
    $s = strtr($s, ['à'=>'a','â'=>'a','ä'=>'a','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
                     'î'=>'i','ï'=>'i','ô'=>'o','ö'=>'o','ù'=>'u','û'=>'u','ü'=>'u',
                     'ç'=>'c','æ'=>'ae','œ'=>'oe',' '=>'-',"'"=>'-']);
    return preg_replace('/[^a-z0-9\-]/', '', $s);
}

function icon(string $tech): string {
    $map = [
        'php'        => 'https://cdn.simpleicons.org/php/005e7c',
        'mysql'      => 'https://cdn.simpleicons.org/mysql/005e7c',
        'javascript' => 'https://cdn.simpleicons.org/javascript/005e7c',
        'html'       => 'https://cdn.simpleicons.org/html5/005e7c',
        'css'        => 'https://cdn.simpleicons.org/css/005e7c',
        'wordpress'  => 'https://cdn.simpleicons.org/wordpress/005e7c',
        'woocommerce'=> 'https://cdn.simpleicons.org/woocommerce/005e7c',
        'figma'      => 'https://cdn.simpleicons.org/figma/005e7c',
        'git'        => 'https://cdn.simpleicons.org/git/005e7c',
        'github'     => 'https://cdn.simpleicons.org/github/005e7c',
        'photoshop'  => 'assets/icons/photoshop.svg',
        'illustrator'=> 'assets/icons/illustrator.svg',
        'indesign'   => 'assets/icons/indesign.svg',
        'premiere'   => 'assets/icons/premierepro.svg',
        'blender'    => 'https://cdn.simpleicons.org/blender/005e7c',
        'davinci'    => 'https://cdn.simpleicons.org/davinciresolve/005e7c',
    ];
    $key = strtolower(trim($tech));
    foreach ($map as $k => $url) {
        if (str_contains($key, $k)) return $url;
    }
    return '';
}

function tagsHtml(string $raw, string $prefix = ''): string {
    $tags = array_filter(array_map('trim', explode(',', $raw)));
    $html = '';
    foreach ($tags as $tag) {
        $ico = icon($tag);
        $img = $ico ? "<img src=\"{$prefix}{$ico}\" alt=\"\" width=\"14\" height=\"14\" aria-hidden=\"true\">" : '';
        $html .= "                    <span class=\"tag\">{$img}" . htmlspecialchars($tag) . "</span>\n";
    }
    return $html;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'login') {
        if ($_POST['user'] === ADMIN_USER && password_verify($_POST['pass'], ADMIN_PASS)) {
            $_SESSION['admin'] = true;
            header('Location: index.php');
            exit;
        }
        $error = 'Identifiants incorrects.';
    }

    if ($_POST['action'] === 'publish' && !empty($_SESSION['admin'])) {
        $title      = trim(strip_tags($_POST['title'] ?? ''));
        $category   = in_array($_POST['category'] ?? '', ['web','creation','autre']) ? $_POST['category'] : 'web';
        $short_desc = trim(strip_tags($_POST['short_desc'] ?? ''));
        $long_desc  = trim(strip_tags($_POST['long_desc'] ?? ''));
        $tags_raw   = trim($_POST['tags'] ?? '');
        $live_url   = trim($_POST['live_url'] ?? '');
        $github_url = trim($_POST['github_url'] ?? '');

        if (!$title || !$short_desc || !$long_desc) {
            $error = 'Titre, description courte et description longue sont obligatoires.';
        } else {
            $s = slug($title);
            $dir = PROJ_DIR . $s . '/';
            $img_dir = $dir . 'images/';

            if (is_dir($dir)) {
                $error = "Le dossier « {$s} » existe déjà.";
            } else {
                mkdir($img_dir, 0755, true);

                $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
                $cover_file = '';
                $extra_files = [];

                if (!empty($_FILES['cover']['name'])) {
                    $f = $_FILES['cover'];
                    if (in_array($f['type'], $allowed)) {
                        $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                        $cover_file = "cover.{$ext}";
                        move_uploaded_file($f['tmp_name'], $img_dir . $cover_file);
                    }
                }

                if (!empty($_FILES['images']['name'][0])) {
                    $i = 1;
                    foreach ($_FILES['images']['tmp_name'] as $k => $tmp) {
                        if (!$tmp) continue;
                        $type = $_FILES['images']['type'][$k];
                        if (!in_array($type, $allowed)) continue;
                        $ext = pathinfo($_FILES['images']['name'][$k], PATHINFO_EXTENSION);
                        $fname = str_pad($i, 2, '0', STR_PAD_LEFT) . ".{$ext}";
                        move_uploaded_file($tmp, $img_dir . $fname);
                        $extra_files[] = $fname;
                        $i++;
                    }
                }

                $cat_label = ['web'=>'Développement web','creation'=>'Création numérique','autre'=>'Autre'][$category];
                $tags_detail = tagsHtml($tags_raw, '../../');

                $live_btn = $live_url
                    ? "<a href=\"" . htmlspecialchars($live_url) . "\" class=\"btn btn-primary\" target=\"_blank\" rel=\"noopener\">Voir le site →</a>\n"
                    : '';
                $github_btn = $github_url
                    ? "          <a href=\"" . htmlspecialchars($github_url) . "\" class=\"btn btn-secondary\" target=\"_blank\" rel=\"noopener\">GitHub →</a>\n"
                    : '';

                $cover_img = $cover_file
                    ? "<img src=\"images/{$cover_file}\" alt=\"" . htmlspecialchars($title) . " — vue principale\" class=\"mosaic-img\" loading=\"eager\">"
                    : '';
                $extra_imgs = '';
                foreach ($extra_files as $ef) {
                    $extra_imgs .= "        <img src=\"images/{$ef}\" alt=\"\" class=\"mosaic-img\" loading=\"lazy\">\n";
                }

                $detail_html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{$short_desc} — Hugo Geay">
  <title>{$title} — Hugo Geay</title>
  <link rel="stylesheet" href="../../css/tokens.css">
  <link rel="stylesheet" href="../../css/base.css">
  <link rel="stylesheet" href="../../css/components.css">
  <link rel="stylesheet" href="../../css/animations.css">
  <link rel="stylesheet" href="../../css/project-detail.css">
</head>
<body>
  <nav class="glass">
    <div class="container nav-inner">
      <a href="../../index.html" class="nav-logo">HG</a>
      <a href="../../index.html#projects" class="back-link">← Projets</a>
    </div>
  </nav>
  <main class="detail-main">
    <div class="container">
      <header class="detail-header">
        <span class="detail-category">{$cat_label}</span>
        <h1>{$title}</h1>
        <p class="detail-desc">{$long_desc}</p>
        <div class="detail-meta">
          <div class="detail-tags">
{$tags_detail}          </div>
          <div class="detail-links">
{$live_btn}{$github_btn}          </div>
        </div>
      </header>
      <div class="detail-mosaic">
        {$cover_img}
{$extra_imgs}      </div>
      <footer class="detail-footer">
        <a href="../../index.html#projects" class="back-link">← Retour aux projets</a>
      </footer>
    </div>
  </main>
  <script src="../../js/nav.js"></script>
  <script src="../../js/lightbox.js"></script>
</body>
</html>
HTML;

                file_put_contents($dir . 'index.html', $detail_html);

                $cover_src = $cover_file ? "projects/{$s}/images/{$cover_file}" : 'https://picsum.photos/seed/' . $s . '/800/450';
                $tags_card = tagsHtml($tags_raw);
                $live_link_card = $live_url
                    ? "                    <a href=\"" . htmlspecialchars($live_url) . "\" class=\"project-link\" target=\"_blank\" rel=\"noopener\">Live →</a>\n"
                    : '';
                $github_link_card = $github_url
                    ? "                    <a href=\"" . htmlspecialchars($github_url) . "\" class=\"project-link\" target=\"_blank\" rel=\"noopener\">GitHub →</a>\n"
                    : '';

                $card = <<<CARD

              <article class="project-card glass reveal" data-category="{$category}">
                <a href="projects/{$s}/index.html">
                  <img src="{$cover_src}" alt="{$title}" class="project-img" loading="lazy" width="800" height="450">
                </a>
                <div class="project-card-body">
                  <h3><a href="projects/{$s}/index.html" class="project-title-link">{$title}</a></h3>
                  <p>{$short_desc}</p>
                  <div class="project-tags">
{$tags_card}                  </div>
                  <div class="project-links">
                    <a href="projects/{$s}/index.html" class="project-link">Détails →</a>
{$live_link_card}{$github_link_card}                  </div>
                </div>
              </article>
CARD;

                $index = file_get_contents(INDEX);
                $index = str_replace(MARKER, $card . "\n" . MARKER, $index);
                file_put_contents(INDEX, $index);

                $success = "Projet « {$title} » publié avec succès !";
            }
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

$logged = !empty($_SESSION['admin']);

$projects = [];
if ($logged && is_dir(PROJ_DIR)) {
    foreach (scandir(PROJ_DIR) as $d) {
        if ($d[0] === '_' || $d[0] === '.') continue;
        $f = PROJ_DIR . $d . '/index.html';
        if (!is_file($f)) continue;
        $html = file_get_contents($f);
        preg_match('/<h1>(.*?)<\/h1>/s', $html, $m);
        $projects[] = ['slug' => $d, 'title' => strip_tags($m[1] ?? $d)];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — Portfolio Hugo Geay</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --accent: #005e7c;
      --accent-h: #004d66;
      --bg: #0f172a;
      --surface: #1e293b;
      --border: #334155;
      --text: #f1f5f9;
      --muted: #94a3b8;
      --red: #ef4444;
      --green: #22c55e;
      --radius: 10px;
    }
    body { font-family: system-ui, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
    a { color: var(--accent); text-decoration: none; }
    a:hover { color: #38bdf8; }

    .topbar {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      padding: .875rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .topbar strong { font-size: 1rem; letter-spacing: -.02em; }
    .topbar-links { display: flex; gap: 1.25rem; font-size: .85rem; }

    .wrap { max-width: 820px; margin: 0 auto; padding: 2.5rem 1.5rem 4rem; }

    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 2rem;
    }
    h1 { font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem; }
    h2 { font-size: 1.1rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; font-size: .75rem; }

    .alert { padding: .75rem 1rem; border-radius: 6px; font-size: .9rem; margin-bottom: 1.5rem; }
    .alert-error { background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.4); color: #fca5a5; }
    .alert-success { background: rgba(34,197,94,.15); border: 1px solid rgba(34,197,94,.4); color: #86efac; }

    .field { margin-bottom: 1.1rem; }
    label { display: block; font-size: .8rem; font-weight: 600; color: var(--muted); margin-bottom: .4rem; }
    input[type=text], input[type=url], input[type=password], textarea, select {
      width: 100%; background: var(--bg); border: 1px solid var(--border);
      border-radius: 6px; padding: .6rem .85rem; color: var(--text);
      font-family: inherit; font-size: .9rem; outline: none;
      transition: border-color .2s;
    }
    input:focus, textarea:focus, select:focus { border-color: var(--accent); }
    textarea { resize: vertical; min-height: 100px; line-height: 1.6; }
    select option { background: var(--bg); }

    .field-hint { font-size: .75rem; color: var(--muted); margin-top: .3rem; }

    .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    .btn {
      display: inline-flex; align-items: center; gap: .4rem;
      padding: .65rem 1.4rem; border-radius: 6px; font-size: .9rem;
      font-weight: 600; cursor: pointer; border: none; font-family: inherit;
      transition: background .2s;
    }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-primary:hover { background: var(--accent-h); }
    .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--muted); }
    .btn-ghost:hover { border-color: var(--accent); color: var(--text); }

    .project-list { list-style: none; display: flex; flex-direction: column; gap: .5rem; margin-bottom: 2rem; }
    .project-list li {
      display: flex; align-items: center; justify-content: space-between;
      background: var(--bg); border: 1px solid var(--border);
      border-radius: 6px; padding: .6rem 1rem; font-size: .9rem;
    }
    .project-list .slug { color: var(--muted); font-size: .78rem; font-family: monospace; }
    .project-list a.view { font-size: .8rem; color: var(--muted); }
    .project-list a.view:hover { color: var(--text); }

    .sep { border: none; border-top: 1px solid var(--border); margin: 2rem 0; }

    .login-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-box { width: 100%; max-width: 380px; }
    .login-box h1 { font-size: 1.3rem; margin-bottom: 1.5rem; text-align: center; }

    @media (max-width: 600px) { .grid2 { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<?php if (!$logged): ?>

<div class="login-wrap">
  <div class="login-box card">
    <h1>Administration</h1>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="action" value="login">
      <div class="field">
        <label>Identifiant</label>
        <input type="text" name="user" required autocomplete="username">
      </div>
      <div class="field">
        <label>Mot de passe</label>
        <input type="password" name="pass" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:.5rem">Connexion</button>
    </form>
  </div>
</div>

<?php else: ?>

<div class="topbar">
  <strong>⚙ Admin — Portfolio</strong>
  <div class="topbar-links">
    <a href="../index.html" target="_blank">Voir le site →</a>
    <a href="?logout=1">Déconnexion</a>
  </div>
</div>

<div class="wrap">

  <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <div class="card">
    <h2>Projets publiés</h2>
    <?php if ($projects): ?>
    <ul class="project-list">
      <?php foreach ($projects as $p): ?>
      <li>
        <div>
          <span><?= htmlspecialchars($p['title']) ?></span><br>
          <span class="slug"><?= htmlspecialchars($p['slug']) ?></span>
        </div>
        <a class="view" href="../projects/<?= $p['slug'] ?>/index.html" target="_blank">Voir →</a>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <p style="color:var(--muted);font-size:.9rem;margin-bottom:1.5rem">Aucun projet pour l'instant.</p>
    <?php endif; ?>

    <hr class="sep">

    <h2>Publier un nouveau projet</h2>

    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="publish">

      <div class="field">
        <label>Titre *</label>
        <input type="text" name="title" required placeholder="Mon super projet">
      </div>

      <div class="field">
        <label>Catégorie *</label>
        <select name="category">
          <option value="web">Développement web</option>
          <option value="creation">Création numérique</option>
          <option value="autre">Autre</option>
        </select>
      </div>

      <div class="field">
        <label>Description courte * <span style="font-weight:400">(carte d'accueil, 1-2 phrases)</span></label>
        <textarea name="short_desc" rows="2" required placeholder="Description affichée sur la carte du projet..."></textarea>
      </div>

      <div class="field">
        <label>Description longue * <span style="font-weight:400">(page de détail, 3-4 phrases)</span></label>
        <textarea name="long_desc" required placeholder="Description complète affichée sur la page du projet..."></textarea>
      </div>

      <div class="field">
        <label>Technologies <span style="font-weight:400">(séparées par des virgules)</span></label>
        <input type="text" name="tags" placeholder="WordPress, PHP, MySQL">
        <p class="field-hint">Icônes automatiques pour : PHP, MySQL, JavaScript, HTML, CSS, WordPress, WooCommerce, Figma, Git, Photoshop, Illustrator, Premiere, Blender, DaVinci</p>
      </div>

      <div class="grid2">
        <div class="field">
          <label>Lien Live</label>
          <input type="url" name="live_url" placeholder="https://monsite.fr">
        </div>
        <div class="field">
          <label>Lien GitHub</label>
          <input type="url" name="github_url" placeholder="https://github.com/...">
        </div>
      </div>

      <div class="field">
        <label>Image de couverture <span style="font-weight:400">(jpg, png, webp)</span></label>
        <input type="file" name="cover" accept="image/*" style="color:var(--muted)">
        <p class="field-hint">Affichée sur la carte et en tête de la page de détail</p>
      </div>

      <div class="field">
        <label>Images supplémentaires <span style="font-weight:400">(mosaïque, sélection multiple)</span></label>
        <input type="file" name="images[]" multiple accept="image/*" style="color:var(--muted)">
      </div>

      <div style="display:flex;gap:1rem;margin-top:1.5rem">
        <button type="submit" class="btn btn-primary">Publier le projet</button>
        <button type="reset" class="btn btn-ghost">Réinitialiser</button>
      </div>
    </form>
  </div>

</div>

<?php endif; ?>

</body>
</html>
