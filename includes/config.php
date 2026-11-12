<?php
/* =====================================================================
   DynaStock — configuration & demo data
   Capstone Design Project · Optimisation of Inventory Management and
   Material Unloading · Semen Dynamix Batching Plant (no weighbridge)

   Prototype only: data lives in PHP arrays, there is no database.
   ===================================================================== */

define('APP_NAME', 'DynaStock');
define('APP_TAGLINE', 'Inventory Today. Stronger Tomorrow.');
define('PLANT', 'Semen Dynamix — Yogyakarta');

/* STATIC_BUILD is defined by build.php when exporting flat .html files */
if (!defined('STATIC_BUILD')) define('STATIC_BUILD', false);

/* ---------------------------------------------------------------
   Roles — FR-02 Role Based Access Control
   --------------------------------------------------------------- */
$ROLES = [
    'security'   => ['label' => 'Security',                 'home' => 'receiving'],
    'admin'      => ['label' => 'Administration (CES)',     'home' => 'dashboard'],
    'planthead'  => ['label' => 'Head of Batching Plant',   'home' => 'approvals'],
    'controlling'=> ['label' => 'Controlling / Inventory',  'home' => 'inventory'],
    'qc'         => ['label' => 'Quality Control',          'home' => 'density'],
];

/* which pages each role may open */
$PERMISSIONS = [
    'security'    => ['receiving','gate','dashboard'],
    'admin'       => ['dashboard','receiving','gate','entry','car','inventory','stock','reconciliation','approvals','materials','density','reports'],
    'planthead'   => ['dashboard','receiving','approvals','car','inventory','reconciliation','reports'],
    'controlling' => ['dashboard','inventory','stock','reconciliation','materials','reports','receiving'],
    'qc'          => ['dashboard','density','materials','inventory'],
];

/* ---------------------------------------------------------------
   Material master — FR-19, and the ICS tolerance limits from
   SOP IN.MT.02 / IN.MT.03 (Table 2.1 of the proposal) — FR-17
   --------------------------------------------------------------- */
$MATERIALS = [
    ['code'=>'MAT-CEM-01','name'=>'Cement',            'class'=>'Binder',    'unit'=>'ton','tol'=>1.0,'density'=>1.44,'stock'=>945.0,'cap'=>1200,'rop'=>420,'ss'=>120,'daily'=>62.0,'lead'=>3,'order_cost'=>1250000,'hold_cost'=>96000],
    ['code'=>'MAT-FLY-01','name'=>'Fly Ash',           'class'=>'Binder',    'unit'=>'ton','tol'=>1.0,'density'=>1.12,'stock'=>112.0,'cap'=>500, 'rop'=>180,'ss'=>60, 'daily'=>24.0,'lead'=>5,'order_cost'=>980000, 'hold_cost'=>72000],
    ['code'=>'MAT-SIL-01','name'=>'Silica Fume',       'class'=>'Binder',    'unit'=>'ton','tol'=>1.0,'density'=>0.65,'stock'=>24.0, 'cap'=>120, 'rop'=>30, 'ss'=>10, 'daily'=>3.2, 'lead'=>7,'order_cost'=>1400000,'hold_cost'=>150000],
    ['code'=>'MAT-CAG-01','name'=>'Coarse Aggregate',  'class'=>'Aggregate', 'unit'=>'ton','tol'=>2.0,'density'=>1.60,'stock'=>612.0,'cap'=>900, 'rop'=>300,'ss'=>90, 'daily'=>48.0,'lead'=>2,'order_cost'=>750000, 'hold_cost'=>42000],
    ['code'=>'MAT-FAG-01','name'=>'Fine Aggregate',    'class'=>'Aggregate', 'unit'=>'ton','tol'=>5.0,'density'=>1.55,'stock'=>486.0,'cap'=>800, 'rop'=>260,'ss'=>80, 'daily'=>41.0,'lead'=>2,'order_cost'=>720000, 'hold_cost'=>38000],
    ['code'=>'MAT-ADM-01','name'=>'Admixture',         'class'=>'Chemical',  'unit'=>'ton','tol'=>3.0,'density'=>1.08,'stock'=>8.4,  'cap'=>40,  'rop'=>9,  'ss'=>3,  'daily'=>1.1, 'lead'=>6,'order_cost'=>640000, 'hold_cost'=>210000],
    ['code'=>'MAT-FUE-01','name'=>'Fuel (Diesel)',     'class'=>'Fuel',      'unit'=>'ton','tol'=>2.0,'density'=>0.84,'stock'=>31.5, 'cap'=>60,  'rop'=>18, 'ss'=>6,  'daily'=>2.4, 'lead'=>3,'order_cost'=>520000, 'hold_cost'=>88000],
];

