// Add your custom JS here.

// AOS stagger: assign delay by DOM index so cards always cascade in sequence.
(function () {
  var STEP = 150; // ms between each sibling

  document
    .querySelectorAll("[data-aos-stagger-group]")
    .forEach(function (group) {
      var children = Array.from(group.querySelectorAll("[data-aos]"));

      children.forEach(function (el, index) {
        el.setAttribute("data-aos-delay", index * STEP);
      });
    });
})();

AOS.init({
  easing: "ease-out",
  once: true,
  duration: 600,
});

// Add background to navbar on scroll
(function () {
  var navbar = document.getElementById("wrapper-navbar");

  var addNavbarBackground = function () {
    if (window.scrollY > 50) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }
  };

  window.addEventListener("scroll", addNavbarBackground);
})();

// footer logo animation
(function () {
  const clip = document.getElementById("footer-logo-clip");
  const inner = document.getElementById("footer-logo-inner");
  const svg = document.getElementById("footer-logo-svg");
  if (!clip || !inner || !svg) return;

  const prefersReduced =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  let triggered = false;

  // Ensure initial visual state shows the three bars (left half) on load
  // Make clip fill container and inner be 200% width, sitting at translateX(0)
  clip.style.width = "100%";
  inner.style.width = "200%";
  inner.style.transform = "translateX(0)";
  // disable any transition until animated explicitly
  inner.style.transition = "none";

  function prepareAndAnimate() {
    // Make clip fill the container width
    clip.style.width = "100%";
    // Ensure inner uses left origin so translating reveals the right half
    inner.style.transformOrigin = "left center";

    // Make inner element 200% width so its left-half fills the clip initially
    inner.style.width = "200%";
    inner.style.display = "block";
    inner.style.transform = "translateX(0)";

    // If user prefers reduced motion, jump to final state (fully revealed)
    if (prefersReduced) {
      inner.style.transform = "translateX(-50%)";
      return;
    }

    // Animate to final state: move left by 50% of inner width (revealing right half)
    // Use a slightly slower duration and a smoother easing
    const animDuration = 1.6; // seconds
    const gsapEase = "power3.out";
    if (window.gsap && typeof window.gsap.to === "function") {
      window.gsap.to(inner, {
        xPercent: -50,
        duration: animDuration,
        ease: gsapEase,
      });
    } else {
      // Fallback: animate via CSS transition with similar easing
      inner.style.transition =
        "transform " + animDuration + "s cubic-bezier(.22,.9,.32,1)";
      requestAnimationFrame(() => {
        inner.style.transform = "translateX(-50%)";
      });
    }
  }

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (triggered) return;
        if (entry.isIntersecting && entry.intersectionRatio >= 0.1) {
          triggered = true;
          prepareAndAnimate();
          obs.disconnect();
        }
      });
    },
    { rootMargin: "0px 0px -10px 0px", threshold: [0] },
  );

  // Prefer the colophon as the trigger element (fires when its bottom enters viewport)
  const triggerEl =
    document.querySelector(".footer__colophon") ||
    document.querySelector(".footer__logo") ||
    clip;
  if (triggerEl) observer.observe(triggerEl);

  // If window resizes before animation triggered, ensure inner stays 200% width
  let resizeTimer = null;
  window.addEventListener("resize", () => {
    if (triggered) return; // no need after animation
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      clip.style.width = "100%";
      inner.style.width = "200%";
    }, 120);
  });
})();

// (function() {
//   // Hide header on scroll
//   var doc = document.documentElement;
//   var w = window;

//   var prevScroll = w.scrollY || doc.scrollTop;
//   var curScroll;
//   var direction = 0;
//   var prevDirection = 0;

//   var header = document.getElementById('wrapper-navbar');

//   var checkScroll = function() {
//       // Find the direction of scroll (0 - initial, 1 - up, 2 - down)
//       curScroll = w.scrollY || doc.scrollTop;
//       if (curScroll > prevScroll) {
//           // Scrolled down
//           direction = 2;

