<?php
/* =====================================================================
   DynaStock — helpers
   ===================================================================== */

if (session_status() === PHP_SESSION_NONE && !STATIC_BUILD) session_start();

/* ---- url helper: .php when running on a server, .html in a static export */
function u(string $page, string $qs = ''): string {
    if (STATIC_BUILD) return $page . '.html';
    return $page . '.php' . ($qs ? '?' . $qs : '');
}
function asset(string $p): string { return 'assets/' . ltrim($p, '/'); }
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/* ---- session / auth -------------------------------------------------- */
function current_user(): array {
    global $ROLES;
    if (STATIC_BUILD) {
        return ['name' => 'Novia Rahmadina', 'role' => 'admin', 'label' => $ROLES['admin']['label'], 'initials' => 'NR'];
    }
    if (!empty($_SESSION['user'])) return $_SESSION['user'];
    return ['name' => 'Novia Rahmadina', 'role' => 'admin', 'label' => $ROLES['admin']['label'], 'initials' => 'NR'];
}
function is_logged_in(): bool { return STATIC_BUILD ? true : !empty($_SESSION['user']); }
function require_login(): void {
    if (STATIC_BUILD) return;
    if (!is_logged_in()) { header('Location: ' . u('login')); exit; }
}
function can(string $page): bool {
    global $PERMISSIONS;
    $r = current_user()['role'] ?? 'admin';
    return in_array($page, $PERMISSIONS[$r] ?? [], true);
}
function initials(string $name): string {
    $p = preg_split('/\s+/', trim($name));
    return strtoupper(substr($p[0] ?? 'U', 0, 1) . substr($p[1] ?? '', 0, 1));
}

/* ---- numbers --------------------------------------------------------- */
function t_(?float $v, int $d = 2): string { return $v === null ? '—' : number_format($v, $d, '.', ','); }
function pct(?float $v, int $d = 2): string {
    if ($v === null) return '—';
    return ($v >= 0 ? '+' : '−') . number_format(abs($v), $d, '.', ',') . '%';
}

/* core conversion of the proposal: m = V × ρ  (FR-09) */
function tonnage(?float $volume, ?float $density): ?float {
    if ($volume === null || $density === null) return null;
    return round($volume * $density, 2);
}
/* variance of calculated tonnage against the delivery ticket */
function variance(?float $calc, ?float $stated): ?float {
    if ($calc === null || !$stated) return null;
    return round((($calc - $stated) / $stated) * 100, 2);
}
/* FR-10 automated validation gate */
function gate_pass(?float $var, float $tol): ?bool {
    if ($var === null) return null;
    return abs($var) <= $tol;
}

/* Economic Order Quantity — FR-21, EOQ = sqrt(2DS/H) */
function eoq(float $annual_demand, float $order_cost, float $hold_cost): float {
    if ($hold_cost <= 0) return 0;
    return round(sqrt((2 * $annual_demand * $order_cost) / $hold_cost), 0);
}
/* Reorder Point — FR-20, ROP = (d × L) + SS */
function rop(float $daily, int $lead, float $safety): float {
    return round($daily * $lead + $safety, 1);
}

function material(string $name): ?array {
    global $MATERIALS;
    foreach ($MATERIALS as $m) if ($m['name'] === $name) return $m;
    return null;
}
function tolerance_of(string $name): float { return material($name)['tol'] ?? 1.0; }

/* ---- small view helpers ---------------------------------------------- */
function chip(string $text, string $tone = 'gray'): string {
    return '<span class="chip chip--' . $tone . '">' . e($text) . '</span>';
}
function status_chip(string $status): string {
    global $STATUS_MAP;
    [$label, $tone] = $STATUS_MAP[$status] ?? [$status, 'gray'];
    return chip($label, $tone);
}
function flash(string $msg, string $type = 'ok'): void {
    if (STATIC_BUILD) return;
    $_SESSION['flash'] = ['m' => $msg, 't' => $type];
}
function show_flash(): void {
    if (STATIC_BUILD || empty($_SESSION['flash'])) return;
    $f = $_SESSION['flash']; unset($_SESSION['flash']);
    echo '<div class="flash flash--' . e($f['t']) . '">' . icon($f['t'] === 'ok' ? 'check' : 'alert', 18) . '<div>' . e($f['m']) . '</div></div>';
}

