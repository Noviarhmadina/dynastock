<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

/* which ticket are we working on */
$tkt = $_GET['tkt'] ?? 'SJ-24111';
$R = null; foreach ($RECEIPTS as $r) if ($r['tkt'] === $tkt) $R = $r;
if (!$R) $R = $RECEIPTS[3];

$mat   = material($R['material']);
$tol   = $mat['tol'];
$vol   = $R['volume']  ?? 15.0;
$den   = $R['density'] ?? $mat['density'];
$stated= $R['stated'];
$mass  = tonnage($vol,$den);
$var   = variance($mass,$stated);
$pass  = gate_pass($var,$tol);

/* approved density values for this material — FR-08 */
$opts = array_values(array_filter($DENSITIES, fn($d)=>$d['material']===$R['material']));

$PAGE_ID='receiving'; $PAGE_TITLE='Receiving — ' . $R['tkt'];
$PAGE_SUB= $R['supplier'] . ' · ' . $R['vehicle'] . ' · ' . $R['material'] . ' · received ' . $R['time'] . ', 31 Oct 2026';
$PAGE_BACK=['receiving','Back to Material Receiving'];
$PAGE_ACTIONS='<a class="btn btn--ghost btn--sm" href="'.u('receiving').'">Save draft</a>
               <a class="btn btn--primary btn--sm" href="#gateBox">Run validation '.icon('right',16).'</a>';
require __DIR__ . '/includes/header.php';
?>

<!-- stepper -->
<div class="stepper">
  <div class="step step--done"><span class="step__d"><?= icon('check',13) ?></span><div><span>STEP 1</span><b>Document &amp; Vehicle</b></div></div>
  <i class="step__line"></i>
  <div class="step step--done"><span class="step__d"><?= icon('check',13) ?></span><div><span>STEP 2</span><b>Volume Measurement</b></div></div>
  <i class="step__line"></i>
  <div class="step step--on"><span class="step__d">3</span><div><span>STEP 3</span><b>Density &amp; Tonnage</b></div></div>
  <i class="step__line"></i>
  <div class="step step--todo"><span class="step__d">4</span><div><span>STEP 4</span><b>Validation Gate</b></div></div>
</div>

