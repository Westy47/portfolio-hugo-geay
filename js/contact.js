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
      const data = new FormData(form);
      const body = new URLSearchParams(data).toString();

      const res = await fetch('/', {
        method:  'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body,
      });

      if (res.ok) {
        status.textContent = '✓ Message envoyé ! Je te répondrai dès que possible.';
        status.classList.add('ok');
        form.reset();
      } else {
        status.textContent = 'Une erreur est survenue. Réessaie ou écris-moi directement.';
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
