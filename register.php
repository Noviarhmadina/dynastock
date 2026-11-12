<?php
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/functions.php';

$err = ''; $ok = false;
if (!STATIC_BUILD && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $nik  = trim($_POST['nik'] ?? '');
    $mail = trim($_POST['email'] ?? '');
    $p1   = $_POST['password'] ?? '';
    $p2   = $_POST['confirm'] ?? '';
    if ($name === '' || $nik === '' || $mail === '') $err = 'Full name, employee ID and work email are required.';
    elseif (strlen($p1) < 8)                         $err = 'The password must be at least 8 characters long.';
    elseif ($p1 !== $p2)                             $err = 'The two passwords do not match.';
    elseif (empty($_POST['sop']))                    $err = 'You must agree to follow SOP IN.MT.02 and IN.MT.03.';
    else $ok = true;
}
$logoW = file_get_contents(__DIR__ . '/assets/img/logo-white.svg');
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Create account · DynaStock</title>
<link rel="icon" href="<?= asset('img/logo.svg') ?>">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="auth">

  <section class="auth__brand">
    <div>
      <a class="auth__logo" href="<?= u('index') ?>"><?= $logoW ?></a>
      <h2 class="auth__h1">Request your plant account.</h2>
      <p class="auth__sub">Accounts are tied to a role and a site. Your access level decides which receiving, inventory
         and approval screens you can open.</p>
    </div>
    <div>
      <div class="feat"><div class="feat__ico"><?= icon('id',20) ?></div>
        <div><div class="feat__t">Verified by employee ID</div>
             <div class="feat__d">Your NIK is matched against the plant personnel list.</div></div></div>
      <div class="feat"><div class="feat__ico"><?= icon('shield',20) ?></div>
        <div><div class="feat__t">Role-based permission</div>
             <div class="feat__d">Security, Admin, Plant Head, Controlling and QC each get a different workspace.</div></div></div>
      <div class="feat"><div class="feat__ico"><?= icon('check',20) ?></div>
        <div><div class="feat__t">Approved before active</div>
             <div class="feat__d">The Head of Batching Plant activates the account before first sign-in.</div></div></div>
    </div>
  </section>

  <section class="auth__form">
    <form class="auth__box auth__box--wide" method="post" action="<?= u('register') ?>">
      <h1 class="auth__title">Create account</h1>
      <p class="auth__lead">Fill in your work identity. Activation needs approval from the Plant Head.</p>

      <?php if ($ok): ?>
        <div class="flash flash--ok" style="margin:22px 0 0"><?= icon('check',18) ?>
          <div><b>Request submitted.</b><br>The Head of Batching Plant will review and activate your account. You will be notified by email.</div></div>
      <?php elseif ($err): ?>
        <div class="flash flash--err" style="margin:22px 0 0"><?= icon('alert',18) ?><div><?= e($err) ?></div></div>
      <?php endif; ?>

      <div style="height:28px"></div>

      <div class="row2">
        <div class="field">
          <label class="field__label" for="name">Full name</label>
          <div class="ctrl"><?= icon('user',20) ?><input id="name" name="name" value="Novia Rahmadina Yuwana" required></div>
        </div>
        <div class="field">
          <label class="field__label" for="nik">Employee ID (NIK)</label>
          <div class="ctrl"><?= icon('id',20) ?><input id="nik" name="nik" value="SBB-2026-0281" required></div>
        </div>
      </div>

      <div class="field">
        <label class="field__label" for="mail">Work email</label>
        <div class="ctrl"><?= icon('mail',20) ?><input id="mail" name="email" type="email" value="novia.rahmadina@sbb.co.id" required></div>
      </div>

      <div class="row2">
        <div class="field">
          <label class="field__label" for="dept">Department / Role</label>
          <div class="ctrl"><select id="dept" name="role">
            <?php foreach ($ROLES as $k => $r): ?><option value="<?= e($k) ?>"<?= $k==='admin'?' selected':'' ?>><?= e($r['label']) ?></option><?php endforeach; ?>
          </select><?= icon('chev',18) ?></div>
        </div>
        <div class="field">
          <label class="field__label" for="site">Plant / Site</label>
          <div class="ctrl"><select id="site" name="plant">
            <option><?= e(PLANT) ?></option><option>Semen Dynamix — Bandung</option><option>Semen Dynamix — Semarang</option>
          </select><?= icon('chev',18) ?></div>
        </div>
      </div>

      <div class="row2">
        <div class="field">
          <label class="field__label" for="pw">Password</label>
          <div class="ctrl"><?= icon('lock',20) ?><input id="pw" name="password" type="password" value="dynastock">
            <button type="button" class="pw-toggle" aria-label="Show password" style="border:0;background:0;cursor:pointer;color:var(--g-400)"><?= icon('eye',20) ?></button></div>
        </div>
        <div class="field">
          <label class="field__label" for="pw2">Confirm password</label>
          <div class="ctrl"><?= icon('lock',20) ?><input id="pw2" name="confirm" type="password" value="dynastock">
            <button type="button" class="pw-toggle" aria-label="Show password" style="border:0;background:0;cursor:pointer;color:var(--g-400)"><?= icon('eye',20) ?></button></div>
        </div>
      </div>
      <p class="hint">Minimum 8 characters, with one number and one uppercase letter.</p>

      <label class="check"><input type="checkbox" name="sop" checked>
        I agree to follow SOP IN.MT.02 and IN.MT.03 and the Internal Control Standard for material receiving.</label>
      <label class="check"><input type="checkbox" name="conf">
        I understand that company data accessed through this system is confidential.</label>

      <div style="height:14px"></div>
      <button class="btn btn--primary btn--block" type="submit">Create account</button>
      <p class="auth__foot">Already registered? <a href="<?= u('login') ?>">Sign in</a></p>
    </form>
  </section>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
