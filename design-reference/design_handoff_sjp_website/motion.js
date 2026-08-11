/* SJP Theatre Arts — shared motion layer.
   Reveal-on-scroll, staggered grids, counting stats, card lift, condensing header.
   Everything is applied from JS so the markup stays plain inline-styled HTML.
   Honours prefers-reduced-motion and fails open (content is never left hidden). */
(function () {
  'use strict';
  if (typeof window === 'undefined') return;
  if (window.__sjpMotion) return;
  window.__sjpMotion = true;

  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var canObserve = 'IntersectionObserver' in window;
  var EASE = 'cubic-bezier(.22,.61,.36,1)';

  /* ---------- injected keyframes (the one thing that cannot be inline) ---------- */
  try {
    var st = document.createElement('style');
    st.setAttribute('data-sjp-motion', '');
    st.textContent =
      '@keyframes sjpPulse{0%{box-shadow:0 0 0 0 rgba(255,255,255,.45)}70%{box-shadow:0 0 0 28px rgba(255,255,255,0)}100%{box-shadow:0 0 0 0 rgba(255,255,255,0)}}' +
      '@keyframes sjpFloat{0%,100%{transform:translateY(0) rotate(var(--sjp-rot,0deg))}50%{transform:translateY(-12px) rotate(var(--sjp-rot,0deg))}}';
    document.head.appendChild(st);
  } catch (e) {}

  /* ---------- reveal on scroll ---------- */
  var revealed = [];

  function offsetFor(kind) {
    if (kind === 'left') return 'translateX(-28px)';
    if (kind === 'right') return 'translateX(28px)';
    if (kind === 'scale') return 'scale(.955)';
    return 'translateY(28px)';
  }

  function prep(el, kind, delay) {
    if (el.__sjpPrepped) return false;
    el.__sjpPrepped = true;
    revealed.push(el);
    el.style.opacity = '0';
    el.style.transform = offsetFor(kind);
    el.style.transition = 'opacity .72s ' + EASE + ', transform .72s ' + EASE;
    if (delay) el.style.transitionDelay = delay + 'ms';
    el.style.willChange = 'opacity, transform';
    return true;
  }

  function show(el) {
    el.style.opacity = '1';
    el.style.transform = 'none';
    window.setTimeout(function () {
      el.style.willChange = 'auto';
      el.style.transitionDelay = '';
    }, 1100);
  }

  var io = canObserve ? new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      show(entry.target);
      io.unobserve(entry.target);
    });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.04 }) : null;

  /* Safety net: if anything goes wrong, never leave content invisible. */
  window.setTimeout(function () {
    revealed.forEach(function (el) {
      if (el.style.opacity === '0') show(el);
    });
  }, 4000);

  /* ---------- work out what to animate ---------- */
  /* The runtime re-serialises inline styles ("position: absolute"), so every
     style test goes through a whitespace-stripped copy rather than [style*=]. */
  function styleOf(el) {
    return ((el.getAttribute && el.getAttribute('style')) || '').replace(/\s+/g, '');
  }

  function styleHas(el, needle) {
    return styleOf(el).indexOf(needle) > -1;
  }

  function isDecorative(el) {
    return styleHas(el, 'position:absolute');
  }

  function isGrid(el) {
    return styleHas(el, 'grid-template-columns');
  }

  function contentContainer(section) {
    var kids = section.children, i;
    for (i = 0; i < kids.length; i++) {
      var k = kids[i];
      if (k.nodeType !== 1 || isDecorative(k)) continue;
      var s = styleOf(k);
      if (s.indexOf('max-width:') > -1 || s.indexOf('margin:0auto') > -1) return k;
    }
    return section;
  }

  function blocksIn(container) {
    var out = [], kids = container.children, i;
    for (i = 0; i < kids.length; i++) {
      var k = kids[i];
      if (k.nodeType !== 1) continue;
      if (isDecorative(k)) continue;
      if (k.tagName === 'SCRIPT' || k.tagName === 'STYLE') continue;
      out.push(k);
    }
    return out;
  }

  function attach(el, kind, delay) {
    if (prep(el, kind, delay) && io) io.observe(el);
  }

  function tagSection(section) {
    if (section.__sjpTagged) return;
    section.__sjpTagged = true;
    if (reduce || !io) return;

    var container = contentContainer(section);
    var blocks = blocksIn(container);
    var step = 0;

    blocks.forEach(function (block) {
      if (isGrid(block) || block.getAttribute('data-anim-stagger') !== null) {
        // Stagger the cards inside a grid rather than sliding the whole grid.
        var cards = blocksIn(block);
        if (cards.length > 1 && cards.length <= 12) {
          cards.forEach(function (card, i) {
            attach(card, 'up', 70 * i);
          });
          return;
        }
      }
      attach(block, 'up', Math.min(step, 3) * 90);
      step++;
    });
  }

  /* ---------- counting numbers ---------- */
  function countUp(el) {
    if (el.__sjpCounted) return;
    el.__sjpCounted = true;
    var raw = (el.textContent || '').trim();
    var match = raw.match(/^(\D*)(\d[\d,]*)(.*)$/);
    if (!match) return;
    var prefix = match[1], suffix = match[3];
    var target = parseInt(match[2].replace(/,/g, ''), 10);
    if (!target || target > 100000) return;
    if (reduce) return;
    var dur = 900, start = Date.now(), done = false;
    var final = prefix + target.toLocaleString('en-GB') + suffix;
    el.textContent = prefix + '0' + suffix;
    function finish() {
      if (done) return;
      done = true;
      el.textContent = final;
    }
    function frame() {
      if (done) return;
      var p = Math.min((Date.now() - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = prefix + Math.round(target * eased).toLocaleString('en-GB') + suffix;
      if (p < 1) window.requestAnimationFrame(frame); else finish();
    }
    window.requestAnimationFrame(frame);
    /* Guarantee the real number lands even if rAF is throttled or paused. */
    window.setTimeout(finish, dur + 200);
  }

  var countIO = canObserve ? new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      countUp(entry.target);
      countIO.unobserve(entry.target);
    });
  }, { threshold: 0.6 }) : null;

  /* ---------- hover lift on cards ---------- */
  function liftable(el) {
    if (el.__sjpLift) return;
    el.__sjpLift = true;
    var base = el.style.transition;
    el.addEventListener('mouseenter', function () {
      if (reduce) return;
      if (el.style.opacity === '0') return;
      el.style.transition = 'transform .32s ' + EASE + ', box-shadow .32s ' + EASE;
      el.style.transform = 'translateY(-6px)';
    });
    el.addEventListener('mouseleave', function () {
      if (reduce) return;
      el.style.transform = 'none';
      window.setTimeout(function () { el.style.transition = base; }, 340);
    });
  }

  /* ---------- pulsing play button ---------- */
  var PULSE = 'sjpPulse 2.6s ' + EASE + ' infinite';
  function pulse(el) {
    if (reduce) return;
    /* Re-applied on every sweep: a React re-render can wipe JS-written inline styles. */
    if (!el.__sjpHover && el.style.animation !== PULSE) el.style.animation = PULSE;
    if (el.__sjpPulse) return;
    el.__sjpPulse = true;
    el.addEventListener('mouseenter', function () {
      el.__sjpHover = true;
      el.style.animation = 'none';
      el.style.transition = 'transform .3s ' + EASE;
      el.style.transform = 'translate(-50%,-50%) scale(1.06)';
    });
    el.addEventListener('mouseleave', function () {
      el.__sjpHover = false;
      el.style.transform = 'translate(-50%,-50%)';
      el.style.animation = PULSE;
    });
  }

  /* ---------- floating decorative shapes ---------- */
  function floaty(el, i) {
    if (reduce) return;
    if (el.__sjpFloat === undefined) {
      var rot = styleOf(el).match(/rotate\((-?[\d.]+)deg\)/);
      el.__sjpFloat = 'sjpFloat ' + (6 + (i % 3)) + 's ease-in-out ' + ((i % 4) * 0.4) + 's infinite';
      el.__sjpRot = (rot ? rot[1] : 0) + 'deg';
    }
    if (el.style.animation !== el.__sjpFloat) {
      el.style.setProperty('--sjp-rot', el.__sjpRot);
      el.style.animation = el.__sjpFloat;
    }
  }

  /* ---------- condensing sticky header ---------- */
  function watchHeader() {
    var header = null;
    var heads = document.querySelectorAll('header');
    for (var h = 0; h < heads.length; h++) {
      if (styleHas(heads[h], 'position:sticky')) { header = heads[h]; break; }
    }
    if (!header || header.__sjpHeader) return;
    header.__sjpHeader = true;
    var bar = header.firstElementChild;
    if (!bar) return;
    header.style.transition = 'box-shadow .3s ' + EASE;
    bar.style.transition = 'height .3s ' + EASE;
    var last = null;
    function onScroll() {
      var stuck = window.scrollY > 24;
      if (stuck === last) return;
      last = stuck;
      if (reduce) return;
      bar.style.height = stuck ? '72px' : '88px';
      header.style.boxShadow = stuck
        ? '0 10px 30px -14px rgba(33,27,39,.32)'
        : '0 4px 24px -12px rgba(33,27,39,.18)';
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---------- sweep ---------- */
  var shapeIndex = 0;
  function sweep() {
    try {
      document.querySelectorAll('section').forEach(tagSection);

      if (countIO) {
        document.querySelectorAll('[data-count]').forEach(function (el) {
          if (el.__sjpCountWatched) return;
          el.__sjpCountWatched = true;
          countIO.observe(el);
        });
      }

      document.querySelectorAll('article, [data-lift]').forEach(liftable);

      document.querySelectorAll('[data-pulse]').forEach(pulse);

      document.querySelectorAll('div').forEach(function (el) {
        if (el.__sjpFloat !== undefined) { floaty(el, 0); return; }
        var s = styleOf(el);
        if (s.indexOf('position:absolute') > -1 && s.indexOf('rotate(') > -1) floaty(el, shapeIndex++);
      });

      document.querySelectorAll('details').forEach(function (d) {
        var chev = d.querySelector('[data-chevron]');
        if (!chev) return;
        var want = d.open ? 'rotate(180deg)' : 'none';
        if (chev.style.transform !== want) chev.style.transform = want;
        if (d.__sjpToggle) return;
        d.__sjpToggle = true;
        d.addEventListener('toggle', function () {
          chev.style.transform = d.open ? 'rotate(180deg)' : 'none';
        });
      });

      watchHeader();
    } catch (e) {}
  }

  function boot() {
    sweep();
    if ('MutationObserver' in window) {
      var mo = new MutationObserver(function () {
        if (boot.__q) return;
        boot.__q = true;
        window.requestAnimationFrame(function () { boot.__q = false; sweep(); });
      });
      mo.observe(document.documentElement, { childList: true, subtree: true });
      window.setTimeout(function () { mo.disconnect(); }, 20000);
      window.setInterval(sweep, 2000);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
  window.addEventListener('load', sweep);
})();