/* ---------------------------------------------------------------
   Density parameters — FR-22 (reference ASTM C29/C29M)
   --------------------------------------------------------------- */
$DENSITIES = [
    ['material'=>'Cement',           'value'=>1.44,'ref'=>'ASTM C29/C29M','tested'=>'2026-06-12','valid'=>'2026-12-12','by'=>'QC · Rian P.','status'=>'active'],
    ['material'=>'Fly Ash',          'value'=>1.12,'ref'=>'ASTM C29/C29M','tested'=>'2026-08-04','valid'=>'2027-02-18','by'=>'QC · Rian P.','status'=>'active'],
    ['material'=>'Fly Ash',          'value'=>1.09,'ref'=>'ASTM C29/C29M','tested'=>'2026-02-11','valid'=>'2026-08-10','by'=>'QC · Rian P.','status'=>'expired'],
    ['material'=>'Silica Fume',      'value'=>0.65,'ref'=>'ASTM C29/C29M','tested'=>'2026-07-02','valid'=>'2027-01-02','by'=>'QC · Dina S.','status'=>'active'],
    ['material'=>'Coarse Aggregate', 'value'=>1.60,'ref'=>'ASTM C29/C29M','tested'=>'2026-05-03','valid'=>'2026-11-03','by'=>'QC · Dina S.','status'=>'expiring'],
    ['material'=>'Fine Aggregate',   'value'=>1.55,'ref'=>'ASTM C29/C29M','tested'=>'2026-07-22','valid'=>'2027-01-22','by'=>'QC · Dina S.','status'=>'active'],
    ['material'=>'Admixture',        'value'=>1.08,'ref'=>'Supplier TDS',  'tested'=>'2026-09-01','valid'=>'2027-03-01','by'=>'QC · Rian P.','status'=>'active'],
    ['material'=>'Fuel (Diesel)',    'value'=>0.84,'ref'=>'ASTM D1298',    'tested'=>'2026-09-18','valid'=>'2027-03-18','by'=>'QC · Rian P.','status'=>'active'],
];

/* ---------------------------------------------------------------
   Receiving transactions — FR-04 … FR-12
   stated = tonnage written on the delivery ticket (Surat Jalan)
   volume / density produce the calculated tonnage (m = V x rho)
   --------------------------------------------------------------- */
