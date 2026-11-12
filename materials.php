<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$saved = (!STATIC_BUILD && $_SERVER['REQUEST_METHOD']==='POST');
if ($saved) flash('Material master updated. New tolerance limits apply to the next receiving transaction.', 'ok');

$PAGE_ID='materials'; $PAGE_TITLE='Materials';
$PAGE_SUB='Material master data and the stock discrepancy tolerance limits used by the validation gate';
$PAGE_ACTIONS='<a class="btn btn--ghost btn--sm" href="'.u('density').'">'.icon('flask',16).' Density parameters</a>
               <a class="btn btn--primary btn--sm" href="#">'.icon('plus',16).' New material</a>';
require __DIR__ . '/includes/header.php';
?>

<div class="banner banner--navy">
  <?= icon('shield',18) ?>
  <div><b>Source of the tolerance limits</b>
    The values below follow the Internal Control Standard of PT Solusi Bangun Indonesia Tbk, SOP IN.MT.02 and IN.MT.03.
    They are configurable here rather than hard-coded, because the limit that applies to a single receiving transaction
    still has to be confirmed with the process owner.</div>
</div>

<form class="card" method="post" action="<?= u('materials') ?>">
  <div class="card__h"><div><h3>Material master</h3><p>Tolerance, default density and planning parameters per material class</p></div>
    <button class="btn btn--primary btn--sm" type="submit"><?= icon('check',16) ?> Save changes</button></div>
  <div class="card__b card__b--flush">
    <table class="tbl">
      <thead><tr><th>CODE</th><th>MATERIAL</th><th>CLASS</th><th>UNIT</th><th>TOLERANCE (±%)</th><th>DEFAULT DENSITY</th><th>CAPACITY</th><th>SAFETY STOCK</th><th>LEAD TIME</th><th>STATUS</th></tr></thead>
      <tbody>
      <?php foreach ($MATERIALS as $m): ?>
        <tr>
          <td class="k"><?= e($m['code']) ?></td>
          <td class="k"><?= e($m['name']) ?></td>
          <td><?= e($m['class']) ?></td>
          <td><?= e($m['unit']) ?></td>
          <td style="width:110px"><div class="ctrl ctrl--sm"><input type="number" step="0.5" min="0" value="<?= $m['tol'] ?>" name="tol[<?= e($m['code']) ?>]"></div></td>
          <td class="num"><?= number_format($m['density'],2) ?> t/m³</td>
          <td class="num"><?= number_format($m['cap']) ?> t</td>
          <td class="num"><?= t_($m['ss'],1) ?> t</td>
          <td class="num"><?= $m['lead'] ?> days</td>
          <td><?= chip('Active','navy') ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</form>

<div class="split">
  <div class="card">
    <div class="card__h"><div><h3>Tolerance limits by material class</h3><p>Table 2.1 of the proposal</p></div></div>
    <div class="card__b card__b--flush">
      <table class="tbl">
        <thead><tr><th>NO</th><th>MATERIAL CLASSIFICATION</th><th>TOLERANCE LIMIT</th></tr></thead>
        <tbody>
        <?php $rows = [['Cement','±1%'],['Fly Ash','±1%'],['Silica Fume','±1%'],['Coarse Aggregate (split, screening)','±2%'],['Fuel (diesel)','±2%'],['Admixture (chemical components)','±3%'],['Fine Aggregate (M-sand, sand)','±5%']];
              foreach ($rows as $i => $r): ?>
          <tr><td class="num"><?= $i+1 ?></td><td class="k"><?= e($r[0]) ?></td><td class="num"><?= e($r[1]) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card">
    <div class="card__h"><div><h3>Suppliers</h3><p>Registered for this site</p></div></div>
    <div class="card__b card__b--flush">
      <table class="tbl">
        <thead><tr><th>SUPPLIER</th><th>MATERIAL</th><th>LEAD TIME</th></tr></thead>
        <tbody>
          <tr><td class="k">PT Varia Usaha</td><td>Cement, Fly Ash, Silica Fume</td><td class="num">3–7 days</td></tr>
          <tr><td class="k">CV Mitra Beton</td><td>Coarse Aggregate, Admixture</td><td class="num">2–6 days</td></tr>
          <tr><td class="k">PT Sinar Abadi</td><td>Fine Aggregate</td><td class="num">2 days</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
