<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$total = 0; $below = 0;
foreach ($MATERIALS as $m) { $total += $m['stock']; if ($m['stock'] < rop($m['daily'],$m['lead'],$m['ss'])) $below++; }

$PAGE_ID='inventory'; $PAGE_TITLE='Inventory';
$PAGE_SUB='Stock level, reorder point and order quantity for every material class';
$PAGE_ACTIONS='<a class="btn btn--ghost btn--sm" href="'.u('reconciliation').'">'.icon('scale',16).' Stock take</a>
               <a class="btn btn--primary btn--sm" href="'.u('reports').'">'.icon('down',16).' Export</a>';
require __DIR__ . '/includes/header.php';
?>

<section class="grid g4">
  <div class="kpi kpi--dark">
    <div class="kpi__t"><span class="kpi__l">TOTAL RECORDED STOCK</span><span class="kpi__i"><?= icon('box',17) ?></span></div>
    <div class="kpi__v"><b><?= t_($total,0) ?></b><span>t</span></div>
    <div class="kpi__d"><span class="delta delta--up"><?= icon('up',12) ?> across <?= count($MATERIALS) ?> materials</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">BELOW REORDER POINT</span><span class="kpi__i"><?= icon('alert',17) ?></span></div>
    <div class="kpi__v"><b><?= $below ?></b><span>materials</span></div>
    <div class="kpi__d"><span class="delta delta--dn"><?= icon('dn',12) ?> action required</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">AVERAGE FILL RATE</span><span class="kpi__i"><?= icon('chart',17) ?></span></div>
    <?php $fill = 0; foreach ($MATERIALS as $m) $fill += $m['stock']/$m['cap']; $fill = round($fill/count($MATERIALS)*100); ?>
    <div class="kpi__v"><b><?= $fill ?></b><span>% of capacity</span></div>
    <div class="kpi__d"><span class="delta delta--up"><?= icon('up',12) ?> healthy</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">OPEN PURCHASE SUGGESTIONS</span><span class="kpi__i"><?= icon('cart',17) ?></span></div>
    <div class="kpi__v"><b><?= $below ?></b><span>orders</span></div>
    <div class="kpi__d"><span class="delta delta--up"><?= icon('up',12) ?> based on EOQ</span></div>
  </div>
</section>

<div class="card">
  <div class="card__h">
    <div><h3>Stock overview</h3><p>Reorder point uses ROP = (d × L) + SS, order quantity uses EOQ = √(2DS / H)</p></div>
    <label class="top__search" style="width:240px"><?= icon('search',16) ?><input data-filter="#invTable" placeholder="Filter material…"></label>
  </div>
  <div class="card__b card__b--flush">
    <table class="tbl" id="invTable">
      <thead><tr>
        <th>MATERIAL</th><th>CLASS</th><th>CURRENT STOCK</th><th>CAPACITY</th><th>FILL</th>
        <th>ROP</th><th>SAFETY STOCK</th><th>EOQ</th><th>TOLERANCE</th><th>STATUS</th><th></th>
      </tr></thead>
      <tbody>
      <?php foreach ($MATERIALS as $m):
            $r = rop($m['daily'],$m['lead'],$m['ss']);
            $q = eoq($m['daily']*365,$m['order_cost'],$m['hold_cost']);
            $p = round($m['stock']/$m['cap']*100);
            $low = $m['stock'] < $r; $warn = !$low && $m['stock'] < $r*1.15; ?>
        <tr>
          <td class="k"><?= e($m['name']) ?><div class="muted" style="font-size:11px"><?= e($m['code']) ?></div></td>
          <td><?= e($m['class']) ?></td>
          <td class="num" style="font-weight:600;color:<?= $low?'var(--red-600)':'var(--g-900)' ?>"><?= t_($m['stock'],1) ?> t</td>
          <td class="num"><?= number_format($m['cap']) ?> t</td>
          <td style="min-width:110px">
            <div class="track"><i class="<?= $low?'low':'' ?>" style="width:<?= $p ?>%"></i></div>
            <span class="muted" style="font-size:11px"><?= $p ?>%</span>
          </td>
          <td class="num"><?= t_($r,1) ?> t</td>
          <td class="num"><?= t_($m['ss'],1) ?> t</td>
          <td class="num" style="font-weight:600;color:var(--navy-900)"><?= number_format($q) ?> t</td>
          <td class="num">±<?= rtrim(rtrim(number_format($m['tol'],1),'0'),'.') ?>%</td>
          <td><?= chip($low?'Order now':($warn?'Monitor':'Healthy'), $low?'red':($warn?'gray':'navy')) ?></td>
          <td class="tr"><a href="<?= u('stock','m='.urlencode($m['name'])) ?>" style="color:var(--g-400)"><?= icon('right',16) ?></a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="banner banner--navy">
  <?= icon('calc',18) ?>
  <div><b>How the planning figures are produced</b>
    ROP is the average daily demand multiplied by the supplier lead time plus the safety stock. EOQ balances the ordering
    cost against the annual holding cost. Both are planning references; the main focus of the design stays on the accuracy
    of the receiving data and the inventory records.</div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
