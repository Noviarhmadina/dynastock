<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

/* ---- derive everything from the data so the numbers stay consistent ---- */
$totalStock = 0; foreach ($MATERIALS as $m) $totalStock += $m['stock'];
$inbound = count($RECEIPTS);
$pending = 0; $openDisc = 0;
foreach ($RECEIPTS as $r) if (in_array($r['status'], ['approval','blocked'], true)) $pending++;
foreach ($RECON as $r) { $v = (($r['physical'] - $r['recorded']) / $r['recorded']) * 100; if (abs($v) > $r['tol']) $openDisc++; }

$slices = [
  ['Cement', 945.0, '#032A48'], ['Coarse Aggregate', 612.0, '#14527D'],
  ['Fine Aggregate', 486.0, '#4E85AB'], ['Fly Ash', 112.0, '#CF000F'],
  ['Others', 63.9, '#9EBBD0'],
];
$maxBar = 420;

$PAGE_ID='dashboard'; $PAGE_TITLE='Inventory Management';
$PAGE_SUB='Monitor material receiving, stock accuracy and discrepancies across the plant';
$PAGE_ACTIONS='<a class="btn btn--ghost btn--sm" href="'.u('reports').'">'.icon('chev',15).' This week</a>
               <a class="btn btn--primary btn--sm" href="'.u('reports').'">'.icon('down',16).' Export report</a>';
require __DIR__ . '/includes/header.php';
?>

<!-- KPI — FR-14 -->
<section class="grid g4">
  <div class="kpi kpi--dark">
    <div class="kpi__t"><span class="kpi__l">TOTAL RECORDED STOCK</span><span class="kpi__i"><?= icon('box',17) ?></span></div>
    <div class="kpi__v"><b><?= t_($totalStock,0) ?></b><span>t</span></div>
    <div class="kpi__d"><span class="delta delta--up"><?= icon('up',12) ?> +4.2% vs last week</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">INBOUND TODAY</span><span class="kpi__i"><?= icon('truck',17) ?></span></div>
    <div class="kpi__v"><b><?= $inbound ?></b><span>trucks</span></div>
    <div class="kpi__d"><span class="delta delta--up"><?= icon('up',12) ?> +3 trucks</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">PENDING APPROVAL</span><span class="kpi__i"><?= icon('check-circle',17) ?></span></div>
    <div class="kpi__v"><b><?= count($APPROVALS) ?></b><span>items</span></div>
    <div class="kpi__d"><span class="delta delta--dn"><?= icon('dn',12) ?> 2 over 24h</span></div>
  </div>
  <div class="kpi">
    <div class="kpi__t"><span class="kpi__l">OPEN DISCREPANCIES</span><span class="kpi__i"><?= icon('alert',17) ?></span></div>
    <div class="kpi__v"><b><?= $openDisc ?></b><span>items</span></div>
    <div class="kpi__d"><span class="delta delta--up"><?= icon('up',12) ?> −2 vs last week</span></div>
  </div>
</section>

<!-- charts -->
<section class="split" style="grid-template-columns:384px 1fr">
  <div class="card">
    <div class="card__h"><div><h3>Stock Composition</h3><p>Share of total recorded tonnage</p></div></div>
    <div class="card__b">
      <figure class="donut" style="width:176px;height:176px">
        <?= donut($slices) ?>
        <figcaption><b><?= t_($totalStock,0) ?></b><span>tonnes</span></figcaption>
      </figure>
      <div class="dlist">
        <?php foreach ($slices as $s): $p = round($s[1] / $totalStock * 100); ?>
          <div><em style="background:<?= $s[2] ?>"></em><span><?= e($s[0]) ?></span>
            <?php if ($s[0] === 'Fly Ash'): ?><strong style="font-size:10.5px;color:var(--red-600)">below ROP</strong><?php endif; ?>
            <b><?= $p ?>%</b></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card__h"><div><h3>Weekly Receiving Volume</h3><p>Tonnage received vs tonnage flagged out of tolerance</p></div>
      <span class="chip chip--gray">Last 7 days</span></div>
    <div class="card__b">
      <div class="bars">
        <?php foreach ($WEEK as [$d,$rec,$fl]): ?>
          <div class="bars__d">
            <div class="bars__p">
              <i style="height:<?= round($rec/$maxBar*162) ?>px" title="<?= $rec ?> t received"></i>
              <i class="f" style="height:<?= max(6, round($fl/$maxBar*162)) ?>px" title="<?= $fl ?> t out of tolerance"></i>
            </div>
            <span class="bars__l"><?= e($d) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="legend">
        <span><b style="background:var(--navy-900)"></b>Received (t)</span>
        <span><b style="background:var(--red-600)"></b>Out of tolerance (t)</span>
      </div>
    </div>
  </div>
