/* DynaStock — small UI behaviours for the prototype */
(function () {
  /* password visibility */
  document.querySelectorAll('.pw-toggle').forEach(function (b) {
    b.addEventListener('click', function () {
      var i = b.parentNode.querySelector('input');
      if (!i) return;
      i.type = i.type === 'password' ? 'text' : 'password';
      b.setAttribute('aria-label', i.type === 'password' ? 'Show password' : 'Hide password');
    });
  });

  /* notifications panel */
  var nb = document.getElementById('notifBtn'), np = document.getElementById('notifPanel');
  if (nb && np) {
    nb.addEventListener('click', function (e) { e.stopPropagation(); np.classList.toggle('hide'); });
    document.addEventListener('click', function (e) { if (!np.contains(e.target)) np.classList.add('hide'); });
  }

  /* live density calculator — FR-09 */
  var calc = document.getElementById('calcForm');
  if (calc) {
    var vol = document.getElementById('inVolume'),
        den = document.getElementById('inDensity'),
        sta = document.getElementById('inStated'),
        tol = parseFloat(calc.dataset.tol || '1'),
        oV  = document.getElementById('outVolume'),
        oD  = document.getElementById('outDensity'),
        oM  = document.getElementById('outMass'),
        oDiff = document.getElementById('outDiff'),
        oVar  = document.getElementById('outVar'),
        gate  = document.getElementById('gateBox'),
        gateT = document.getElementById('gateTitle'),
        gateD = document.getElementById('gateDesc'),
        act   = document.getElementById('gateAction');

    function run() {
      var v = parseFloat(vol.value) || 0,
          d = parseFloat(den.value) || 0,
          s = parseFloat(sta.value) || 0,
          m = v * d,
          diff = m - s,
          vr = s ? (diff / s) * 100 : 0,
          pass = Math.abs(vr) <= tol;

      oV.textContent = v.toFixed(1) + ' m³';
      oD.textContent = d.toFixed(2) + ' t/m³';
      oM.textContent = m.toFixed(2) + ' t';
      oDiff.textContent = (diff >= 0 ? '+' : '−') + Math.abs(diff).toFixed(2) + ' t';
      oVar.textContent = (vr >= 0 ? '+' : '−') + Math.abs(vr).toFixed(2) + '%';
      oVar.className = pass ? '' : 'bad';

      if (!gate) return;
      gate.className = 'banner banner--' + (pass ? 'navy' : 'red');
      gateTitle.textContent = pass
        ? 'Within tolerance — ready to post'
        : 'Variance exceeds the tolerance limit';
      gateD.textContent = pass
        ? 'The variance of ' + Math.abs(vr).toFixed(2) + '% is inside the ±' + tol.toFixed(2) + '% limit for this material. The Goods Receipt can be posted and the inventory synchronised.'
        : 'Posting is blocked. A Corrective Action Report must be created and approved by the Head of Batching Plant before this receipt can be synchronised.';
      if (act) {
        act.textContent = pass ? 'Post Goods Receipt' : 'Create Corrective Action Report';
        act.className = 'btn btn--block ' + (pass ? 'btn--primary' : 'btn--danger');
        act.setAttribute('href', pass ? act.dataset.post : act.dataset.car);
      }
    }
    [vol, den, sta].forEach(function (el) { if (el) el.addEventListener('input', run); });
    run();
  }

  /* reconciliation: recompute variance as the physical count is typed */
  document.querySelectorAll('[data-recon]').forEach(function (row) {
    var inp = row.querySelector('input'),
        rec = parseFloat(row.dataset.recorded),
        tol = parseFloat(row.dataset.tol),
        out = row.querySelector('[data-var]'),
        st  = row.querySelector('[data-status]');
    if (!inp) return;
    function calc2() {
      var ph = parseFloat(inp.value);
      if (isNaN(ph)) { out.textContent = '—'; return; }
      var v = ((ph - rec) / rec) * 100, pass = Math.abs(v) <= tol;
      out.textContent = (v >= 0 ? '+' : '−') + Math.abs(v).toFixed(2) + '%';
      out.style.color = pass ? 'var(--navy-900)' : 'var(--red-600)';
      out.style.fontWeight = '600';
      if (st) st.innerHTML = '<span class="chip chip--' + (pass ? 'navy' : 'red') + '">' +
        (pass ? 'Fit — synchronise' : 'Gap — CAR required') + '</span>';
    }
    inp.addEventListener('input', calc2);
  });

  /* table filter boxes */
  document.querySelectorAll('[data-filter]').forEach(function (inp) {
    inp.addEventListener('input', function () {
      var q = inp.value.toLowerCase(),
          rows = document.querySelectorAll(inp.dataset.filter + ' tbody tr');
      rows.forEach(function (r) { r.style.display = r.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none'; });
    });
  });
})();
