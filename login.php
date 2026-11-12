<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$err = '';
if (!STATIC_BUILD && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $role  = $_POST['role'] ?? 'admin';
    if ($email === '' || $pass === '') {
        $err = 'Enter your work email and password.';
    } elseif (!isset($ROLES[$role])) {
        $err = 'Choose a valid role.';
    } else {
        $name = ucwords(str_replace(['.', '_'], ' ', explode('@', $email)[0]));
        if ($name === '') $name = 'Plant User';
        $_SESSION['user'] = [
            'name' => $name, 'role' => $role,
            'label' => $ROLES[$role]['label'], 'initials' => initials($name),
        ];
        flash('Signed in as ' . $ROLES[$role]['label'] . '.', 'ok');
        header('Location: ' . u($ROLES[$role]['home']));
        exit;
    }
}
$logoW = file_get_contents(__DIR__ . '/assets/img/logo-white.svg');
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign in · DynaStock</title>
<link rel="icon" href="<?= asset('img/logo.svg') ?>">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="auth">

  <section class="auth__brand">
    <div>
      <a class="auth__logo" href="<?= u('index') ?>"><?= $logoW ?></a>
      <h2 class="auth__h1">Accurate stock without a weighbridge.</h2>
      <p class="auth__sub">DynaStock converts measured volume into verified tonnage, blocks duplicate receipts, and keeps
         plant inventory in step with the ERP.</p>
    </div>
    <div>
      <div class="feat"><div class="feat__ico"><?= icon('calc',20) ?></div>
        <div><div class="feat__t">Dynamic Density Calculator</div>
             <div class="feat__d">Volume to tonnage using approved density values (m = V × ρ).</div></div></div>
      <div class="feat"><div class="feat__ico"><?= icon('shield',20) ?></div>
        <div><div class="feat__t">Automated Validation Gate</div>
             <div class="feat__d">Blocks duplicate Goods Receipt and out-of-tolerance postings.</div></div></div>
      <div class="feat"><div class="feat__ico"><?= icon('chart',20) ?></div>
        <div><div class="feat__t">Real-time Stock Accuracy</div>
             <div class="feat__d">Physical vs recorded stock checked against ICS tolerance limits.</div></div></div>
    </div>
  </section>

  <section class="auth__form">
    <form class="auth__box" method="post" action="<?= u('login') ?>">
      <h1 class="auth__title">Sign in</h1>
      <p class="auth__lead">Enter your plant account to continue.</p>

      <?php if ($err): ?>
        <div class="flash flash--err" style="margin:22px 0 0"><?= icon('alert',18) ?><div><?= e($err) ?></div></div>
      <?php endif; ?>

      <div style="height:30px"></div>

      <div class="field">
        <label class="field__label" for="email">Work email</label>
        <div class="ctrl"><?= icon('mail',20) ?>
          <input id="email" name="email" type="email" value="novia.rahmadina@sbb.co.id" required>
        </div>
      </div>

      <div class="field">
        <label class="field__label" for="password">Password</label>
        <div class="ctrl"><?= icon('lock',20) ?>
          <input id="password" name="password" type="password" value="dynastock" required>
          <button type="button" class="pw-toggle" aria-label="Show password" style="border:0;background:0;cursor:pointer;color:var(--g-400)"><?= icon('eye',20) ?></button>
        </div>
      </div>

      <div class="field">
        <label class="field__label" for="role">Sign in as</label>
        <div class="ctrl"><?= icon('user',20) ?>
          <select id="role" name="role">
            <?php foreach ($ROLES as $k => $r): ?>
              <option value="<?= e($k) ?>"<?= $k === 'admin' ? ' selected' : '' ?>><?= e($r['label']) ?></option>
            <?php endforeach; ?>
          </select>
          <?= icon('chev',18) ?>
        </div>
      </div>

      <div class="field">
        <label class="field__label" for="plant">Plant / Site</label>
        <div class="ctrl"><?= icon('pin',20) ?>
          <select id="plant" name="plant">
            <option><?= e(PLANT) ?></option>
            <option>Semen Dynamix — Bandung</option>
            <option>Semen Dynamix — Semarang</option>
          </select>
          <?= icon('chev',18) ?>
        </div>
      </div>

      <div class="formrow">
        <label class="check" style="margin:0"><input type="checkbox" checked> Keep me signed in</label>
        <a class="link-red" href="#">Forgot password?</a>
      </div>

      <button class="btn btn--primary btn--block" type="submit">Sign in</button>

      <p class="auth__foot">Don't have an account? <a href="<?= u('register') ?>">Request access</a></p>

      <div class="auth__note"><?= icon('shield',18) ?>
        <div>Access is role-based. Every sign-in and transaction is recorded in the activity log.</div>
      </div>
    </form>
  </section>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
