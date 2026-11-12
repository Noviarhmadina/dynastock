<?php
/* app shell — expects $PAGE_ID, $PAGE_TITLE, optional $PAGE_SUB, $PAGE_ACTIONS, $PAGE_BACK */
require_login();
$U = current_user();
$logo = file_get_contents(__DIR__ . '/../assets/img/logo-white.svg');

$NAVGROUPS = [
  'OPERATIONS' => [
    ['dashboard','Dashboard','grid',null],
    ['receiving','Material Receiving','truck','18'],
    ['inventory','Inventory','box',null],
    ['reconciliation','Stock Reconciliation','scale',null],
    ['approvals','Approvals','check-circle','7'],
  ],
  'MASTER DATA' => [
    ['materials','Materials','layers',null],
    ['density','Density Parameters','flask',null],
  ],
  'REPORTING' => [
    ['reports','Reports','report',null],
  ],
];
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($PAGE_TITLE ?? 'DynaStock') ?> · DynaStock</title>
<meta name="description" content="DynaStock — inventory management and material unloading for a batching plant without a weighbridge.">
<link rel="icon" href="<?= asset('img/logo.svg') ?>">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="app">

  <aside class="side">
    <div class="side__logo"><a href="<?= u('dashboard') ?>" aria-label="DynaStock home"><?= $logo ?></a></div>
    <?php foreach ($NAVGROUPS as $group => $items): ?>
      <div class="side__sec"><?= e($group) ?></div>
      <nav class="nav">
      <?php foreach ($items as [$id,$label,$ico,$badge]):
            $allowed = can($id); ?>
        <a class="nav__i<?= ($PAGE_ID ?? '') === $id ? ' on' : '' ?>"
           href="<?= $allowed ? u($id) : '#' ?>"
           <?= $allowed ? '' : 'title="Not available for your role" style="opacity:.42;pointer-events:none"' ?>>
          <?= icon($ico) ?><span><?= e($label) ?></span>
          <?php if ($badge): ?><em class="nav__b"><?= e($badge) ?></em><?php endif; ?>
        </a>
      <?php endforeach; ?>
      </nav>
    <?php endforeach; ?>

    <div class="side__site">
      <span>ACTIVE SITE</span>
      <b><?= e(PLANT) ?></b>
      <i>No weighbridge · volume-based</i>
    </div>
  </aside>

  <div class="main">
    <header class="top">
      <label class="top__search">
        <?= icon('search',17) ?>
        <input type="search" placeholder="Search delivery ticket, material, GR number…" aria-label="Search">
      </label>
      <div class="grow"></div>

      <button class="iconbtn" id="notifBtn" aria-label="Notifications"><?= icon('bell',18) ?><i class="dot"></i></button>
      <button class="iconbtn" aria-label="Settings"><?= icon('gear',18) ?></button>

      <div class="user">
        <div class="avatar"><?= e($U['initials']) ?></div>
        <div>
          <b><?= e($U['name']) ?></b>
          <span><?= e($U['label']) ?></span>
        </div>
        <?= icon('chev',16) ?>
      </div>
      <a class="iconbtn" href="<?= u('logout') ?>" title="Sign out" aria-label="Sign out"><?= icon('logout',18) ?></a>
    </header>

    <div id="notifPanel" class="card hide" style="position:absolute;right:28px;top:72px;width:340px;z-index:40;box-shadow:var(--sh-lg)">
      <div class="card__h"><div><h3>Notifications</h3><p>4 unread</p></div></div>
      <div class="card__b" style="display:flex;flex-direction:column;gap:10px">
        <?php foreach ($NOTIFS as [$msg,$age,$tone]): ?>
          <div class="flex gap10" style="align-items:flex-start">
            <span class="chip chip--<?= $tone ?>" style="margin-top:2px"></span>
            <div style="font-size:12.5px;line-height:1.5"><?= e($msg) ?><div class="muted" style="font-size:11px"><?= e($age) ?> ago</div></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <main class="content">
      <?php show_flash(); ?>
      <?php if (!empty($PAGE_BACK)): ?>
        <a class="back" href="<?= u($PAGE_BACK[0]) ?>"><?= icon('left',16) ?><?= e($PAGE_BACK[1]) ?></a>
      <?php endif; ?>
      <div class="phead">
        <div>
          <h1><?= e($PAGE_TITLE ?? '') ?></h1>
          <?php if (!empty($PAGE_SUB)): ?><p><?= e($PAGE_SUB) ?></p><?php endif; ?>
        </div>
        <?php if (!empty($PAGE_ACTIONS)): ?><div class="phead__a"><?= $PAGE_ACTIONS ?></div><?php endif; ?>
      </div>
