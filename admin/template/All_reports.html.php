<?php
date_default_timezone_set("Asia/Karachi");

$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'opd';
$date_from   = isset($_GET['date_from'])   ? $_GET['date_from']   : date('Y-m-01');
$date_to     = isset($_GET['date_to'])     ? $_GET['date_to']     : date('Y-m-d');
$doc_id      = isset($_GET['doc_id'])      ? $_GET['doc_id']      : '';
$search_btn  = isset($_GET['search_btn']);

// Fetch doctors for filter
$doctors_q = mysqli_query($con, "SELECT D_ID, Name FROM ssh_dr_reg ORDER BY Name");
?>

<div class="container-fluid">
<div class="card">
<div class="card-body">

<!-- Filter Form -->
<form method="get" action="" class="mb-3">
    <input type="hidden" name="page" value="all_reports">
    <div class="row align-items-end">
        <div class="col-md-2">
            <label><b>Report Type</b></label>
            <select name="report_type" class="form-control" onchange="this.form.submit()">
                <option value="opd"       <?= $report_type=='opd'       ? 'selected':'' ?>>OPD (Outdoor)</option>
                <option value="ipd"       <?= $report_type=='ipd'       ? 'selected':'' ?>>IPD (Indoor)</option>
                <option value="dialysis"  <?= $report_type=='dialysis'  ? 'selected':'' ?>>Dialysis</option>
                <option value="services"  <?= $report_type=='services'  ? 'selected':'' ?>>Services</option>
                <option value="expense"   <?= $report_type=='expense'   ? 'selected':'' ?>>Expense</option>
                <option value="income"    <?= $report_type=='income'    ? 'selected':'' ?>>General Income</option>
                <option value="summary"   <?= $report_type=='summary'   ? 'selected':'' ?>>Daily Summary</option>
            </select>
        </div>
        <div class="col-md-2">
            <label><b>Date From</b></label>
            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from) ?>">
        </div>
        <div class="col-md-2">
            <label><b>Date To</b></label>
            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to) ?>">
        </div>
        <?php if (in_array($report_type, ['opd','ipd','dialysis'])): ?>
        <div class="col-md-2">
            <label><b>Doctor</b></label>
            <select name="doc_id" class="form-control">
                <option value="">-- All Doctors --</option>
                <?php mysqli_data_seek($doctors_q, 0); foreach($doctors_q as $d): ?>
                <option value="<?= $d['D_ID'] ?>" <?= $doc_id==$d['D_ID']?'selected':'' ?>><?= htmlspecialchars($d['Name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="col-md-2">
            <button type="submit" name="search_btn" class="btn btn-danger mt-4">
                <i class="fa fa-search"></i> Search
            </button>
            <a href="?page=all_reports" class="btn btn-secondary mt-4">Reset</a>
        </div>
        <div class="col-md-2 mt-4">
            <button type="button" onclick="window.print()" class="btn btn-success">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
    </div>
</form>

<div id="report-area">
<?php if ($search_btn): ?>

<?php
// ============ OPD REPORT ============
if ($report_type == 'opd'):
    $where_doc = $doc_id ? "AND ssh_p_dpr.D_ID = '$doc_id'" : "";
    $q = "SELECT DATE(ssh_p_dpr.A_DATE) AS rdate,
            ssh_dr_reg.Name AS doctor,
            COUNT(ssh_p_dpr.MRN) AS patients,
            SUM(ssh_p_dpr.Paid) AS total_paid,
            SUM(ssh_p_dpr.D_Pay) AS doc_share,
            SUM(ssh_p_dpr.Charges - ssh_p_dpr.Paid) AS discount
          FROM ssh_p_dpr
          JOIN ssh_dr_reg ON ssh_p_dpr.D_ID = ssh_dr_reg.D_ID
          WHERE DATE(ssh_p_dpr.A_DATE) BETWEEN '$date_from' AND '$date_to'
          $where_doc
          GROUP BY DATE(ssh_p_dpr.A_DATE), ssh_p_dpr.D_ID
          ORDER BY rdate DESC";
    $res = mysqli_query($con, $q);
    $total_p=0; $total_paid=0; $total_doc=0; $total_disc=0;
?>
<h5 class="mb-2"><b>OPD Report</b> — <?= $date_from ?> to <?= $date_to ?></h5>
<table class="table table-bordered table-striped table-sm">
    <thead class="thead-dark">
        <tr><th>Date</th><th>Doctor</th><th>Patients</th><th>Total Paid</th><th>Doctor Share</th><th>Hospital Share</th><th>Discount</th></tr>
    </thead>
    <tbody>
    <?php foreach($res as $r):
        $hosp = $r['total_paid'] - $r['doc_share'];
        $total_p+=$r['patients']; $total_paid+=$r['total_paid']; $total_doc+=$r['doc_share']; $total_disc+=$r['discount'];
    ?>
        <tr>
            <td><?= $r['rdate'] ?></td>
            <td><?= htmlspecialchars($r['doctor']) ?></td>
            <td><?= $r['patients'] ?></td>
            <td><?= number_format($r['total_paid'],2) ?></td>
            <td><?= number_format($r['doc_share'],2) ?></td>
            <td><?= number_format($hosp,2) ?></td>
            <td><?= number_format($r['discount'],2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot style="background:lightgrey;font-weight:bold;">
        <tr><td>Total</td><td></td><td><?= $total_p ?></td><td><?= number_format($total_paid,2) ?></td><td><?= number_format($total_doc,2) ?></td><td><?= number_format($total_paid-$total_doc,2) ?></td><td><?= number_format($total_disc,2) ?></td></tr>
    </tfoot>
</table>

<?php
// ============ IPD REPORT ============
elseif ($report_type == 'ipd'):
    $where_doc = $doc_id ? "AND ssh_p_indoor_doctors.D_ID = '$doc_id'" : "";
    $q = "SELECT DATE(ssh_p_indoor.admit_date) AS rdate,
            ssh_p_reg.Name AS patient,
            ssh_cases_indoor.Title AS case_type,
            indoor_room.room_no,
            ssh_p_indoor.admit_date,
            ssh_p_indoor.exit_date,
            ssh_p_indoor.Paid,
            ssh_p_indoor.admition_type
          FROM ssh_p_indoor
          JOIN ssh_p_reg ON ssh_p_indoor.P_ID = ssh_p_reg.P_ID
          LEFT JOIN ssh_cases_indoor ON ssh_p_indoor.S_ID = ssh_cases_indoor.S_ID
          LEFT JOIN indoor_room ON ssh_p_indoor.room_id = indoor_room.ir_id
          WHERE DATE(ssh_p_indoor.admit_date) BETWEEN '$date_from' AND '$date_to'
          ORDER BY ssh_p_indoor.admit_date DESC";
    $res = mysqli_query($con, $q);
    $total_paid=0; $count=0;
?>
<h5 class="mb-2"><b>IPD Report</b> — <?= $date_from ?> to <?= $date_to ?></h5>
<table class="table table-bordered table-striped table-sm">
    <thead class="thead-dark">
        <tr><th>#</th><th>Admit Date</th><th>Patient</th><th>Case</th><th>Room</th><th>Exit Date</th><th>Paid</th><th>Type</th></tr>
    </thead>
    <tbody>
    <?php $sr=1; foreach($res as $r): $total_paid+=$r['Paid']; $count++; ?>
        <tr>
            <td><?= $sr++ ?></td>
            <td><?= $r['admit_date'] ?></td>
            <td><?= htmlspecialchars($r['patient']) ?></td>
            <td><?= htmlspecialchars($r['case_type']) ?></td>
            <td><?= $r['room_no'] ?></td>
            <td><?= $r['exit_date']=='0000-00-00'?'<span class="badge badge-warning">Admitted</span>':$r['exit_date'] ?></td>
            <td><?= number_format($r['Paid'],2) ?></td>
            <td><?= $r['admition_type']=='0'?'Private':'Health Card' ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot style="background:lightgrey;font-weight:bold;">
        <tr><td colspan="6">Total Patients: <?= $count ?></td><td><?= number_format($total_paid,2) ?></td><td></td></tr>
    </tfoot>
</table>

<?php
// ============ DIALYSIS REPORT ============
elseif ($report_type == 'dialysis'):
    $q = "SELECT ssh_p_dialysis.date, ssh_p_reg.Name AS patient,
            ssh_p_reg.phone, ssh_p_dialysis.Paid, ssh_p_dialysis.pd_id
          FROM ssh_p_dialysis
          JOIN ssh_p_reg ON ssh_p_dialysis.P_ID = ssh_p_reg.P_ID
          WHERE DATE(ssh_p_dialysis.date) BETWEEN '$date_from' AND '$date_to'
          ORDER BY ssh_p_dialysis.date DESC";
    $res = mysqli_query($con, $q);
    $total_paid=0; $count=0;
?>
<h5 class="mb-2"><b>Dialysis Report</b> — <?= $date_from ?> to <?= $date_to ?></h5>
<table class="table table-bordered table-striped table-sm">
    <thead class="thead-dark">
        <tr><th>#</th><th>Date</th><th>Patient</th><th>Phone</th><th>Paid</th></tr>
    </thead>
    <tbody>
    <?php $sr=1; foreach($res as $r): $total_paid+=$r['Paid']; $count++; ?>
        <tr>
            <td><?= $sr++ ?></td>
            <td><?= $r['date'] ?></td>
            <td><?= htmlspecialchars($r['patient']) ?></td>
            <td><?= $r['phone'] ?></td>
            <td><?= number_format($r['Paid'],2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot style="background:lightgrey;font-weight:bold;">
        <tr><td colspan="4">Total Sessions: <?= $count ?></td><td><?= number_format($total_paid,2) ?></td></tr>
    </tfoot>
</table>

<?php
// ============ SERVICES REPORT ============
elseif ($report_type == 'services'):
    $q = "SELECT ssh_p_services.Date, ssh_p_reg.Name AS patient,
            ssh_ser_cat.Name AS service, ssh_p_services.Paid
          FROM ssh_p_services
          JOIN ssh_p_reg ON ssh_p_services.P_ID = ssh_p_reg.P_ID
          JOIN ssh_ser_cat ON ssh_p_services.C_ID = ssh_ser_cat.C_ID
          WHERE DATE(ssh_p_services.Date) BETWEEN '$date_from' AND '$date_to'
          ORDER BY ssh_p_services.Date DESC";
    $res = mysqli_query($con, $q);
    $total_paid=0; $count=0;
?>
<h5 class="mb-2"><b>Services Report</b> — <?= $date_from ?> to <?= $date_to ?></h5>
<table class="table table-bordered table-striped table-sm">
    <thead class="thead-dark">
        <tr><th>#</th><th>Date</th><th>Patient</th><th>Service</th><th>Paid</th></tr>
    </thead>
    <tbody>
    <?php $sr=1; foreach($res as $r): $total_paid+=$r['Paid']; $count++; ?>
        <tr>
            <td><?= $sr++ ?></td>
            <td><?= $r['Date'] ?></td>
            <td><?= htmlspecialchars($r['patient']) ?></td>
            <td><?= htmlspecialchars($r['service']) ?></td>
            <td><?= number_format($r['Paid'],2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot style="background:lightgrey;font-weight:bold;">
        <tr><td colspan="4">Total: <?= $count ?></td><td><?= number_format($total_paid,2) ?></td></tr>
    </tfoot>
</table>

<?php
// ============ EXPENSE REPORT ============
elseif ($report_type == 'expense'):
    $q = "SELECT Date, Title, Amount, Description FROM ssh_expenses
          WHERE DATE(Date) BETWEEN '$date_from' AND '$date_to'
          ORDER BY Date DESC";
    $res = mysqli_query($con, $q);
    $total=0; $count=0;
?>
<h5 class="mb-2"><b>Expense Report</b> — <?= $date_from ?> to <?= $date_to ?></h5>
<table class="table table-bordered table-striped table-sm">
    <thead class="thead-dark">
        <tr><th>#</th><th>Date</th><th>Title</th><th>Description</th><th>Amount</th></tr>
    </thead>
    <tbody>
    <?php $sr=1; foreach($res as $r): $total+=$r['Amount']; $count++; ?>
        <tr>
            <td><?= $sr++ ?></td>
            <td><?= $r['Date'] ?></td>
            <td><?= htmlspecialchars($r['Title']) ?></td>
            <td><?= htmlspecialchars($r['Description']) ?></td>
            <td><?= number_format($r['Amount'],2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot style="background:lightgrey;font-weight:bold;">
        <tr><td colspan="4">Total: <?= $count ?></td><td><?= number_format($total,2) ?></td></tr>
    </tfoot>
</table>

<?php
// ============ GENERAL INCOME REPORT ============
elseif ($report_type == 'income'):
    $q = "SELECT * FROM ssh_general_income
          WHERE DATE(Date) BETWEEN '$date_from' AND '$date_to'
          ORDER BY Date DESC";
    $res = mysqli_query($con, $q);
    $total=0; $count=0;
?>
<h5 class="mb-2"><b>General Income Report</b> — <?= $date_from ?> to <?= $date_to ?></h5>
<table class="table table-bordered table-striped table-sm">
    <thead class="thead-dark">
        <tr><th>#</th><th>Date</th><th>Title</th><th>Amount</th><th>Description</th></tr>
    </thead>
    <tbody>
    <?php $sr=1; foreach($res as $r): $total+=$r['Amount']; $count++; ?>
        <tr>
            <td><?= $sr++ ?></td>
            <td><?= $r['Date'] ?></td>
            <td><?= htmlspecialchars($r['Title']) ?></td>
            <td><?= number_format($r['Amount'],2) ?></td>
            <td><?= htmlspecialchars($r['Description'] ?? '') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot style="background:lightgrey;font-weight:bold;">
        <tr><td colspan="3">Total: <?= $count ?></td><td><?= number_format($total,2) ?></td><td></td></tr>
    </tfoot>
</table>

<?php
// ============ DAILY SUMMARY REPORT ============
elseif ($report_type == 'summary'):
    // OPD
    $opd_q = mysqli_query($con, "SELECT COUNT(*) AS c, SUM(Paid) AS s FROM ssh_p_dpr WHERE DATE(A_DATE) BETWEEN '$date_from' AND '$date_to'");
    $opd = mysqli_fetch_assoc($opd_q);
    // IPD
    $ipd_q = mysqli_query($con, "SELECT COUNT(*) AS c, SUM(Paid) AS s FROM ssh_p_indoor WHERE DATE(admit_date) BETWEEN '$date_from' AND '$date_to'");
    $ipd = mysqli_fetch_assoc($ipd_q);
    // Dialysis
    $dia_q = mysqli_query($con, "SELECT COUNT(*) AS c, SUM(Paid) AS s FROM ssh_p_dialysis WHERE DATE(date) BETWEEN '$date_from' AND '$date_to'");
    $dia = mysqli_fetch_assoc($dia_q);
    // Services
    $ser_q = mysqli_query($con, "SELECT COUNT(*) AS c, SUM(Paid) AS s FROM ssh_p_services WHERE DATE(Date) BETWEEN '$date_from' AND '$date_to'");
    $ser = mysqli_fetch_assoc($ser_q);
    // Expense
    $exp_q = mysqli_query($con, "SELECT COUNT(*) AS c, SUM(Amount) AS s FROM ssh_expenses WHERE DATE(Date) BETWEEN '$date_from' AND '$date_to'");
    $exp = mysqli_fetch_assoc($exp_q);
    // General Income
    $inc_q = mysqli_query($con, "SELECT COUNT(*) AS c, SUM(Amount) AS s FROM ssh_general_income WHERE DATE(Date) BETWEEN '$date_from' AND '$date_to'");
    $inc = mysqli_fetch_assoc($inc_q);

    $total_income = ($opd['s']??0) + ($ipd['s']??0) + ($dia['s']??0) + ($ser['s']??0) + ($inc['s']??0);
    $total_expense = $exp['s']??0;
    $net = $total_income - $total_expense;
?>
<h5 class="mb-3"><b>Summary Report</b> — <?= $date_from ?> to <?= $date_to ?></h5>
<table class="table table-bordered table-sm" style="max-width:600px;">
    <thead class="thead-dark"><tr><th>Category</th><th>Count</th><th>Amount</th></tr></thead>
    <tbody>
        <tr><td>OPD (Outdoor)</td><td><?= $opd['c'] ?></td><td><?= number_format($opd['s']??0,2) ?></td></tr>
        <tr><td>IPD (Indoor)</td><td><?= $ipd['c'] ?></td><td><?= number_format($ipd['s']??0,2) ?></td></tr>
        <tr><td>Dialysis</td><td><?= $dia['c'] ?></td><td><?= number_format($dia['s']??0,2) ?></td></tr>
        <tr><td>Services</td><td><?= $ser['c'] ?></td><td><?= number_format($ser['s']??0,2) ?></td></tr>
        <tr><td>General Income</td><td><?= $inc['c'] ?></td><td><?= number_format($inc['s']??0,2) ?></td></tr>
        <tr class="table-success"><td><b>Total Income</b></td><td></td><td><b><?= number_format($total_income,2) ?></b></td></tr>
        <tr class="table-danger"><td><b>Total Expense</b></td><td><?= $exp['c'] ?></td><td><b><?= number_format($total_expense,2) ?></b></td></tr>
        <tr class="table-warning"><td><b>Net</b></td><td></td><td><b><?= number_format($net,2) ?></b></td></tr>
    </tbody>
</table>
<?php endif; ?>

<?php else: ?>
<div class="alert alert-info">Select filters and click <b>Search</b> to view report.</div>
<?php endif; ?>
</div><!-- report-area -->

</div>
</div>
</div>

<style>
@media print {
    .left-side-menu, .navbar-custom, .card-widgets, form, .btn { display: none !important; }
    #report-area { display: block !important; }
}
</style>
