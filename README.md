# DynaStock Inventory Management System (web prototype)

Prototype for the Capstone Design Project
**Optimisation of Inventory Management and Material Unloading to Improve Operational Efficiency
in Sustainable Concrete Manufacturing** — Semen Dynamix batching plant (no weighbridge).

Built with plain **PHP 8 + HTML + CSS + vanilla JS**. No database, no framework, no build step.
All data lives in `includes/config.php` so the screens can be demonstrated offline.

---

## How to run

### Option A — XAMPP / Laragon (Windows)
1. Copy the `dynastock` folder into `C:\xampp\htdocs\`
2. Start Apache from the XAMPP control panel
3. Open <http://localhost/dynastock/>

### Option B — PHP built-in server (fastest)
```bash
cd dynastock
php -S localhost:8000
```
Then open <http://localhost:8000>

Demo sign-in: any email and password work. Pick a role in the **Sign in as** dropdown to see
role-based access change the menu.

---

## Pages and the functional requirements they cover

| File | Screen | FR |
|---|---|---|
| `index.php` | Homepage, particle intro that assembles the logo (~3.6 s) | — |
| `login.php` | Sign in, role and site selection | FR-01, FR-02 |
| `register.php` | Request access, NIK, role, SOP consent | FR-01, FR-02 |
| `dashboard.php` | KPI, stock composition, weekly receiving, stock levels | FR-14, FR-18, FR-20 |
| `receiving.php` | Receiving transaction list with status tabs | FR-04, FR-11 |
| `gate.php` | Delivery ticket completeness and duplicate detection | FR-05, FR-06 |
| `entry.php` | Volume entry, approved density, **live calculator**, validation gate | FR-07, FR-08, FR-09, FR-10 |
| `car.php` | Corrective Action Report | FR-12 |
| `approvals.php` | Approval queue for the Head of Batching Plant | FR-13 |
| `inventory.php` | Stock overview with ROP and EOQ | FR-14, FR-20, FR-21 |
| `stock.php` | Stock card, movement ledger, 14-day trend | FR-15 |
| `reconciliation.php` | Physical vs recorded, live variance against tolerance | FR-16, FR-18 |
| `materials.php` | Material master and tolerance limits | FR-17, FR-19 |
| `density.php` | Density register and new laboratory test | FR-22 |
| `reports.php` | Report catalogue and receiving report preview | FR-24 |

Excluded on purpose, as agreed: **SAP Integration** and **Audit Trail**.

---

## The calculation the whole system is built around

```
m = V × ρ                     calculated mass from measured volume
variance = (m − ticket) / ticket × 100 %
pass     = |variance| ≤ tolerance of the material class
```

Tolerance limits follow SOP IN.MT.02 / IN.MT.03:
Cement, Fly Ash, Silica Fume ±1% · Coarse Aggregate, Fuel ±2% · Admixture ±3% · Fine Aggregate ±5%

Change the volume, the density or the stated tonnage on `entry.php` and the calculator, the variance
and the validation gate all update live — including the switch between the navy "ready to post"
state and the red "CAR required" state.

---

## Colours

Taken from the DynaStock logo only, with no extra accent colours.

| Token | Hex | Use |
|---|---|---|
| navy | `#032A48` | sidebar, primary buttons, positive status |
| red | `#CF000F` | out of tolerance, below ROP, blocked actions |
| grey | `#565656` | body text and neutral states |

---

## Structure

```
dynastock/
  index.php login.php register.php logout.php
  dashboard.php receiving.php gate.php entry.php car.php
  inventory.php stock.php reconciliation.php approvals.php
  materials.php density.php reports.php
  build.php                 → exports a flat HTML copy of any page
  includes/  config.php functions.php header.php footer.php
  assets/    css/style.css  js/app.js  js/intro.js  img/logo.svg logo-white.svg
```

Demo data only. No production ERP data is used and no SAP connection is claimed.
