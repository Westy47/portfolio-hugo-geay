(() => {
  const imgs = document.querySelectorAll('.mosaic-img');
  if (!imgs.length) return;

  const overlay = document.createElement('div');
  overlay.id = 'lightbox';
  overlay.innerHTML = '<img class="lb-img" alt=""><button class="lb-close" aria-label="Fermer">✕</button>';
  document.body.appendChild(overlay);

  const lbImg = overlay.querySelector('.lb-img');

  const open = (src, alt) => {
    lbImg.src = src;
    lbImg.alt = alt;
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  };

  const close = () => {
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  };

  imgs.forEach(img => {
    img.addEventListener('click', () => open(img.src, img.alt));
  });

  overlay.querySelector('.lb-close').addEventListener('click', close);
  overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
})();
