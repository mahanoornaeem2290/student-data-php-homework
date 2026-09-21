<?php
require_once 'db_connect.php';

$success = '';
$error   = '';

// ── Handle Form Submission ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'insert') {

    $first_name     = trim($conn->real_escape_string($_POST['first_name']));
    $last_name      = trim($conn->real_escape_string($_POST['last_name']));
    $roll_number    = trim($conn->real_escape_string($_POST['roll_number']));
    $class          = trim($conn->real_escape_string($_POST['class']));
    $section        = trim($conn->real_escape_string($_POST['section']));
    $gender         = $conn->real_escape_string($_POST['gender']);
    $dob            = $conn->real_escape_string($_POST['dob']);
    $email          = trim($conn->real_escape_string($_POST['email']));
    $phone          = trim($conn->real_escape_string($_POST['phone']));
    $address        = trim($conn->real_escape_string($_POST['address']));
    $father_name    = trim($conn->real_escape_string($_POST['father_name']));
    $mother_name    = trim($conn->real_escape_string($_POST['mother_name']));
    $guardian_phone = trim($conn->real_escape_string($_POST['guardian_phone']));
    $blood_group    = $conn->real_escape_string($_POST['blood_group']);
    $nationality    = trim($conn->real_escape_string($_POST['nationality']));
    $religion       = trim($conn->real_escape_string($_POST['religion']));
    $admission_date = $conn->real_escape_string($_POST['admission_date']);
    $fee_status     = $conn->real_escape_string($_POST['fee_status']);

    $sql = "INSERT INTO students 
            (first_name, last_name, roll_number, class, section, gender, dob,
             email, phone, address, father_name, mother_name, guardian_phone,
             blood_group, nationality, religion, admission_date, fee_status)
            VALUES
            ('$first_name','$last_name','$roll_number','$class','$section',
             '$gender','$dob','$email','$phone','$address','$father_name',
             '$mother_name','$guardian_phone','$blood_group','$nationality',
             '$religion','$admission_date','$fee_status')";

    if ($conn->query($sql)) {
        $success = "✅ Student <strong>$first_name $last_name</strong> registered successfully! (Roll No: $roll_number)";
    } else {
        if ($conn->errno === 1062) {
            $error = "❌ Roll Number <strong>$roll_number</strong> already exists. Please use a unique roll number.";
        } else {
            $error = "❌ Error: " . $conn->error;
        }
    }
}

// ── Handle Delete ─────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM students WHERE id = $del_id");
    header("Location: index.php?deleted=1");
    exit;
}

if (isset($_GET['deleted'])) {
    $success = "🗑️ Student record deleted successfully.";
}

