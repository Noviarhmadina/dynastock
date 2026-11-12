<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$sent = (!STATIC_BUILD && $_SERVER['REQUEST_METHOD'] === 'POST');
if ($sent) { flash('CAR-2026-015 submitted to the Head of Batching Plant for approval.', 'ok'); }

$PAGE_ID='receiving'; $PAGE_TITLE='Corrective Action Report';
$PAGE_SUB='Raised when a receiving variance exceeds the tolerance limit of the material class';
$PAGE_BACK=['receiving','Back to Material Receiving'];
require __DIR__ . '/includes/header.php';
?>

<div class="split">
  <form class="card" method="post" action="<?= u('car') ?>">
    <div class="card__h"><div><h3>New report — SJ-24111</h3><p>Fly Ash · variance +3.60% against a ±1.00% tolerance limit</p></div>
      <span class="chip chip--red">Posting blocked</span></div>
    <div class="card__b">

      <div class="quad" style="margin:0 0 18px">
        <div><span>Measured volume</span><b>15.0 m³</b></div>
        <div><span>Density used</span><b>1.12 t/m³</b></div>
        <div><span>Calculated mass</span><b>16.80 t</b></div>
        <div><span>Variance</span><b class="bad">+3.60%</b></div>
      </div>

      <div class="field">
        <label class="field__label" for="cause">Root cause</label>
        <div class="ctrl" style="height:auto;padding:12px 14px">
          <textarea id="cause" name="cause" rows="3" style="border:0;outline:0;background:0;width:100%;resize:vertical;font-size:14px">Moisture absorption during open transit changed the bulk density of the delivered fly ash, so the volume-based conversion overstated the received mass.</textarea>
        </div>
      </div>

      <div class="field">
        <label class="field__label" for="action">Proposed corrective action</label>
        <div class="ctrl" style="height:auto;padding:12px 14px">
          <textarea id="action" name="action" rows="3" style="border:0;outline:0;background:0;width:100%;resize:vertical;font-size:14px">Re-measure the silo level after a 2 hour settling period and request a fresh density test from Quality Control before the Goods Receipt is posted.</textarea>
        </div>
      </div>

      <div class="row2">
        <div class="field">
          <label class="field__label" for="cat">Category</label>
          <div class="ctrl"><select id="cat" name="cat">
            <option>Material characteristic (moisture)</option>
            <option>Measurement error</option>
            <option>Density parameter out of date</option>
            <option>Delivery ticket discrepancy</option>
            <option>Suspected document fraud</option>
          </select><?= icon('chev',18) ?></div>
        </div>
        <div class="field">
          <label class="field__label" for="pic">Person in charge</label>
          <div class="ctrl"><?= icon('user',20) ?><input id="pic" name="pic" value="Admin · Novia Rahmadina"></div>
        </div>
      </div>

      <div class="field">
        <label class="field__label" for="due">Target completion</label>
        <div class="ctrl"><?= icon('clock',20) ?><input id="due" name="due" type="date" value="2026-11-03"></div>
      </div>

      <label class="check"><input type="checkbox" checked>
        I confirm the inventory must not be adjusted automatically until this report is approved.</label>

      <div style="height:14px"></div>
      <div class="flex gap10">
        <button class="btn btn--danger" type="submit"><?= icon('alert',16) ?> Submit for approval</button>
        <a class="btn btn--ghost" href="<?= u('entry') ?>">Back to calculation</a>
      </div>
    </div>
  </form>

  <div style="display:flex;flex-direction:column;gap:18px">
    <div class="banner banner--red">
      <?= icon('alert',18) ?>
      <div><b>Why posting is blocked</b>
        Fly Ash belongs to the binder class, where the Internal Control Standard allows a maximum stock difference of ±1%.
        A variance of +3.60% has to be explained and approved before the inventory is updated.</div>
    </div>

    <div class="card">
      <div class="card__h"><div><h3>Report history</h3><p>Last reports raised at this plant</p></div></div>
      <div class="card__b card__b--flush">
        <table class="tbl">
          <thead><tr><th>REPORT</th><th>TICKET</th><th>VARIANCE</th><th>STATUS</th></tr></thead>
          <tbody>
          <?php foreach ($CARS as $c): ?>
            <tr>
              <td class="k"><?= e($c['no']) ?><div class="muted" style="font-size:11px"><?= e($c['raised']) ?></div></td>
              <td><?= e($c['tkt']) ?><div class="muted" style="font-size:11px"><?= e($c['material']) ?></div></td>
              <td class="num" style="color:var(--red-600);font-weight:600"><?= pct($c['variance']) ?></td>
              <td><?= chip($c['state']==='approved'?'Approved':'Waiting approval', $c['state']==='approved'?'navy':'gray') ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
