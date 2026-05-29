(() => {
  const filterBtns = document.querySelectorAll('.filter-btn');
  const cards      = document.querySelectorAll('.project-card');

  if (!filterBtns.length) return;

  const FADE_MS = 160;

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.dataset.filter;

      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      cards.forEach(card => {
        const match = filter === 'all' || card.dataset.category === filter;

        card.style.transition = `opacity ${FADE_MS}ms ease, transform ${FADE_MS}ms ease`;
        card.style.opacity    = '0';
        card.style.transform  = 'translateY(6px)';

        setTimeout(() => {
          card.style.display = match ? 'flex' : 'none';
          if (match) {
            requestAnimationFrame(() => {
              card.style.opacity   = '1';
              card.style.transform = '';
            });
          }
        }, FADE_MS);
      });
    });
  });
})();
