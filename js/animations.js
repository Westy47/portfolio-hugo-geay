const motionOk = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (motionOk) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );

  document.querySelectorAll('.project-card').forEach((card, i) => {
    card.style.transitionDelay = `${i * 80}ms`;
  });

  document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
} else {
  document.querySelectorAll('.reveal').forEach((el) => el.classList.add('visible'));
}