/* ---- icon set (inline SVG, inherits currentColor) --------------------- */
function icon(string $name, int $size = 19): string {
    $p = [
    'grid'  =>'<rect x="3.2" y="3.2" width="7.6" height="7.6" rx="2"/><rect x="13.2" y="3.2" width="7.6" height="7.6" rx="2"/><rect x="3.2" y="13.2" width="7.6" height="7.6" rx="2"/><rect x="13.2" y="13.2" width="7.6" height="7.6" rx="2"/>',
    'truck' =>'<path d="M2.8 6.5h10.4v9.8H2.8z"/><path d="M13.2 10h3.9l3.1 3.3v3h-7z"/><circle cx="7" cy="18.4" r="1.9"/><circle cx="16.8" cy="18.4" r="1.9"/>',
    'box'   =>'<path d="M12 3.2 20.2 7.6v8.8L12 20.8 3.8 16.4V7.6z"/><path d="m3.8 7.6 8.2 4.4 8.2-4.4M12 12v8.8"/>',
    'scale' =>'<path d="M12 3.4v17.2M6 20.6h12M4 8.6h16M4 8.6 1.8 14h4.4L4 8.6ZM20 8.6 17.8 14h4.4L20 8.6ZM12 3.4 4 8.6M12 3.4l8 5.2"/>',
    'check-circle'=>'<circle cx="12" cy="12" r="8.8"/><path d="m8.4 12.2 2.5 2.5 4.7-4.9"/>',
    'layers'=>'<path d="m12 3.2 8.6 4.5-8.6 4.5-8.6-4.5z"/><path d="m3.4 12.3 8.6 4.5 8.6-4.5M3.4 16.6l8.6 4.5 8.6-4.5"/>',
    'flask' =>'<path d="M9.6 3.2h4.8M10.6 3.2v5.6L5.4 17.6c-.9 1.6.2 3.2 1.9 3.2h9.4c1.7 0 2.8-1.6 1.9-3.2L13.4 8.8V3.2"/><path d="M7.6 15h8.8"/>',
    'report'=>'<path d="M14 2.8H6.6a2 2 0 0 0-2 2v14.4a2 2 0 0 0 2 2h10.8a2 2 0 0 0 2-2V8.2z"/><path d="M14 2.8V8.2h5.4M8.6 13h6.8M8.6 16.6h4.6"/>',
    'search'=>'<circle cx="11" cy="11" r="6.8"/><path d="m16.2 16.2 4 4"/>',
    'bell'  =>'<path d="M18 9.4a6 6 0 1 0-12 0c0 5-2 6.6-2 6.6h16s-2-1.6-2-6.6Z"/><path d="M13.7 19.2a2 2 0 0 1-3.4 0"/>',
    'gear'  =>'<circle cx="12" cy="12" r="3.1"/><circle cx="12" cy="12" r="8.4"/><path d="M12 3.6v3M12 17.4v3M3.6 12h3M17.4 12h3"/>',
    'chev'  =>'<path d="m6 9.5 6 6 6-6"/>',
    'left'  =>'<path d="m15 6-6 6 6 6"/>',
    'right' =>'<path d="m9 6 6 6-6 6"/>',
    'plus'  =>'<path d="M12 5v14M5 12h14"/>',
    'check' =>'<path d="m5 12.5 4.5 4.5L19 7"/>',
    'alert' =>'<path d="M12 3.6 1.8 20.4h20.4L12 3.6Z"/><path d="M12 10v4.2"/><circle cx="12" cy="17.2" r="1.1" fill="currentColor" stroke="none"/>',
    'down'  =>'<path d="M12 4.4v12.4M7 12l5 5 5-5M4.4 20.4h15.2"/>',
    'up'    =>'<path d="m5.5 14.5 5-5 3.5 3.5 5-5"/><path d="M15 8h4v4"/>',
    'dn'    =>'<path d="m5.5 9.5 5 5 3.5-3.5 5 5"/><path d="M15 16h4v-4"/>',
    'mail'  =>'<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m4 8 8 5.5L20 8"/>',
    'lock'  =>'<rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7.5a4 4 0 0 1 8 0V10"/>',
    'eye'   =>'<path d="M2.5 12S6 6.5 12 6.5 21.5 12 21.5 12 18 17.5 12 17.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="2.8"/>',
    'user'  =>'<circle cx="12" cy="8" r="3.6"/><path d="M4.5 20c.8-4 3.9-6 7.5-6s6.7 2 7.5 6"/>',
    'id'    =>'<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><circle cx="8.5" cy="11" r="2.2"/><path d="M5 16.3c.5-1.6 1.9-2.4 3.5-2.4s3 .8 3.5 2.4M14.5 10h4M14.5 13.5h4"/>',
    'pin'   =>'<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/>',
    'calc'  =>'<rect x="4" y="2.5" width="16" height="19" rx="2.5"/><rect x="7.5" y="6" width="9" height="3.6" rx="1"/><path d="M8.8 13.6h.01M12 13.6h.01M15.2 13.6h.01M8.8 17.4h.01M12 17.4h.01M15.2 17.4h.01" stroke-width="2.4" stroke-linecap="round"/>',
    'shield'=>'<path d="M12 2.8 5 5.8v6.1c0 4.1 3 7.7 7 9.3 4-1.6 7-5.2 7-9.3V5.8l-7-3Z"/><path d="m9 12 2.2 2.2L15.4 10"/>',
    'chart' =>'<path d="M4 20.2h16"/><path d="M7.3 17.4V11M12 17.4V6.6M16.7 17.4v-4"  stroke-width="2.6" stroke-linecap="round"/>',
    'cart'  =>'<path d="M2.6 3.4h2.6l2.4 11.4h9.6l2.2-8.4H6.3"/><circle cx="9.4" cy="19.4" r="1.6"/><circle cx="16.6" cy="19.4" r="1.6"/>',
    'dots'  =>'<circle cx="5.5" cy="12" r="1.5" fill="currentColor" stroke="none"/><circle cx="12" cy="12" r="1.5" fill="currentColor" stroke="none"/><circle cx="18.5" cy="12" r="1.5" fill="currentColor" stroke="none"/>',
    'logout'=>'<path d="M15.5 16.5 20 12l-4.5-4.5M20 12H9M12 20H6.4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2H12"/>',
    'clock' =>'<circle cx="12" cy="12" r="8.8"/><path d="M12 7v5.4l3.4 2"/>',
    ];
    $d = $p[$name] ?? $p['box'];
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
}

