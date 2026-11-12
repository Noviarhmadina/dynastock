<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$counts = ['all'=>count($RECEIPTS),'gate'=>0,'density'=>0,'blocked'=>0,'posted'=>0,'approval'=>0];
foreach ($RECEIPTS as $r) $counts[$r['status']] = ($counts[$r['status']] ?? 0) + 1;

$PAGE_ID='receiving'; $PAGE_TITLE='Material Receiving';
$PAGE_SUB='Verify delivery tickets, convert volume to tonnage and post the Goods Receipt';
$PAGE_ACTIONS='<a class="btn btn--ghost btn--sm" href="'.u('gate').'">'.icon('shield',16).' Gate verification</a>
               <a class="btn btn--primary btn--sm" href="'.u('entry').'">'.icon('plus',16).' New receiving</a>';
require __DIR__ . '/includes/header.php';
?>

<div class="tabs">
  <a class="tab on" href="#">All <i><?= $counts['all'] ?></i></a>
  <a class="tab" href="<?= u('gate') ?>">Awaiting verification <i><?= $counts['gate'] ?></i></a>
  <a class="tab" href="#">Density pending <i><?= $counts['density'] ?></i></a>
  <a class="tab" href="<?= u('car') ?>">Needs CAR <i><?= $counts['blocked'] ?></i></a>
  <a class="tab" href="<?= u('approvals') ?>">Awaiting approval <i><?= $counts['approval'] ?></i></a>
  <a class="tab" href="#">Posted <i><?= $counts['posted'] ?></i></a>
</div>

<div class="card">
  <div class="card__h">
    <div><h3>Receiving transactions</h3><p>Showing <?= count($RECEIPTS) ?> records for 31 Oct 2026 · <?= e(PLANT) ?></p></div>
    <label class="top__search" style="width:260px">
      <?= icon('search',16) ?><input data-filter="#recTable" placeholder="Filter ticket, supplier, material…">
    </label>
  </div>
  <div class="card__b card__b--flush">
    <table class="tbl" id="recTable">
      <thead><tr>
        <th>TICKET</th><th>TIME</th><th>SUPPLIER</th><th>VEHICLE</th><th>MATERIAL</th>
        <th>VOLUME</th><th>DENSITY</th><th>TONNAGE</th><th>VARIANCE</th><th>STATUS</th><th></th>
      </tr></thead>
      <tbody>
      <?php foreach ($RECEIPTS as $r):
            $m = tonnage($r['volume'],$r['density']); $v = variance($m,$r['stated']);
            $tol = tolerance_of($r['material']); $pass = gate_pass($v,$tol);
            $href = $r['status']==='gate' ? u('gate') : u('entry','tkt='.$r['tkt']); ?>
        <tr>
          <td class="k"><?= e($r['tkt']) ?></td>
          <td class="num"><?= e($r['time']) ?></td>
          <td><?= e($r['supplier']) ?></td>
          <td class="num"><?= e($r['vehicle']) ?></td>
          <td><?= e($r['material']) ?></td>
          <td class="num"><?= $r['volume']!==null ? t_($r['volume'],1).' m³' : '—' ?></td>
          <td class="num"><?= $r['density']!==null ? t_($r['density'],2) : '—' ?></td>
          <td class="num"><?= $m!==null ? t_($m).' t' : '—' ?></td>
          <td class="num" style="font-weight:600;color:<?= $pass===false?'var(--red-600)':($pass===true?'var(--navy-900)':'var(--g-400)') ?>"><?= pct($v) ?></td>
          <td><?= status_chip($r['status']) ?></td>
          <td class="tr"><a href="<?= $href ?>" title="Open" style="color:var(--g-400)"><?= icon('right',16) ?></a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <div class="tfoot">
      <span>Showing 1–<?= count($RECEIPTS) ?> of 24 transactions</span>
      <span class="flex gap8">
        <a class="btn btn--ghost btn--sm" href="#"><?= icon('left',15) ?> Previous</a>
        <a class="btn btn--ghost btn--sm" href="#">Next <?= icon('right',15) ?></a>
      </span>
    </div>
  </div>
</div>

<div class="banner banner--gray">
  <?= icon('shield',18) ?>
  <div><b>How a receipt is validated</b>
    Gate verification checks the delivery ticket for completeness and duplicates, the admin records the measured volume,
    the calculator converts it with m = V × ρ, and the validation gate compares the result with the tolerance limit of the
    material class before the Goods Receipt can be posted.</div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