$RECEIPTS = [
 ['tkt'=>'SJ-24108','time'=>'07:12','supplier'=>'PT Varia Usaha','vehicle'=>'AB 8821 CD','driver'=>'Suyatno','material'=>'Cement',          'volume'=>18.0,'density'=>1.44,'stated'=>25.84,'status'=>'posted',  'gr'=>'GR-500241','method'=>'Silo level gauge','by'=>'Admin · Novia R.','gate'=>'Security · Budi S.'],
 ['tkt'=>'SJ-24109','time'=>'08:05','supplier'=>'CV Mitra Beton','vehicle'=>'AB 1174 KL','driver'=>'Hartono','material'=>'Coarse Aggregate','volume'=>22.5,'density'=>1.60,'stated'=>35.56,'status'=>'posted',  'gr'=>'GR-500242','method'=>'Truck bin profile','by'=>'Admin · Novia R.','gate'=>'Security · Budi S.'],
 ['tkt'=>'SJ-24110','time'=>'09:48','supplier'=>'PT Sinar Abadi','vehicle'=>'AB 3390 MN','driver'=>'Marwan','material'=>'Fine Aggregate',  'volume'=>20.0,'density'=>1.55,'stated'=>29.58,'status'=>'posted',  'gr'=>'GR-500243','method'=>'Truck bin profile','by'=>'Admin · Novia R.','gate'=>'Security · Budi S.'],
 ['tkt'=>'SJ-24111','time'=>'10:22','supplier'=>'PT Varia Usaha','vehicle'=>'AB 7765 PQ','driver'=>'Suyatno','material'=>'Fly Ash',        'volume'=>15.0,'density'=>1.12,'stated'=>16.20,'status'=>'blocked', 'gr'=>null,      'method'=>'Silo level gauge','by'=>'Admin · Novia R.','gate'=>'Security · Budi S.'],
 ['tkt'=>'SJ-24112','time'=>'11:03','supplier'=>'CV Mitra Beton','vehicle'=>'AB 2218 RS','driver'=>'Hartono','material'=>'Cement',          'volume'=>19.2,'density'=>1.44,'stated'=>27.50,'status'=>'approval','gr'=>null,      'method'=>'Silo level gauge','by'=>'Admin · Novia R.','gate'=>'Security · Budi S.'],
 ['tkt'=>'SJ-24113','time'=>'12:30','supplier'=>'PT Sinar Abadi','vehicle'=>'AB 5540 TU','driver'=>'Marwan','material'=>'Fine Aggregate',  'volume'=>24.0,'density'=>null,'stated'=>37.40,'status'=>'density', 'gr'=>null,      'method'=>'Truck bin profile','by'=>'Admin · Novia R.','gate'=>'Security · Budi S.'],
 ['tkt'=>'SJ-24114','time'=>'13:15','supplier'=>'PT Varia Usaha','vehicle'=>'AB 9902 VW','driver'=>'Suyatno','material'=>'Silica Fume',    'volume'=>null,'density'=>null,'stated'=>9.80, 'status'=>'gate',    'gr'=>null,      'method'=>null,              'by'=>null,              'gate'=>null],
 ['tkt'=>'SJ-24115','time'=>'13:52','supplier'=>'CV Mitra Beton','vehicle'=>'AB 4417 XY','driver'=>'Hartono','material'=>'Admixture',      'volume'=>null,'density'=>null,'stated'=>4.20, 'status'=>'gate',    'gr'=>null,      'method'=>null,              'by'=>null,              'gate'=>null],
];

$STATUS_MAP = [
  'posted'   => ['Posted to ERP',         'navy'],
  'blocked'  => ['Exceeds tolerance',     'red'],
  'approval' => ['Awaiting approval',     'gray'],
  'density'  => ['Density pending',       'gray'],
  'gate'     => ['Awaiting verification', 'gray'],
];

/* ---------------------------------------------------------------
   Reconciliation — FR-16 / FR-18 (physical stock take vs recorded)
   --------------------------------------------------------------- */
$RECON = [
 ['material'=>'Cement',           'recorded'=>945.0,'physical'=>942.1,'tol'=>1.0,'date'=>'2026-10-30','by'=>'Controlling · Lia N.'],
 ['material'=>'Coarse Aggregate', 'recorded'=>612.0,'physical'=>601.4,'tol'=>2.0,'date'=>'2026-10-30','by'=>'Controlling · Lia N.'],
 ['material'=>'Fine Aggregate',   'recorded'=>486.0,'physical'=>467.2,'tol'=>5.0,'date'=>'2026-10-30','by'=>'Controlling · Lia N.'],
 ['material'=>'Fly Ash',          'recorded'=>112.0,'physical'=>106.9,'tol'=>1.0,'date'=>'2026-10-30','by'=>'Controlling · Lia N.'],
 ['material'=>'Silica Fume',      'recorded'=>24.0, 'physical'=>23.9, 'tol'=>1.0,'date'=>'2026-10-30','by'=>'Controlling · Lia N.'],
 ['material'=>'Admixture',        'recorded'=>8.4,  'physical'=>8.3,  'tol'=>3.0,'date'=>'2026-10-30','by'=>'Controlling · Lia N.'],
];

/* ---------------------------------------------------------------
   Corrective Action Reports — FR-12
   --------------------------------------------------------------- */
