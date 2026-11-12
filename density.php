<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$saved = (!STATIC_BUILD && $_SERVER['REQUEST_METHOD']==='POST');
if ($saved) flash('New density value submitted. It becomes usable once the Plant Head approves it.', 'ok');

$active = count(array_filter($DENSITIES, fn($d)=>$d['status']==='active'));
$soon   = count(array_filter($DENSITIES, fn($d)=>$d['status']==='expiring'));
$exp    = count(array_filter($DENSITIES, fn($d)=>$d['status']==='expired'));

$PAGE_ID='density'; $PAGE_TITLE='Density Parameters';
$PAGE_SUB='Approved bulk density values used by the volume-to-tonnage conversion';
$PAGE_ACTIONS='<a class="btn btn--primary btn--sm" href="#newDensity">'.icon('plus',16).' Record new test</a>';
require __DIR__ . '/includes/header.php';
?>

<section class="grid g4">
  <div class="kpi kpi--dark">
    <div class="kpi__t"><span class="kpi__l">ACTIVE VALUES</span><span class="kpi__i"><?= icon('flask',17) ?></span></div>
    <div class="kpi__v"><b><?= $active ?></b><span>materials</span></div>
    <div class="kpi__d"><span class="delta">usable in the calculator</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">EXPIRING SOON</span><span class="kpi__i"><?= icon('clock',17) ?></span></div>
    <div class="kpi__v"><b><?= $soon ?></b><span>within 30 days</span></div>
    <div class="kpi__d"><span class="delta delta--dn"><?= icon('dn',12) ?> schedule a retest</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">EXPIRED</span><span class="kpi__i"><?= icon('alert',17) ?></span></div>
    <div class="kpi__v"><b><?= $exp ?></b><span>locked</span></div>
    <div class="kpi__d"><span class="delta delta--dn"><?= icon('dn',12) ?> cannot be selected</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">REFERENCE STANDARD</span><span class="kpi__i"><?= icon('shield',17) ?></span></div>
    <div class="kpi__v"><b style="font-size:19px">ASTM C29</b></div>
    <div class="kpi__d"><span class="delta delta--up">/C29M bulk density</span></div>
  </div>
</section>

<div class="card">
  <div class="card__h"><div><h3>Density register</h3><p>Every value carries its test reference, date and validity, so a conversion can always be traced</p></div>
    <label class="top__search" style="width:240px"><?= icon('search',16) ?><input data-filter="#denTable" placeholder="Filter material…"></label></div>
  <div class="card__b card__b--flush">
    <table class="tbl" id="denTable">
      <thead><tr><th>MATERIAL</th><th>DENSITY</th><th>REFERENCE</th><th>TEST DATE</th><th>VALID UNTIL</th><th>RELEASED BY</th><th>STATUS</th></tr></thead>
      <tbody>
      <?php foreach ($DENSITIES as $d):
            $tone = $d['status']==='active' ? 'navy' : ($d['status']==='expiring' ? 'red' : 'gray');
            $lbl  = $d['status']==='active' ? 'Active' : ($d['status']==='expiring' ? 'Expires soon' : 'Expired'); ?>
        <tr>
          <td class="k"><?= e($d['material']) ?></td>
          <td class="num" style="font-weight:700;color:var(--g-900)"><?= number_format($d['value'],2) ?> t/m³</td>
          <td><?= e($d['ref']) ?></td>
          <td class="num"><?= date('d M Y',strtotime($d['tested'])) ?></td>
          <td class="num" style="<?= $d['status']!=='active'?'color:var(--red-600);font-weight:600':'' ?>"><?= date('d M Y',strtotime($d['valid'])) ?></td>
          <td><?= e($d['by']) ?></td>
          <td><?= chip($lbl,$tone) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="split">
  <form class="card" id="newDensity" method="post" action="<?= u('density') ?>">
    <div class="card__h"><div><h3>Record a new laboratory test</h3><p>Quality Control releases the value, the Plant Head approves it</p></div></div>
    <div class="card__b">
      <div class="row2">
        <div class="field"><label class="field__label" for="dm">Material</label>
          <div class="ctrl"><select id="dm" name="material">
            <?php foreach ($MATERIALS as $m): ?><option><?= e($m['name']) ?></option><?php endforeach; ?>
          </select><?= icon('chev',18) ?></div></div>
        <div class="field"><label class="field__label" for="dv">Measured bulk density (t/m³)</label>
          <div class="ctrl"><?= icon('flask',20) ?><input id="dv" name="value" type="number" step="0.01" value="1.58"></div></div>
      </div>
      <div class="row2">
        <div class="field"><label class="field__label" for="dr">Test reference</label>
          <div class="ctrl"><select id="dr" name="ref"><option>ASTM C29/C29M</option><option>ASTM D1298</option><option>Supplier TDS</option></select><?= icon('chev',18) ?></div></div>
        <div class="field"><label class="field__label" for="dt">Test date</label>
          <div class="ctrl"><?= icon('clock',20) ?><input id="dt" name="tested" type="date" value="2026-10-31"></div></div>
      </div>
      <div class="field"><label class="field__label" for="du">Valid until</label>
        <div class="ctrl"><?= icon('clock',20) ?><input id="du" name="valid" type="date" value="2027-04-30"></div></div>
      <label class="check"><input type="checkbox" checked> The sample was taken from the delivered material at this site.</label>
      <div style="height:12px"></div>
      <button class="btn btn--primary" type="submit"><?= icon('check',16) ?> Submit for approval</button>
    </div>
  </form>

  <div class="banner banner--navy">
    <?= icon('calc',18) ?>
    <div><b>Why the density matters</b>
      Without a weighbridge the received mass is produced by m = V × ρ, so an error in the density value moves straight
      into the inventory record. A value may only be used while it is inside its validity period, and every conversion
      stores which value was applied.</div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
