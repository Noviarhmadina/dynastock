<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$fit = 0; $gap = 0;
foreach ($RECON as $r) { $v = (($r['physical']-$r['recorded'])/$r['recorded'])*100; abs($v) <= $r['tol'] ? $fit++ : $gap++; }

$PAGE_ID='reconciliation'; $PAGE_TITLE='Stock Reconciliation';
$PAGE_SUB='Physical stock take compared with the recorded inventory, checked against the ICS tolerance limit';
$PAGE_ACTIONS='<a class="btn btn--ghost btn--sm" href="'.u('reports').'">'.icon('down',16).' Export</a>
               <a class="btn btn--primary btn--sm" href="'.u('approvals').'">'.icon('check',16).' Submit reconciliation</a>';
require __DIR__ . '/includes/header.php';
?>

<section class="grid g4">
  <div class="kpi kpi--dark">
    <div class="kpi__t"><span class="kpi__l">STOCK TAKE DATE</span><span class="kpi__i"><?= icon('clock',17) ?></span></div>
    <div class="kpi__v"><b>30 Oct</b><span>2026</span></div>
    <div class="kpi__d"><span class="delta">Controlling · Lia N.</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">MATERIALS COUNTED</span><span class="kpi__i"><?= icon('layers',17) ?></span></div>
    <div class="kpi__v"><b><?= count($RECON) ?></b><span>of <?= count($MATERIALS) ?></span></div>
    <div class="kpi__d"><span class="delta delta--up"><?= icon('up',12) ?> on schedule</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">WITHIN TOLERANCE (FIT)</span><span class="kpi__i"><?= icon('check-circle',17) ?></span></div>
    <div class="kpi__v"><b><?= $fit ?></b><span>materials</span></div>
    <div class="kpi__d"><span class="delta delta--up"><?= icon('up',12) ?> can be synchronised</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">OUT OF TOLERANCE (GAP)</span><span class="kpi__i"><?= icon('alert',17) ?></span></div>
    <div class="kpi__v"><b><?= $gap ?></b><span>materials</span></div>
    <div class="kpi__d"><span class="delta delta--dn"><?= icon('dn',12) ?> CAR required</span></div>
  </div>
</section>

<div class="card">
  <div class="card__h"><div><h3>Physical vs recorded</h3><p>Type a physical count to recalculate the variance live — the tolerance limit comes from the material master</p></div>
    <span class="chip chip--gray">Period 10 / 2026</span></div>
  <div class="card__b card__b--flush">
    <table class="tbl">
      <thead><tr><th>MATERIAL</th><th>RECORDED</th><th>PHYSICAL COUNT</th><th>DIFFERENCE</th><th>VARIANCE</th><th>TOLERANCE</th><th>FIT / GAP</th></tr></thead>
      <tbody>
      <?php foreach ($RECON as $r):
            $v = round((($r['physical']-$r['recorded'])/$r['recorded'])*100,2);
            $pass = abs($v) <= $r['tol']; $diff = $r['physical'] - $r['recorded']; ?>
        <tr data-recon data-recorded="<?= $r['recorded'] ?>" data-tol="<?= $r['tol'] ?>">
          <td class="k"><?= e($r['material']) ?></td>
          <td class="num"><?= t_($r['recorded'],1) ?> t</td>
          <td style="width:140px"><div class="ctrl ctrl--sm"><input type="number" step="0.1" value="<?= $r['physical'] ?>"></div></td>
          <td class="num"><?= ($diff>=0?'+':'−').t_(abs($diff),1) ?> t</td>
          <td class="num" data-var style="font-weight:600;color:<?= $pass?'var(--navy-900)':'var(--red-600)' ?>"><?= pct($v) ?></td>
          <td class="num">±<?= rtrim(rtrim(number_format($r['tol'],1),'0'),'.') ?>%</td>
          <td data-status><?= chip($pass?'Fit — synchronise':'Gap — CAR required', $pass?'navy':'red') ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="split">
  <div class="banner banner--navy">
    <?= icon('shield',18) ?>
    <div><b>Rule applied by the system</b>
      If the difference is inside the tolerance limit the transaction is valid and the inventory record can be synchronised.
      If it is outside the limit the system raises a warning, prevents an immediate data adjustment, and requires a
      Corrective Action Report. A stock-take difference is treated separately from a quantity difference on a single
      receiving transaction.</div>
  </div>
  <div class="card">
    <div class="card__h"><div><h3>Next step</h3><p>After the count is confirmed</p></div></div>
    <div class="card__b" style="display:flex;flex-direction:column;gap:10px">
      <a class="btn btn--primary btn--block" href="<?= u('approvals') ?>">Send fit items for synchronisation</a>
      <a class="btn btn--danger btn--block" href="<?= u('car') ?>">Raise CAR for gap items</a>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
