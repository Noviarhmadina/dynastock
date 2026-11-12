<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$name = $_GET['m'] ?? 'Cement';
$m = material($name) ?: $MATERIALS[0];
$name = $m['name'];
$r = rop($m['daily'],$m['lead'],$m['ss']);
$q = eoq($m['daily']*365,$m['order_cost'],$m['hold_cost']);
$p = round($m['stock']/$m['cap']*100);
$low = $m['stock'] < $r;

/* 14-day stock trend, drawn as an inline sparkline */
$trend = [988,961,933,1010,982,954,1008,979,951,1004,976,948,972,$m['stock']];
$scale = $m['stock'] / 972;
$trend = array_map(fn($v) => round($v * $scale, 1), $trend);

$PAGE_ID='inventory'; $PAGE_TITLE='Stock Card — ' . $name;
$PAGE_SUB=$m['code'] . ' · ' . $m['class'] . ' · tolerance ±' . rtrim(rtrim(number_format($m['tol'],1),'0'),'.') . '%';
$PAGE_BACK=['inventory','Back to Inventory'];
$PAGE_ACTIONS='<a class="btn btn--ghost btn--sm" href="'.u('reconciliation').'">'.icon('scale',16).' Reconcile</a>
               <a class="btn btn--primary btn--sm" href="'.u('reports').'">'.icon('down',16).' Export stock card</a>';
require __DIR__ . '/includes/header.php';

$max = max($trend); $min = min($trend) * 0.94; $w = 100; $h = 40;
$pts = [];
foreach ($trend as $i => $v) {
    $x = $i / (count($trend) - 1) * $w;
    $y = $h - (($v - $min) / max(0.01, $max - $min)) * $h;
    $pts[] = round($x,2) . ',' . round($y,2);
}
?>

<section class="grid g4">
  <div class="kpi kpi--dark">
    <div class="kpi__t"><span class="kpi__l">CURRENT STOCK</span><span class="kpi__i"><?= icon('box',17) ?></span></div>
    <div class="kpi__v"><b><?= t_($m['stock'],1) ?></b><span>t</span></div>
    <div class="kpi__d"><span class="delta"><?= $p ?>% of <?= number_format($m['cap']) ?> t capacity</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">REORDER POINT</span><span class="kpi__i"><?= icon('alert',17) ?></span></div>
    <div class="kpi__v"><b><?= t_($r,1) ?></b><span>t</span></div>
    <div class="kpi__d"><span class="delta <?= $low?'delta--dn':'delta--up' ?>"><?= $low ? 'below ROP' : 'above ROP' ?></span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">BULK DENSITY</span><span class="kpi__i"><?= icon('flask',17) ?></span></div>
    <div class="kpi__v"><b><?= number_format($m['density'],2) ?></b><span>t/m³</span></div>
    <div class="kpi__d"><span class="delta delta--up">ASTM C29/C29M</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">SUGGESTED ORDER (EOQ)</span><span class="kpi__i"><?= icon('cart',17) ?></span></div>
    <div class="kpi__v"><b><?= number_format($q) ?></b><span>t</span></div>
    <div class="kpi__d"><span class="delta delta--up">lead time <?= $m['lead'] ?> days</span></div>
  </div>
</section>

<section class="split">
  <div class="card">
    <div class="card__h"><div><h3>Stock movement ledger</h3><p>Goods receipts, production issues and stock take adjustments</p></div>
      <span class="chip chip--gray">Last 6 movements</span></div>
    <div class="card__b card__b--flush">
      <table class="tbl ledger">
        <thead><tr><th>DATE</th><th>DOCUMENT</th><th>TYPE</th><th>IN</th><th>OUT</th><th>NOTE</th></tr></thead>
        <tbody>
        <?php $bal = $m['stock']; foreach ($LEDGER as $l): ?>
          <tr>
            <td class="num"><?= e($l['date']) ?></td>
            <td class="k"><?= e($l['doc']) ?></td>
            <td><?= e($l['type']) ?></td>
            <td class="num in"><?= $l['in'] ? '+'.t_($l['in']) : '—' ?></td>
            <td class="num out"><?= $l['out'] ? '−'.t_($l['out']) : '—' ?></td>
            <td class="muted"><?= e($l['note']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:18px">
    <div class="card">
      <div class="card__h"><div><h3>Stock level — 14 days</h3><p>Against reorder point and safety stock</p></div></div>
      <div class="card__b">
        <svg viewBox="0 0 100 40" preserveAspectRatio="none" style="width:100%;height:110px">
          <polyline points="<?= implode(' ',$pts) ?>" fill="none" stroke="#032A48" stroke-width="1.4" vector-effect="non-scaling-stroke"/>
          <line x1="0" y1="<?= round(40 - (($r-$min)/max(0.01,$max-$min))*40,2) ?>" x2="100" y2="<?= round(40 - (($r-$min)/max(0.01,$max-$min))*40,2) ?>"
                stroke="#CF000F" stroke-width="1" stroke-dasharray="3 3" vector-effect="non-scaling-stroke"/>
        </svg>
        <div class="legend" style="margin-top:8px">
          <span><b style="background:var(--navy-900)"></b>Recorded stock</span>
          <span><b style="background:var(--red-600)"></b>Reorder point</span>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card__h"><div><h3>Planning parameters</h3><p>Used by the reorder calculation</p></div></div>
      <div class="card__b">
        <div class="kv"><span>Average daily demand (d)</span><b><?= t_($m['daily'],1) ?> t/day</b></div>
        <div class="kv"><span>Lead time (L)</span><b><?= $m['lead'] ?> days</b></div>
        <div class="kv"><span>Safety stock (SS)</span><b><?= t_($m['ss'],1) ?> t</b></div>
        <div class="kv"><span>Reorder point</span><b><?= t_($r,1) ?> t</b></div>
        <div class="kv"><span>Annual demand (D)</span><b><?= number_format($m['daily']*365) ?> t</b></div>
        <div class="kv"><span>Tolerance limit</span><b>±<?= rtrim(rtrim(number_format($m['tol'],1),'0'),'.') ?>%</b></div>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