// ── Fetch All Students ────────────────────────────────────────────────────
$view_result = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
$total       = $view_result ? $view_result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ── CSS Reset & Variables ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:         #09090b; /* Zinc 950 */
            --surface:    #18181b; /* Zinc 900 */
            --surface2:   #27272a; /* Zinc 800 */
            --border:     #3f3f46; /* Zinc 700 */
            --accent:     #0ea5e9; /* Sky 500 */
            --accent2:    #38bdf8; /* Sky 400 */
            --accent3:    #10b981; /* Emerald 500 */
            --text:       #f4f4f5; /* Zinc 100 */
            --text-muted: #a1a1aa; /* Zinc 400 */
            --input-bg:   #09090b;
            --success:    #10b981;
            --error:      #ef4444;
            --warning:    #f59e0b;
            --radius:     16px;
            --shadow:     0 20px 60px rgba(0,0,0,.6);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            background-image: 
                radial-gradient(ellipse at 15% 10%, rgba(14,165,233,.12) 0%, transparent 50%),
                radial-gradient(ellipse at 85% 85%, rgba(56,189,248,.08) 0%, transparent 50%);
        }

        /* ── Top Header ── */
        .site-header {
            background: rgba(24, 24, 27, 0.7);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding: 0 40px;
            display: flex; align-items: center; justify-content: space-between;
            height: 70px;
            position: sticky; top: 0; z-index: 100;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .brand { display: flex; align-items: center; gap: 14px; }
        .brand-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; box-shadow: 0 4px 20px rgba(14,165,233,.35);
        }
        .brand-name { font-size: 20px; font-weight: 700; }
        .brand-name span { color: var(--accent); }
        .header-stats {
            display: flex; gap: 24px;
        }
        .stat-pill {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 30px; padding: 6px 16px;
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 8px;
        }
        .stat-pill i { color: var(--accent); }

        /* ── Nav Tabs ── */
        .tabs {
            display: flex; gap: 0;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 40px;
        }
        .tab-btn {
            padding: 16px 28px; border: none; background: transparent;
            color: var(--text-muted); cursor: pointer; font-family: inherit;
            font-size: 14px; font-weight: 500;
            border-bottom: 3px solid transparent;
            transition: all .25s; display: flex; align-items: center; gap: 8px;
        }
        .tab-btn:hover { color: var(--text); }
        .tab-btn.active {
            color: var(--accent); border-bottom-color: var(--accent);
            background: rgba(14,165,233,.08);
        }

        /* ── Main Layout ── */
        .main { max-width: 1400px; margin: 0 auto; padding: 40px; }

        /* ── Alerts ── */
        .alert {
            padding: 16px 20px; border-radius: var(--radius);
            margin-bottom: 28px; font-size: 14px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            animation: slideIn .4s ease;
        }
        @keyframes slideIn {
            from { opacity:0; transform: translateY(-10px); }
            to   { opacity:1; transform: translateY(0); }
        }
        .alert-success {
            background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.3);
            color: #6ee7b7;
        }
        .alert-error {
            background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.3);
            color: #fca5a5;
        }

        /* ── Card ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .card-header {
            padding: 24px 32px;
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 14px;
        }
        .card-header-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--accent), #7dd3fc);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; box-shadow: 0 4px 15px rgba(14,165,233,.35);
        }
        .card-title { font-size: 18px; font-weight: 700; }
        .card-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 2px; }
        .card-body { padding: 32px; }

        /* ── Form Grid ── */
        .section-label {
            font-size: 11px; font-weight: 700; letter-spacing: 1.2px;
            text-transform: uppercase; color: var(--accent);
            margin: 28px 0 16px; padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 8px;
        }
        .section-label:first-child { margin-top: 0; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
        .form-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
        .form-grid.cols-1 { grid-template-columns: 1fr; }
        .span-2 { grid-column: span 2; }
        .span-3 { grid-column: span 3; }

        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label {
            font-size: 12px; font-weight: 600; color: var(--text-muted);
            display: flex; align-items: center; gap: 6px;
        }
        .form-label .req { color: var(--accent2); }

        .form-control {
            background: var(--input-bg); border: 1.5px solid var(--border);
            color: var(--text); padding: 11px 14px; border-radius: 10px;
            font-family: inherit; font-size: 14px; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(14,165,233,.25);
        }
        .form-control::placeholder { color: #52525b; }
        select.form-control { cursor: pointer; }
        select.form-control option { background: var(--surface); }
        textarea.form-control { resize: vertical; min-height: 90px; }

        /* ── Submit Btn ── */
        .btn {
            padding: 13px 28px; border: none; border-radius: 10px;
            font-family: inherit; font-size: 14px; font-weight: 600;
            cursor: pointer; transition: all .25s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #7dd3fc);
            color: #0f172a; box-shadow: 0 4px 20px rgba(14,165,233,.4);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(14,165,233,.5);
        }
        .btn-secondary {
            background: var(--surface2); color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { background: var(--border); }
        .btn-danger {
            background: rgba(239,68,68,.15); color: #fca5a5;
            border: 1px solid rgba(239,68,68,.3);
            padding: 7px 14px; font-size: 12px;
        }
        .btn-danger:hover {
            background: rgba(239,68,68,.3);
            transform: scale(1.03);
        }
        .form-footer {
            margin-top: 32px; display: flex; gap: 14px;
            align-items: center; justify-content: flex-end;
        }

        /* ── Stats Cards ── */
        .stats-row {
            display: grid; grid-template-columns: repeat(4,1fr); gap: 18px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 20px 24px;
            display: flex; align-items: center; gap: 16px;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow); border-color: rgba(14,165,233,.4); }
        .stat-icon {
            width: 50px; height: 50px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }
        .stat-icon.purple { background: rgba(14,165,233,.15); color: var(--accent); } /* Repurposed as blue */
        .stat-icon.pink   { background: rgba(239,68,68,.15); color: var(--error); }
        .stat-icon.green  { background: rgba(16,185,129,.15); color: var(--success); }
        .stat-icon.yellow { background: rgba(245,158,11,.15); color: var(--warning); }
        .stat-val  { font-size: 26px; font-weight: 800; }
        .stat-lbl  { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        /* ── Data Table ── */
        .table-toolbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 24px; border-bottom: 1px solid var(--border);
        }
        .search-box {
            position: relative; width: 300px;
        }
        .search-box i {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--text-muted); font-size: 14px;
        }
        .search-box input {
            width: 100%; background: var(--input-bg); border: 1.5px solid var(--border);
            color: var(--text); padding: 10px 14px 10px 40px; border-radius: 10px;
            font-family: inherit; font-size: 13px; outline: none;
            transition: border-color .2s;
        }
        .search-box input:focus { border-color: var(--accent); }

        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead tr {
            background: var(--surface2);
            border-bottom: 2px solid var(--border);
        }
        th {
            padding: 14px 16px; text-align: left;
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .8px; color: var(--text-muted);
            white-space: nowrap;
        }
        tbody tr {
            border-bottom: 1px solid rgba(63,63,70,.5);
            transition: background .15s;
        }
        tbody tr:hover { background: rgba(14,165,233,.08); }
        tbody tr:last-child { border-bottom: none; }
        td { padding: 13px 16px; vertical-align: middle; white-space: nowrap; }

        .avatar {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px; flex-shrink: 0;
        }
        .name-cell { display: flex; align-items: center; gap: 12px; }

        .badge {
            display: inline-flex; align-items: center; padding: 4px 10px;
            border-radius: 20px; font-size: 11px; font-weight: 600;
        }
        .badge-paid    { background: rgba(16,185,129,.15); color: #6ee7b7; }
        .badge-unpaid  { background: rgba(239,68,68,.15);  color: #fca5a5; }
        .badge-partial { background: rgba(245,158,11,.15); color: #fcd34d; }
        .badge-male    { background: rgba(14,165,233,.15); color: #7dd3fc; }
        .badge-female  { background: rgba(236,72,153,.15); color: #f472b6; }
        .badge-other   { background: rgba(20,184,166,.15); color: #5eead4; }

        .no-data {
            text-align: center; padding: 60px 20px; color: var(--text-muted);
        }
        .no-data i { font-size: 48px; margin-bottom: 14px; opacity: .4; }
        .no-data p { font-size: 15px; }

        /* ── Tab Panels ── */
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* ── Responsive ── */
        @media(max-width: 900px) {
            .main { padding: 20px; }
            .form-grid { grid-template-columns: 1fr 1fr; }
            .stats-row { grid-template-columns: 1fr 1fr; }
            .span-3 { grid-column: span 2; }
        }
        @media(max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .span-2, .span-3 { grid-column: span 1; }
            .stats-row { grid-template-columns: 1fr; }
            .site-header { padding: 0 16px; }
            .header-stats { display: none; }
            .tabs { padding: 0 16px; overflow-x: auto; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
    </style>
</head>
<body>

<!-- ── Header ── -->
<header class="site-header">
    <div class="brand">
        <div class="brand-icon">🎓</div>
        <div>
            <div class="brand-name">Student<span>MS</span></div>
        </div>
    </div>
    <div class="header-stats">
        <div class="stat-pill">
            <i class="fas fa-users"></i>
            Total Students: <strong><?= $total ?></strong>
        </div>
        <div class="stat-pill">
            <i class="fas fa-calendar-alt"></i>
            <?= date('d M Y') ?>
        </div>
    </div>
</header>

<!-- ── Tabs ── -->
<nav class="tabs">
    <button class="tab-btn active" onclick="switchTab('form', this)">
        <i class="fas fa-user-plus"></i> Register Student
    </button>
    <button class="tab-btn" onclick="switchTab('view', this)">
        <i class="fas fa-table"></i> View Records
        <span style="background:var(--accent);color:#fff;border-radius:20px;padding:1px 8px;font-size:11px;margin-left:4px;"><?= $total ?></span>
    </button>
</nav>

<div class="main">

    <!-- ── Alerts ── -->
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════════════ -->
    <!-- TAB 1 — Registration Form                      -->
    <!-- ══════════════════════════════════════════════ -->
    <div class="tab-panel <?= (!$error && !$success || $success) ? 'active' : '' ?>" id="panel-form">
        <div class="card">
            <div class="card-header">
                <div class="card-header-icon"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <div class="card-title">Student Registration Form</div>
                    <div class="card-subtitle">Fill in all required fields to register a new student</div>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php" id="studentForm" novalidate>
                    <input type="hidden" name="action" value="insert">

                    <!-- Section 1: Personal Info -->
                    <div class="section-label"><i class="fas fa-id-card"></i> Personal Information</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-user"></i> First Name <span class="req">*</span></label>
                            <input type="text" name="first_name" class="form-control" placeholder="e.g. Ahmed" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-user"></i> Last Name <span class="req">*</span></label>
                            <input type="text" name="last_name" class="form-control" placeholder="e.g. Khan" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-hashtag"></i> Roll Number <span class="req">*</span></label>
                            <input type="text" name="roll_number" class="form-control" placeholder="e.g. 2024-CS-001" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-venus-mars"></i> Gender <span class="req">*</span></label>
                            <select name="gender" class="form-control" required>
                                <option value="">-- Select Gender --</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-birthday-cake"></i> Date of Birth <span class="req">*</span></label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-tint"></i> Blood Group <span class="req">*</span></label>
                            <select name="blood_group" class="form-control" required>
                                <option value="">-- Select --</option>
                                <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                                    <option value="<?= $bg ?>"><?= $bg ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-globe"></i> Nationality <span class="req">*</span></label>
                            <input type="text" name="nationality" class="form-control" value="Pakistani" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-mosque"></i> Religion <span class="req">*</span></label>
                            <input type="text" name="religion" class="form-control" placeholder="e.g. Islam" required>
                        </div>
                    </div>

                    <!-- Section 2: Academic Info -->
                    <div class="section-label"><i class="fas fa-graduation-cap"></i> Academic Information</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-chalkboard"></i> Class <span class="req">*</span></label>
                            <select name="class" class="form-control" required>
                                <option value="">-- Select Class --</option>
                                <?php foreach(['1st','2nd','3rd','4th','5th','6th','7th','8th','9th','10th',
                                               '11th (Pre-Med)','11th (Pre-Eng)','11th (Commerce)',
                                               '12th (Pre-Med)','12th (Pre-Eng)','12th (Commerce)',
                                               'BS-CS 1st Year','BS-CS 2nd Year','BS-CS 3rd Year','BS-CS 4th Year',
                                               'BS-IT 1st Year','BS-IT 2nd Year','BS-IT 3rd Year','BS-IT 4th Year',
                                               'Other'] as $c): ?>
                                    <option value="<?= $c ?>"><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-layer-group"></i> Section <span class="req">*</span></label>
                            <select name="section" class="form-control" required>
                                <option value="">-- Select Section --</option>
                                <?php foreach(['A','B','C','D','E','F'] as $s): ?>
                                    <option value="<?= $s ?>"><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-calendar-plus"></i> Admission Date <span class="req">*</span></label>
                            <input type="date" name="admission_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-money-bill-wave"></i> Fee Status <span class="req">*</span></label>
                            <select name="fee_status" class="form-control" required>
                                <option value="Unpaid">Unpaid</option>
                                <option value="Paid">Paid</option>
                                <option value="Partial">Partial</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 3: Contact Info -->
                    <div class="section-label"><i class="fas fa-address-book"></i> Contact Information</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-envelope"></i> Email Address <span class="req">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="student@email.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-phone"></i> Phone Number <span class="req">*</span></label>
                            <input type="tel" name="phone" class="form-control" placeholder="03XX-XXXXXXX" required>
                        </div>
                        <div class="form-group span-3">
                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Full Address <span class="req">*</span></label>
                            <textarea name="address" class="form-control" placeholder="House No, Street, City, Province" required></textarea>
                        </div>
                    </div>

                    <!-- Section 4: Guardian Info -->
                    <div class="section-label"><i class="fas fa-users"></i> Guardian / Parent Information</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-male"></i> Father's Name <span class="req">*</span></label>
                            <input type="text" name="father_name" class="form-control" placeholder="Father's full name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-female"></i> Mother's Name <span class="req">*</span></label>
                            <input type="text" name="mother_name" class="form-control" placeholder="Mother's full name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-phone-alt"></i> Guardian Phone <span class="req">*</span></label>
                            <input type="tel" name="guardian_phone" class="form-control" placeholder="03XX-XXXXXXX" required>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="reset" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset Form</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Register Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════ -->
    <!-- TAB 2 — View Records                           -->
    <!-- ══════════════════════════════════════════════ -->
    <div class="tab-panel" id="panel-view">

        <!-- Stats Row -->
        <?php
        $paid    = $conn->query("SELECT COUNT(*) c FROM students WHERE fee_status='Paid'")->fetch_assoc()['c'];
        $unpaid  = $conn->query("SELECT COUNT(*) c FROM students WHERE fee_status='Unpaid'")->fetch_assoc()['c'];
        $males   = $conn->query("SELECT COUNT(*) c FROM students WHERE gender='Male'")->fetch_assoc()['c'];
        ?>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                <div><div class="stat-val"><?= $total ?></div><div class="stat-lbl">Total Students</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div><div class="stat-val"><?= $paid ?></div><div class="stat-lbl">Fee Paid</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pink"><i class="fas fa-exclamation-circle"></i></div>
                <div><div class="stat-val"><?= $unpaid ?></div><div class="stat-lbl">Fee Unpaid</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-male"></i></div>
                <div><div class="stat-val"><?= $males ?></div><div class="stat-lbl">Male Students</div></div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="card">
            <div class="table-toolbar">
                <div>
                    <div style="font-size:16px;font-weight:700;">Student Records</div>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px;"><?= $total ?> student(s) found</div>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search students..." onkeyup="filterTable()">
                </div>
            </div>

            <div class="table-wrapper">
                <?php if ($total > 0): ?>
                <table id="studentTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Roll No</th>
                            <th>Class</th>
                            <th>Sec</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Father</th>
                            <th>Blood</th>
                            <th>Admission</th>
                            <th>Fee</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $view_result->data_seek(0);
                    $i = 1;
                    $colors = ['#0ea5e9','#38bdf8','#10b981','#f59e0b','#06b6d4','#f43f5e'];
                    while ($row = $view_result->fetch_assoc()):
                        $initials = strtoupper(substr($row['first_name'],0,1) . substr($row['last_name'],0,1));
                        $color = $colors[($i-1) % count($colors)];
                    ?>
                    <tr>
                        <td style="color:var(--text-muted);"><?= $i++ ?></td>
                        <td>
                            <div class="name-cell">
                                <div class="avatar" style="background:<?= $color ?>22;color:<?= $color ?>;">
                                    <?= $initials ?>
                                </div>
                                <div>
                                    <div style="font-weight:600;"><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></div>
                                    <div style="font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($row['nationality']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><code style="background:var(--surface2);padding:3px 7px;border-radius:5px;font-size:12px;"><?= htmlspecialchars($row['roll_number']) ?></code></td>
                        <td><?= htmlspecialchars($row['class']) ?></td>
                        <td><span style="font-weight:700;color:var(--accent);"><?= htmlspecialchars($row['section']) ?></span></td>
                        <td>
                            <span class="badge badge-<?= strtolower($row['gender']) ?>">
                                <?= $row['gender'] ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($row['dob'])) ?></td>
                        <td style="color:var(--text-muted);font-size:12px;"><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['father_name']) ?></td>
                        <td>
                            <span class="badge" style="background:rgba(6,182,212,.15);color:#67e8f9;">
                                <?= $row['blood_group'] ?>
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= date('d M Y', strtotime($row['admission_date'])) ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($row['fee_status']) ?>">
                                <?= $row['fee_status'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?delete=<?= $row['id'] ?>" 
                               class="btn btn-danger"
                               onclick="return confirm('Delete student <?= addslashes($row['first_name'].' '.$row['last_name']) ?>?')">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-user-slash"></i>
                    <p>No students registered yet.<br>
                    <a href="#" onclick="switchTab('form', document.querySelector('.tab-btn'))" style="color:var(--accent);">Register your first student →</a></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div><!-- /panel-view -->

</div><!-- /main -->

<script>
// ── Tab switching ──────────────────────────────────────
function switchTab(id, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + id).classList.add('active');
    btn.classList.add('active');
}

// ── Table search ──────────────────────────────────────
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#studentTable tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

// ── Auto-switch to view tab after success ─────────────
<?php if($success && strpos($success,'registered') !== false): ?>
setTimeout(() => {
    const btns = document.querySelectorAll('.tab-btn');
    switchTab('view', btns[1]);
}, 1800);
<?php endif; ?>

// ── Client-side form validation ───────────────────────
document.getElementById('studentForm').addEventListener('submit', function(e) {
    const required = this.querySelectorAll('[required]');
    let valid = true;
    required.forEach(field => {
        field.style.borderColor = '';
        if (!field.value.trim()) {
            field.style.borderColor = '#ef4444';
            field.style.boxShadow   = '0 0 0 3px rgba(239,68,68,.2)';
            valid = false;
            field.focus();
        }
    });
    if (!valid) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>
</body>
</html>
