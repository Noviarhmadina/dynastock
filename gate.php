<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

/* mandatory fields of a delivery ticket — FR-05 */
$FIELDS = [
  ['Delivery ticket number', 'SJ-24114', true],
  ['Issue date and time',    '31 Oct 2026, 13:02', true],
  ['Supplier name',          'PT Varia Usaha', true],
  ['Vehicle plate number',   'AB 9902 VW', true],
  ['Driver name',            'Suyatno', true],
  ['Material description',   'Silica Fume', true],
  ['Stated quantity',        '9.80 t', true],
  ['Purchase order number',  'not written on the ticket', false],
];
$complete = count(array_filter($FIELDS, fn($f)=>$f[2]));

$PAGE_ID='receiving'; $PAGE_TITLE='Gate Verification — SJ-24114';
$PAGE_SUB='Security checks the vehicle and the delivery ticket before the material is unloaded';
$PAGE_BACK=['receiving','Back to Material Receiving'];
$PAGE_ACTIONS='<a class="btn btn--ghost btn--sm" href="'.u('receiving').'">Reject entry</a>
               <a class="btn btn--primary btn--sm" href="'.u('entry').'">'.icon('check',16).' Verify and send to admin</a>';
require __DIR__ . '/includes/header.php';
?>

<!-- FR-06 duplicate detection -->
<div class="banner banner--red">
  <?= icon('alert',18) ?>
  <div><b>Possible duplicate delivery ticket</b>
    Ticket <b style="display:inline">SJ-24114</b> from PT Varia Usaha with vehicle AB 9902 VW was already recorded today at 09:14 for Silica Fume.
    Confirm with the driver before verifying, or reject the entry. A duplicate Goods Receipt cannot be posted by the system.</div>
</div>

<div class="split">
  <div class="card">
    <div class="card__h"><div><h3>Delivery ticket completeness</h3><p>All mandatory fields must be present before the truck is unloaded</p></div>
      <span class="chip chip--<?= $complete===count($FIELDS)?'navy':'red' ?>"><?= $complete ?> of <?= count($FIELDS) ?> fields complete</span></div>
    <div class="card__b card__b--flush">
      <table class="tbl">
        <thead><tr><th>FIELD</th><th>VALUE ON THE TICKET</th><th>CHECK</th></tr></thead>
        <tbody>
        <?php foreach ($FIELDS as [$label,$val,$ok]): ?>
          <tr>
            <td class="k"><?= e($label) ?></td>
            <td style="color:<?= $ok?'var(--g-700)':'var(--g-400)' ?>"><?= e($val) ?></td>
            <td><?= chip($ok ? 'Present' : 'Missing', $ok ? 'navy' : 'red') ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:18px">
    <div class="card">
      <div class="card__h"><div><h3>Vehicle and driver</h3><p>Recorded at the gate</p></div></div>
      <div class="card__b">
        <div class="kv"><span>Arrival time</span><b>13:15, 31 Oct 2026</b></div>
        <div class="kv"><span>Vehicle</span><b>AB 9902 VW</b></div>
        <div class="kv"><span>Driver</span><b>Suyatno</b></div>
        <div class="kv"><span>Supplier</span><b>PT Varia Usaha</b></div>
        <div class="kv"><span>Checked by</span><b>Security · Budi S.</b></div>
      </div>
    </div>

    <div class="card">
      <div class="card__h"><div><h3>Security checklist</h3><p>Occupational health and safety</p></div></div>
      <div class="card__b">
        <label class="check"><input type="checkbox" checked> Driver wearing the required PPE</label>
        <label class="check"><input type="checkbox" checked> Vehicle parked in the designated unloading bay</label>
        <label class="check"><input type="checkbox" checked> Ticket photographed and attached</label>
        <label class="check"><input type="checkbox"> Seal number matches the ticket</label>
        <p class="hint" style="margin-top:10px">Data entry is performed in the office area after the physical inspection, never beside a moving vehicle.</p>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