<form id="calcForm" class="split split--wide" data-tol="<?= $tol ?>" onsubmit="return false">

  <div style="display:flex;flex-direction:column;gap:18px">

    <!-- step 2 recap, editable -->
    <div class="card">
      <div class="card__h"><div><h3>Volume measurement</h3><p>Recorded in the safe area after the physical inspection</p></div>
        <span class="chip chip--gray"><?= e($R['method'] ?? 'Silo level gauge') ?></span></div>
      <div class="card__b">
        <div class="row2">
          <div class="field" style="margin:0">
            <label class="field__label" for="inVolume">Measured volume (m³)</label>
            <div class="ctrl"><input id="inVolume" type="number" step="0.1" min="0" value="<?= $vol ?>"></div>
          </div>
          <div class="field" style="margin:0">
            <label class="field__label" for="inStated">Tonnage stated on the delivery ticket (t)</label>
            <div class="ctrl"><input id="inStated" type="number" step="0.01" min="0" value="<?= $stated ?>"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- FR-08 authorised density -->
    <div class="card">
      <div class="card__h"><div><h3>Authorized density value</h3><p>Only approved laboratory values can be used in the conversion</p></div></div>
      <div class="card__b">
        <?php foreach ($opts as $i => $d): $active = $d['status'] !== 'expired'; ?>
          <label class="opt <?= $i===0 && $active ? 'on' : ($active ? '' : 'off') ?>">
            <input type="radio" name="den" class="hide" value="<?= $d['value'] ?>" <?= $i===0&&$active?'checked':'' ?> <?= $active?'':'disabled' ?>
                   onchange="document.getElementById('inDensity').value=this.value;document.getElementById('inDensity').dispatchEvent(new Event('input'));
                             document.querySelectorAll('.opt').forEach(function(o){o.classList.remove('on')});this.closest('.opt').classList.add('on')">
            <span class="opt__r"></span>
            <span class="opt__t">
              <b><?= number_format($d['value'],2) ?> t/m³</b>
              <span><?= e($d['material']) ?> · lab test <?= date('d M Y',strtotime($d['tested'])) ?> ·
                <?= $active ? 'valid to '.date('d M Y',strtotime($d['valid'])) : 'expired '.date('d M Y',strtotime($d['valid'])) ?> · <?= e($d['ref']) ?></span>
            </span>
            <?= chip($active ? ($d['status']==='expiring' ? 'Expires soon' : 'Active') : 'Expired', $active ? ($d['status']==='expiring'?'red':'navy') : 'gray') ?>
          </label>
        <?php endforeach; ?>
        <input id="inDensity" type="hidden" value="<?= $den ?>">
        <p class="hint" style="margin:4px 0 0">Expired values are locked. A new value has to be released by Quality Control on the Density Parameters page.</p>
      </div>
    </div>

    <!-- FR-09 calculator -->
    <div class="card">
      <div class="card__h"><div><h3>Dynamic Density Calculator</h3><p>Conversion of measured volume into tonnage</p></div>
        <span class="chip chip--navy">Auto-calculated</span></div>
      <div class="card__b">
        <div class="calc">
          <div class="calc__p"><b id="outVolume"><?= t_($vol,1) ?> m³</b><span>MEASURED VOLUME (V)</span></div>
          <div class="calc__o">×</div>
          <div class="calc__p"><b id="outDensity"><?= t_($den,2) ?> t/m³</b><span>BULK DENSITY (ρ)</span></div>
          <div class="calc__o">=</div>
          <div class="calc__p out"><b id="outMass"><?= t_($mass) ?> t</b><span>CALCULATED MASS (m)</span></div>
        </div>

        <div class="quad">
          <div><span>Delivery ticket states</span><b><?= t_($stated) ?> t</b></div>
          <div><span>Difference</span><b id="outDiff"><?= ($mass-$stated>=0?'+':'−').t_(abs($mass-$stated)) ?> t</b></div>
          <div><span>Variance</span><b><span id="outVar" class="<?= $pass?'':'bad' ?>"><?= pct($var) ?></span></b></div>
          <div><span>Tolerance (<?= e($R['material']) ?>)</span><b>±<?= number_format($tol,2) ?>%</b></div>
        </div>

        <!-- FR-10 validation gate -->
        <div id="gateBox" class="banner banner--<?= $pass?'navy':'red' ?>" style="margin-top:16px">
          <?= icon($pass?'shield':'alert',18) ?>
          <div>
            <b id="gateTitle"><?= $pass ? 'Within tolerance — ready to post' : 'Variance exceeds the tolerance limit' ?></b>
            <span id="gateDesc"><?= $pass
              ? 'The variance is inside the ±'.number_format($tol,2).'% limit for this material. The Goods Receipt can be posted and the inventory synchronised.'
              : 'Posting is blocked. A Corrective Action Report must be created and approved by the Head of Batching Plant before this receipt can be synchronised.' ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- right column -->
  <div style="display:flex;flex-direction:column;gap:18px">
    <div class="card">
      <div class="card__h"><div><h3>Delivery ticket</h3><p>Data captured at the gate</p></div></div>
      <div class="card__b">
        <div class="kv"><span>Ticket number</span><b><?= e($R['tkt']) ?></b></div>
        <div class="kv"><span>Supplier</span><b><?= e($R['supplier']) ?></b></div>
        <div class="kv"><span>Vehicle</span><b><?= e($R['vehicle']) ?></b></div>
        <div class="kv"><span>Driver</span><b><?= e($R['driver']) ?></b></div>
        <div class="kv"><span>Material</span><b><?= e($R['material']) ?></b></div>
        <div class="kv"><span>Stated tonnage</span><b><?= t_($stated) ?> t</b></div>
        <div class="kv"><span>Verified by</span><b><?= e($R['gate'] ?? 'Security · Budi S.') ?></b></div>
      </div>
    </div>

    <div class="card">
      <div class="card__h"><div><h3>Duplicate check</h3><p>FR-06 · automated validation gate</p></div></div>
      <div class="card__b">
        <div class="kv"><span>Ticket number</span><b style="color:var(--navy-900)">Unique today</b></div>
        <div class="kv"><span>Supplier + vehicle + date</span><b style="color:var(--navy-900)">No match found</b></div>
        <div class="kv"><span>Existing GR for this ticket</span><b style="color:var(--navy-900)">None</b></div>
      </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px">
      <a id="gateAction"
         class="btn btn--block <?= $pass?'btn--primary':'btn--danger' ?>"
         data-post="<?= u('receiving') ?>" data-car="<?= u('car') ?>"
         href="<?= $pass?u('receiving'):u('car') ?>">
        <?= $pass ? 'Post Goods Receipt' : 'Create Corrective Action Report' ?>
      </a>
      <a class="btn btn--ghost btn--block" href="<?= u('receiving') ?>">Back to volume measurement</a>
    </div>
  </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
