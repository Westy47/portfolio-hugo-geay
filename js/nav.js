const nav       = document.querySelector('nav');
const scroller  = document.querySelector('.page-scroller');
const hamburger = document.querySelector('.nav-hamburger');
const navLinks  = document.querySelector('.nav-links');

if (scroller) {
  scroller.addEventListener('scroll', () => {
    if (scroller.scrollTop > 40) nav.setAttribute('data-scrolled', '');
    else nav.removeAttribute('data-scrolled');
  }, { passive: true });
} else {
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) nav.setAttribute('data-scrolled', '');
    else nav.removeAttribute('data-scrolled');
  }, { passive: true });
}

if (hamburger && navLinks) {
  hamburger.addEventListener('click', () => {
    const open = navLinks.classList.toggle('open');
    hamburger.setAttribute('aria-expanded', String(open));
  });

  navLinks.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      navLinks.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
    });
  });
}
