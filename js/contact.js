(() => {
  const form      = document.getElementById('contact-form');
  if (!form) return;

  const status    = form.querySelector('.form-status');
  const submitBtn = form.querySelector('.form-submit');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    submitBtn.disabled = true;
    submitBtn.classList.add('loading');
    status.textContent = '';
    status.className   = 'form-status';

    try {
      const res  = await fetch('contact.php', {
        method: 'POST',
        body:   new FormData(form),
      });

      let data;
      try { data = await res.json(); } catch { data = { ok: false }; }

      if (data.ok) {
        status.textContent = '✓ Message envoyé ! Je te répondrai dès que possible.';
        status.classList.add('ok');
        form.reset();
      } else {
        const msg = Array.isArray(data.errors) ? data.errors.join(' ') : 'Une erreur est survenue.';
        status.textContent = msg;
        status.classList.add('error');
      }
    } catch {
      status.textContent = 'Impossible de joindre le serveur. Écris-moi directement par email.';
      status.classList.add('error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.classList.remove('loading');
    }
  });
})();
