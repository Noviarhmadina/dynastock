<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';
$logo = file_get_contents(__DIR__ . '/assets/img/logo.svg');
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>DynaStock — Inventory Today. Stronger Tomorrow.</title>
<meta name="description" content="DynaStock converts measured volume into verified tonnage, blocks duplicate receipts and keeps batching-plant inventory accurate without a weighbridge.">
<link rel="icon" href="<?= asset('img/logo.svg') ?>">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<!-- ============ intro: dots sweep in and assemble the logo ============ -->
<div class="intro" id="intro">
  <div class="intro__stage">
    <canvas id="introCanvas" data-src="<?= asset('img/logo.svg') ?>" aria-label="DynaStock"></canvas>
    <div class="intro__tag" id="introTag">INVENTORY TODAY. STRONGER TOMORROW.</div>
    <div class="intro__bar"><i id="introBar"></i></div>
  </div>
  <button class="intro__skip" id="introSkip" type="button">Skip intro</button>
</div>

<!-- ============ landing ============ -->
<main class="land">
  <div class="land__box">
    <div class="land__logo"><?= $logo ?></div>
    <h1>Accurate stock without a weighbridge.</h1>
    <p>DynaStock converts measured volume into verified tonnage, blocks duplicate Goods Receipts and keeps the inventory
       of the <?= e(PLANT) ?> batching plant aligned with the Internal Control Standard.</p>

    <div class="land__a">
      <a class="btn btn--primary" href="<?= u('login') ?>"><?= icon('lock',17) ?> Sign in</a>
      <a class="btn btn--ghost" href="<?= u('register') ?>"><?= icon('user',17) ?> Request access</a>
      <a class="btn btn--ghost" href="<?= u('dashboard') ?>"><?= icon('grid',17) ?> Open demo dashboard</a>
    </div>

    <div class="land__f">
      <div><em><?= icon('calc',19) ?></em><b>Dynamic Density Calculator</b>
        <span>Measured volume is converted with m&nbsp;=&nbsp;V&nbsp;×&nbsp;ρ using only approved laboratory density values.</span></div>
      <div><em><?= icon('shield',19) ?></em><b>Automated Validation Gate</b>
        <span>Duplicate delivery tickets and out-of-tolerance receipts are blocked before they reach the ERP.</span></div>
      <div><em><?= icon('chart',19) ?></em><b>Real-time Stock Accuracy</b>
        <span>Physical and recorded stock are reconciled against the ICS tolerance limit for every material class.</span></div>
    </div>

    <p style="font-size:12px;color:var(--g-400);margin-top:34px">
      Prototype for the Capstone Design Project · S1 Sistem Informasi, Universitas Telkom · demo data only
    </p>
  </div>
</main>

<script src="<?= asset('js/intro.js') ?>"></script>
</body>
</html>