// Equalize image heights per multi-module row in content grid.
(function () {
  function syncRow(row) {
    const covers = row.querySelectorAll(".img-cover");
    if (!covers || covers.length < 2) return;

    row.classList.remove("content-grid-row-sync");

    let maxHeight = 0;
    covers.forEach((cover) => {
      cover.style.height = "auto";
    });

    covers.forEach((cover) => {
      const rect = cover.getBoundingClientRect();
      if (rect.height > maxHeight) maxHeight = rect.height;
    });

    if (maxHeight > 0) {
      covers.forEach((cover) => {
        cover.style.height = `${Math.ceil(maxHeight)}px`;
      });
      row.classList.add("content-grid-row-sync");
    } else {
      covers.forEach((cover) => {
        cover.style.height = "";
      });
      row.classList.remove("content-grid-row-sync");
    }
  }

  function syncAll() {
    const rows = document.querySelectorAll(".content-grid .row");
    rows.forEach(syncRow);
  }

  function init() {
    syncAll();
    const imgs = document.querySelectorAll(".content-grid .img-cover img");
    imgs.forEach((img) => {
      if (img.complete) return;
      img.addEventListener("load", syncAll, { once: true });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  let resizeTimer = null;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(syncAll, 150);
  });
})();
//       } else if (curScroll < prevScroll) {
//           // Scrolled up
//           direction = 1;
//       }

//       if (direction !== prevDirection) {
//           toggleHeader(direction, curScroll);
//       }

//       prevScroll = curScroll;
//   };

//   var toggleHeader = function(direction, curScroll) {
//       if (direction === 2 && curScroll > 125) {
//           // Replace 52 with the height of your header in px
//           if (!document.getElementById('navbar').classList.contains('show')) {
//               header.classList.add('hide');
//               prevDirection = direction;
//           }
//       } else if (direction === 1) {
//           header.classList.remove('hide');
//           prevDirection = direction;
//       }
//   };

//   window.addEventListener('scroll', checkScroll);
// }
// )();

// Swap solutions nav intro content on card hover/focus.
(function () {
  const navs = document.querySelectorAll(
    ".cb-solutions-nav, .cb-business-travel-nav, .cb-specialist-travel-nav",
  );

  if (!navs.length) return;

  navs.forEach((nav) => {
    const title = nav.querySelector("[data-solutions-nav-title]");
    const summary = nav.querySelector("[data-solutions-nav-summary]");
    const cards = nav.querySelectorAll("[data-solutions-nav-card]");

    if (!title || !summary || !cards.length) return;

    const setActiveCard = (card) => {
      cards.forEach((item) => item.classList.remove("is-active"));
      card.classList.add("is-active");

      title.textContent = card.dataset.cardTitle || "";

      const summaryTemplate = card.querySelector("[hidden]");
      summary.innerHTML = summaryTemplate ? summaryTemplate.innerHTML : "";
    };

    cards.forEach((card) => {
      card.addEventListener("mouseenter", () => setActiveCard(card));
      card.addEventListener("focus", () => setActiveCard(card));
    });
  });
})();

// Count up stat hero values when they enter view.
(function () {
  const statHeroes = document.querySelectorAll(".stat-hero, .cb-stats");

  if (!statHeroes.length) return;

  const prefersReducedMotion =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const animateValue = (element) => {
    const target = Number(element.dataset.statTarget || 0);

    if (!Number.isFinite(target)) {
      return;
    }

    if (prefersReducedMotion || target === 0) {
      element.textContent = String(target);
      return;
    }

    const duration = 1200;
    const startTime = performance.now();

    const tick = (now) => {
      const progress = Math.min((now - startTime) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      element.textContent = String(Math.round(target * eased));

      if (progress < 1) {
        window.requestAnimationFrame(tick);
      }
    };

    window.requestAnimationFrame(tick);
  };

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        entry.target
          .querySelectorAll(".stat-hero__stat-value, .cb-stats__stat-value")
          .forEach(animateValue);

        obs.unobserve(entry.target);
      });
    },
    {
      threshold: 0.35,
    },
  );

  statHeroes.forEach((hero) => observer.observe(hero));
})();

/*

  // Header background
  document.addEventListener('scroll', function() {
      var nav = document.getElementById('navbar');
    //   var primaryNav = document.getElementById('primaryNav');
    //   if (!primaryNav.classList.contains('show')) {
    //       nav.classList.toggle('scrolled', window.scrollY > nav.offsetHeight);
    //   }
      document.querySelectorAll('.dropdown-menu').forEach(function(dropdown) {
          dropdown.classList.remove('show');
      });
      document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
          toggle.classList.remove('show');
          toggle.blur();
      });
  });

*/
