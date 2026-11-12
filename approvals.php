<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$PAGE_ID='approvals'; $PAGE_TITLE='Approvals';
$PAGE_SUB='Items waiting for a decision from the Head of Batching Plant before they reach the inventory record';
$PAGE_ACTIONS='<a class="btn btn--ghost btn--sm" href="#">Approve all low risk</a>
               <a class="btn btn--primary btn--sm" href="'.u('reports').'">'.icon('down',16).' Export queue</a>';
require __DIR__ . '/includes/header.php';

$risk = ['high'=>['High risk','red'],'medium'=>['Medium risk','gray'],'low'=>['Low risk','navy']];
?>

<div class="tabs">
  <a class="tab on" href="#">All <i><?= count($APPROVALS) ?></i></a>
  <a class="tab" href="#">Goods Receipt <i>4</i></a>
  <a class="tab" href="#">Corrective Action <i>1</i></a>
  <a class="tab" href="#">Density change <i>1</i></a>
  <a class="tab" href="#">Stock adjustment <i>1</i></a>
</div>

<div class="card">
  <div class="card__h"><div><h3>Approval queue</h3><p>Sorted by age — items older than 24 hours are highlighted</p></div>
    <label class="top__search" style="width:240px"><?= icon('search',16) ?><input data-filter="#apprTable" placeholder="Filter reference…"></label></div>
  <div class="card__b card__b--flush">
    <table class="tbl" id="apprTable">
      <thead><tr><th>TYPE</th><th>REFERENCE</th><th>MATERIAL</th><th>DETAIL</th><th>WAITING</th><th>RISK</th><th>DECISION</th></tr></thead>
      <tbody>
      <?php foreach ($APPROVALS as $a): [$rl,$rt] = $risk[$a['risk']]; ?>
        <tr>
          <td class="k"><?= e($a['type']) ?></td>
          <td><?= e($a['ref']) ?></td>
          <td><?= e($a['material']) ?></td>
          <td class="muted"><?= e($a['detail']) ?></td>
          <td class="num" style="<?= str_contains($a['age'],'d')?'color:var(--red-600);font-weight:600':'' ?>"><?= e($a['age']) ?></td>
          <td><?= chip($rl,$rt) ?></td>
          <td>
            <span class="flex gap8">
              <a class="btn btn--primary btn--sm" href="<?= u('receiving') ?>">Approve</a>
              <a class="btn btn--ghost btn--sm" href="<?= u('car') ?>">Reject</a>
            </span>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="split">
  <div class="card">
    <div class="card__h"><div><h3>CAR-2026-014 — Fly Ash</h3><p>Raised from SJ-24111 · variance +3.60% against ±1.00%</p></div>
      <span class="chip chip--red">High risk</span></div>
    <div class="card__b">
      <div class="kv"><span>Root cause</span><b style="max-width:60%;text-align:right;font-weight:400"><?= e($CARS[0]['cause']) ?></b></div>
      <div class="kv"><span>Proposed action</span><b style="max-width:60%;text-align:right;font-weight:400"><?= e($CARS[0]['action']) ?></b></div>
      <div class="kv"><span>Person in charge</span><b><?= e($CARS[0]['pic']) ?></b></div>
      <div class="kv"><span>Raised</span><b><?= e($CARS[0]['raised']) ?></b></div>
      <div class="flex gap10" style="margin-top:16px">
        <a class="btn btn--primary" href="<?= u('receiving') ?>"><?= icon('check',16) ?> Approve and allow posting</a>
        <a class="btn btn--ghost" href="<?= u('car') ?>">Return for revision</a>
      </div>
    </div>
  </div>
  <div class="banner banner--gray">
    <?= icon('shield',18) ?>
    <div><b>Separation of duties</b>
      The person who records the receiving transaction cannot approve it. Approval sits with the Head of Batching Plant,
      and stock corrections outside the tolerance limit always need an approved Corrective Action Report first.</div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