/* donut chart as inline svg */
function donut(array $slices, int $size = 176, float $thickness = 0.34): string {
    $r = $size / 2; $ir = $r * (1 - $thickness); $cx = $r; $cy = $r;
    $total = array_sum(array_column($slices, 1)); if ($total <= 0) $total = 1;
    $a = -M_PI / 2; $out = '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">';
    foreach ($slices as $s) {
        $sw = ($s[1] / $total) * 2 * M_PI;
        $a0 = $a + 0.018; $a1 = $a + $sw - 0.018; $a += $sw;
        if ($a1 <= $a0) continue;
        $large = ($a1 - $a0) > M_PI ? 1 : 0;
        $x0 = $cx + $r * cos($a0);  $y0 = $cy + $r * sin($a0);
        $x1 = $cx + $r * cos($a1);  $y1 = $cy + $r * sin($a1);
        $x2 = $cx + $ir * cos($a1); $y2 = $cy + $ir * sin($a1);
        $x3 = $cx + $ir * cos($a0); $y3 = $cy + $ir * sin($a0);
        $out .= sprintf('<path d="M%.2f %.2f A%.2f %.2f 0 %d 1 %.2f %.2f L%.2f %.2f A%.2f %.2f 0 %d 0 %.2f %.2f Z" fill="%s"/>',
            $x0,$y0,$r,$r,$large,$x1,$y1,$x2,$y2,$ir,$ir,$large,$x3,$y3,$s[2]);
    }
    return $out . '</svg>';
}