$CARS = [
 ['no'=>'CAR-2026-014','tkt'=>'SJ-24111','material'=>'Fly Ash','variance'=>3.60,'tol'=>1.0,
  'cause'=>'Moisture absorption during open transit changed the bulk density of the delivered fly ash.',
  'action'=>'Re-measure the silo level after 2 hours of settling and request a fresh density test from Quality Control before posting.',
  'pic'=>'Admin · Novia R.','raised'=>'2026-10-31 10:41','state'=>'waiting'],
 ['no'=>'CAR-2026-013','tkt'=>'SJ-24097','material'=>'Cement','variance'=>1.42,'tol'=>1.0,
  'cause'=>'Silo level gauge read before the material finished settling, so the measured volume was overstated.',
  'action'=>'Standardise a 30-minute settling window before volume measurement; added to the receiving work instruction.',
  'pic'=>'Admin · Novia R.','raised'=>'2026-10-28 15:10','state'=>'approved'],
];

/* ---------------------------------------------------------------
   Approval queue — FR-13
   --------------------------------------------------------------- */
$APPROVALS = [
 ['type'=>'Goods Receipt','ref'=>'SJ-24112','material'=>'Cement','detail'=>'27.65 t calculated · variance +0.55%','age'=>'2 h','risk'=>'low'],
 ['type'=>'Corrective Action','ref'=>'CAR-2026-014','material'=>'Fly Ash','detail'=>'Variance +3.60% against ±1.00%','age'=>'4 h','risk'=>'high'],
 ['type'=>'Goods Receipt','ref'=>'SJ-24104','material'=>'Fine Aggregate','detail'=>'30.20 t calculated · variance +3.90%','age'=>'1 d','risk'=>'low'],
 ['type'=>'Density change','ref'=>'DEN-0042','material'=>'Coarse Aggregate','detail'=>'1.60 → 1.58 t/m³ after new lab test','age'=>'1 d','risk'=>'medium'],
 ['type'=>'Goods Receipt','ref'=>'SJ-24101','material'=>'Admixture','detail'=>'4.18 t calculated · variance +2.10%','age'=>'2 d','risk'=>'low'],
 ['type'=>'Stock adjustment','ref'=>'ADJ-0117','material'=>'Silica Fume','detail'=>'−0.10 t after physical stock take','age'=>'2 d','risk'=>'low'],
 ['type'=>'Goods Receipt','ref'=>'SJ-24099','material'=>'Coarse Aggregate','detail'=>'35.90 t calculated · variance +1.10%','age'=>'3 d','risk'=>'low'],
];

/* ---------------------------------------------------------------
   Stock ledger for the stock card — FR-15
   --------------------------------------------------------------- */
$LEDGER = [
 ['date'=>'2026-10-31 11:03','doc'=>'SJ-24112','type'=>'Goods Receipt','in'=>27.65,'out'=>0,   'note'=>'Awaiting approval'],
 ['date'=>'2026-10-31 06:00','doc'=>'PRD-8841','type'=>'Production issue','in'=>0,  'out'=>58.20,'note'=>'Batch order 4412'],
 ['date'=>'2026-10-30 16:40','doc'=>'STK-1030','type'=>'Stock take','in'=>0,        'out'=>2.90, 'note'=>'Variance −0.31% · within ±1%'],
 ['date'=>'2026-10-30 07:12','doc'=>'SJ-24108','type'=>'Goods Receipt','in'=>25.92,'out'=>0,    'note'=>'Posted to ERP'],
 ['date'=>'2026-10-29 06:10','doc'=>'PRD-8829','type'=>'Production issue','in'=>0,  'out'=>61.40,'note'=>'Batch order 4405'],
 ['date'=>'2026-10-28 08:20','doc'=>'SJ-24097','type'=>'Goods Receipt','in'=>26.40,'out'=>0,    'note'=>'CAR-2026-013 approved'],
];

/* weekly receiving chart */
$WEEK = [['Mon',310,18],['Tue',365,9],['Wed',290,24],['Thu',402,6],['Fri',348,30],['Sat',420,12],['Sun',255,8]];

/* notifications — FR-25 */
$NOTIFS = [
 ['Fly Ash is below its reorder point (112 t / 180 t)','4 m','red'],
 ['SJ-24111 blocked by the validation gate','18 m','red'],
 ['Coarse Aggregate density expires on 03 Nov 2026','2 h','gray'],
 ['GR-500243 synchronised with the ERP','3 h','navy'],
];