</section>

<!-- stock levels -->
<section class="grid g4">
  <?php foreach (['Cement','Coarse Aggregate','Fine Aggregate','Fly Ash'] as $n):
        $m = material($n); $p = round($m['stock'] / $m['cap'] * 100); $low = $m['stock'] < $m['rop']; ?>
    <a class="card" style="padding:16px 18px" href="<?= u('stock','m='.urlencode($n)) ?>">
      <div class="flex jb ac"><b style="font-size:13px;color:var(--g-900)"><?= e($n) ?></b>
        <span class="tag">±<?= rtrim(rtrim(number_format($m['tol'],1),'0'),'.') ?>%</span></div>
      <div class="flex ac gap8" style="margin:11px 0 0;align-items:baseline">
        <b style="font-size:21px;font-weight:700;letter-spacing:-.3px;color:<?= $low?'var(--red-600)':'var(--g-900)' ?>"><?= t_($m['stock'],0) ?></b>
        <span class="muted" style="font-size:11.5px">/ <?= number_format($m['cap']) ?> t</span>
      </div>
      <div class="track" style="margin:11px 0 9px"><i class="<?= $low?'low':'' ?>" style="width:<?= $p ?>%"></i></div>
      <div class="flex jb" style="font-size:11px">
        <span style="font-weight:500;color:<?= $low?'var(--red-600)':'var(--g-600)' ?>"><?= $low?'Below reorder point':'Healthy level' ?></span>
        <b style="color:<?= $low?'var(--red-600)':'var(--g-700)' ?>"><?= $p ?>%</b>
      </div>
    </a>
  <?php endforeach; ?>
</section>

