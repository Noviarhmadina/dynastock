/* =====================================================================
   DynaStock — homepage intro
   Dots sweep in from the left and assemble the logo, the tagline fades
   in, a progress bar runs, then the overlay clears to the landing page.
   Total duration ≈ 3.6 s (matches the brand intro clip).
   ===================================================================== */
(function () {
  var overlay = document.getElementById('intro');
  if (!overlay) return;

  var canvas  = document.getElementById('introCanvas');
  var tagline = document.getElementById('introTag');
  var barFill = document.getElementById('introBar');
  var skipBtn = document.getElementById('introSkip');
  var ctx     = canvas.getContext('2d');

  var SWEEP_MS   = 1000;   // how long one dot takes to reach its place
  var STAGGER_MS = 950;    // left-to-right delay spread
  var HOLD_MS    = 700;    // pause once the logo is complete
  var TOTAL_MS   = 3600;

  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var done = false;

  function finish() {
    if (done) return;
    done = true;
    overlay.classList.add('out');
    try { sessionStorage.setItem('ds_intro_seen', '1'); } catch (e) {}
    setTimeout(function () { overlay.remove(); document.body.style.overflow = ''; }, 520);
  }
  if (skipBtn) skipBtn.addEventListener('click', finish);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' || e.key === 'Enter') finish(); });

  /* already watched it this session → go straight to the page */
  var seen = false;
  try { seen = sessionStorage.getItem('ds_intro_seen') === '1'; } catch (e) {}
  if (seen || reduce) {
    if (reduce && !seen) { overlay.querySelector('.intro__stage').classList.add('static'); setTimeout(finish, 900); }
    else { overlay.remove(); document.body.style.overflow = ''; }
    return;
  }

  document.body.style.overflow = 'hidden';

  var img = new Image();
  img.onload = function () { setup(); };
  img.onerror = function () { finish(); };
  img.src = canvas.getAttribute('data-src');

  function setup() {
    var dpr = Math.min(window.devicePixelRatio || 1, 2);
    var cssW = Math.min(overlay.clientWidth * 0.86, 900);
    var cssH = Math.round(cssW * (img.height / img.width));
    canvas.style.width = cssW + 'px';
    canvas.style.height = cssH + 'px';
    canvas.width  = Math.round(cssW * dpr);
    canvas.height = Math.round(cssH * dpr);

    /* sample the logo off-screen */
    var off = document.createElement('canvas');
    off.width = canvas.width; off.height = canvas.height;
    var octx = off.getContext('2d');
    octx.drawImage(img, 0, 0, off.width, off.height);
    var data;
    try { data = octx.getImageData(0, 0, off.width, off.height).data; }
    catch (err) { drawPlain(); setTimeout(finish, TOTAL_MS); return; }

    var step = Math.max(2, Math.round(2.6 * dpr));
    var dots = [];
    for (var y = 0; y < off.height; y += step) {
      for (var x = 0; x < off.width; x += step) {
        var i = (y * off.width + x) * 4;
        if (data[i + 3] < 140) continue;
        dots.push({
          tx: x, ty: y,
          x: -60 * dpr - Math.random() * 220 * dpr,
          y: y + (Math.random() - 0.5) * 150 * dpr,
          r: data[i], g: data[i + 1], b: data[i + 2],
          d: (x / off.width) * STAGGER_MS + Math.random() * 130,
          s: step * 0.92
        });
      }
    }

    var start = performance.now();
    function ease(t) { return 1 - Math.pow(1 - t, 3); }

    function frame(now) {
      var el = now - start;
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      for (var k = 0; k < dots.length; k++) {
        var p = dots[k];
        var t = (el - p.d) / SWEEP_MS;
        if (t <= 0) continue;
        if (t > 1) t = 1;
        var m = ease(t);
        var px = p.x + (p.tx - p.x) * m;
        var py = p.y + (p.ty - p.y) * m;
        ctx.globalAlpha = t < 0.12 ? t / 0.12 : 1;
        ctx.fillStyle = 'rgb(' + p.r + ',' + p.g + ',' + p.b + ')';
        ctx.fillRect(px, py, p.s, p.s);
      }
      ctx.globalAlpha = 1;

      /* leading sweep highlight */
      var sweepT = el / (STAGGER_MS + SWEEP_MS * 0.55);
      if (sweepT < 1.15) {
        var sx = sweepT * canvas.width;
        var grad = ctx.createLinearGradient(sx - 90 * dpr, 0, sx + 30 * dpr, 0);
        grad.addColorStop(0, 'rgba(207,0,15,0)');
        grad.addColorStop(0.7, 'rgba(207,0,15,0.30)');
        grad.addColorStop(1, 'rgba(207,0,15,0)');
        ctx.fillStyle = grad;
        ctx.fillRect(sx - 90 * dpr, 0, 120 * dpr, canvas.height);
      }

      if (el > STAGGER_MS + SWEEP_MS - 350 && tagline) tagline.classList.add('in');
      if (barFill) barFill.style.width = Math.min(100, (el / (TOTAL_MS - 250)) * 100) + '%';

      if (el >= STAGGER_MS + SWEEP_MS + HOLD_MS) {
        /* crisp final render so the logo never looks pixelated */
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      }
      if (el < TOTAL_MS) requestAnimationFrame(frame);
      else finish();
    }
    requestAnimationFrame(frame);
  }

  function drawPlain() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
    if (tagline) tagline.classList.add('in');
    if (barFill) barFill.style.width = '100%';
  }
})();