<!-- receiving + reorder -->
<section class="split">
  <div class="card">
    <div class="card__h">
      <div><h3>Active Material Receiving</h3><p>Measured volume converted with m = V × ρ, then checked against the ICS tolerance limit</p></div>
      <a class="flex ac gap8" style="font-size:12.5px;font-weight:600;color:var(--navy-900)" href="<?= u('receiving') ?>">View all <?= icon('right',13) ?></a>
    </div>
    <div class="card__b card__b--flush">
      <table class="tbl">
        <thead><tr><th>TICKET</th><th>SUPPLIER</th><th>MATERIAL</th><th>VOLUME</th><th>DENSITY</th><th>TONNAGE</th><th>VARIANCE</th><th>STATUS</th></tr></thead>
        <tbody>
        <?php foreach (array_slice($RECEIPTS,0,5) as $r):
              $m = tonnage($r['volume'],$r['density']); $v = variance($m,$r['stated']);
              $tol = tolerance_of($r['material']); $pass = gate_pass($v,$tol); ?>
          <tr>
            <td class="k"><?= e($r['tkt']) ?></td>
            <td><?= e($r['supplier']) ?></td>
            <td><?= e($r['material']) ?></td>
            <td class="num"><?= $r['volume']!==null ? t_($r['volume'],1).' m³' : '—' ?></td>
            <td class="num"><?= $r['density']!==null ? t_($r['density'],2) : '—' ?></td>
            <td class="num"><?= $m!==null ? t_($m).' t' : '—' ?></td>
            <td class="num" style="font-weight:600;color:<?= $pass===false?'var(--red-600)':'var(--navy-900)' ?>"><?= pct($v) ?></td>
            <td><?= status_chip($r['status']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card__h"><div><h3>Reorder Point Alerts</h3><p>Current stock vs ROP</p></div></div>
    <div class="card__b" style="display:flex;flex-direction:column;gap:12px">
      <?php foreach ($MATERIALS as $m):
            if ($m['stock'] >= $m['rop'] * 1.05) continue;
            $urgent = $m['stock'] < $m['rop'];
            $q = eoq($m['daily']*365, $m['order_cost'], $m['hold_cost']); ?>
        <div class="alert <?= $urgent?'alert--urgent':'' ?>">
          <div class="alert__h"><?= icon('alert',15) ?><span><?= e($m['name']) ?></span><em><?= $urgent?'Order now':'Monitor' ?></em></div>
          <div class="alert__g">
            <div><span>Current</span><b><?= t_($m['stock'],1) ?> t</b></div>
            <div><span>ROP</span><b><?= t_(rop($m['daily'],$m['lead'],$m['ss']),1) ?> t</b></div>
            <div><span>Suggested EOQ</span><b><?= number_format($q) ?> t</b></div>
          </div>
        </div>
      <?php endforeach; ?>
      <a class="btn btn--ghost btn--sm" href="<?= u('inventory') ?>">Open inventory planning</a>
    </div>
  </div>
</section>

<!-- reconciliation + density -->
<section class="split">
  <div class="card">
    <div class="card__h">
      <div><h3>Stock Discrepancy — Physical vs Recorded</h3><p>Latest stock take compared with the ICS tolerance limit per material</p></div>
      <a class="flex ac gap8" style="font-size:12.5px;font-weight:600;color:var(--navy-900)" href="<?= u('reconciliation') ?>">Open reconciliation <?= icon('right',13) ?></a>
    </div>
    <div class="card__b card__b--flush">
      <table class="tbl">
        <thead><tr><th>MATERIAL</th><th>RECORDED</th><th>PHYSICAL</th><th>VARIANCE</th><th>TOLERANCE</th><th>FIT / GAP</th></tr></thead>
        <tbody>
        <?php foreach (array_slice($RECON,0,4) as $r):
              $v = round((($r['physical']-$r['recorded'])/$r['recorded'])*100,2); $pass = abs($v) <= $r['tol']; ?>
          <tr>
            <td class="k"><?= e($r['material']) ?></td>
            <td class="num"><?= t_($r['recorded'],1) ?> t</td>
            <td class="num"><?= t_($r['physical'],1) ?> t</td>
            <td class="num" style="font-weight:600;color:<?= $pass?'var(--navy-900)':'var(--red-600)' ?>"><?= pct($v) ?></td>
            <td class="num">±<?= rtrim(rtrim(number_format($r['tol'],1),'0'),'.') ?>%</td>
            <td><?= chip($pass?'Fit — synchronised':'Gap — CAR required', $pass?'navy':'red') ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card__h"><div><h3>Density Parameters</h3><p>Approved values used by the calculator</p></div></div>
    <div class="card__b" style="display:flex;flex-direction:column;gap:11px">
      <?php foreach (array_filter($DENSITIES, fn($d)=>$d['status']!=='expired') as $d):
            if (!in_array($d['material'],['Cement','Coarse Aggregate','Fine Aggregate','Fly Ash'],true)) continue;
            $soon = $d['status']==='expiring'; ?>
        <div class="flex ac gap12" style="background:var(--g-50);border-radius:var(--r-md);padding:11px 13px">
          <span style="width:34px;height:34px;border-radius:9px;background:var(--navy-50);color:var(--navy-900);display:grid;place-items:center"><?= icon('flask',17) ?></span>
          <div class="grow">
            <b style="display:block;font-size:12.5px;color:var(--g-900)"><?= e($d['material']) ?></b>
            <span style="font-size:10.5px;color:<?= $soon?'var(--red-600)':'var(--g-400)' ?>">ASTM C29 · valid to <?= date('d M Y', strtotime($d['valid'])) ?></span>
          </div>
          <div class="tr"><b style="font-size:15px;color:var(--g-900)"><?= number_format($d['value'],2) ?></b>
            <span style="display:block;font-size:10px;color:var(--g-400)">t/m³</span></div>
        </div>
      <?php endforeach; ?>
      <a class="btn btn--ghost btn--sm" href="<?= u('density') ?>">Manage density parameters</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
