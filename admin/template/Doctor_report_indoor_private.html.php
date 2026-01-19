<button id="updateinfobutton" hidden class="btn" data-toggle="modal" data-target="#updateinfo" style="background: #21325E; color: white;">
    <i class="mdi mdi-plus-circle mr-2"></i> View Records
</button>

<div class="modal fade" id="updateinfo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myCenterModalLabel">View Records</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body p-4 modalbody1"></div>
        </div>
    </div>
</div>

<div class="col-xl-12 col-lg-12">
    <div class="card">
        <div class="card-body" dir="ltr">
            <div class="card-widgets">
                <a href="javascript:void(0);" onclick="reloadtablecontent()" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>
                <a data-toggle="collapse" href="#cardCollpase4" role="button" aria-expanded="false" aria-controls="cardCollpase4"><i class="mdi mdi-minus"></i></a>
                <a href="javascript:void(0);" data-toggle="remove"><i class="mdi mdi-close"></i></a>
            </div>
            
            <div>
                <form method="get" id="filterForm">
                    <input type="hidden" id="selected_filter_id" value="<?= $_GET['filter_id'] ?? '' ?>">    
                    <div class="row col-sm-12 align-items-center justify-content-center">
                        <!-- Report Type Selection -->
                        <select name="report_type" class="form-control m-1" style="width:200px;" required onchange="updateFilterOptions()">
                            <option value="">Select Report Type</option>
                            <option value="doctor" <?= ($_GET['report_type'] ?? '') == 'doctor' ? 'selected' : '' ?>>Doctor Wise</option>
                            <option value="case" <?= ($_GET['report_type'] ?? '') == 'case' ? 'selected' : '' ?>>Case Wise</option>
                        </select>

                        <!-- Dynamic Filter Option (Doctor or Case) -->
                        <div id="filterOptionContainer" class="m-1">
                            <?php
                            $selected_filter_id = $_GET['filter_id'] ?? '';
                            $report_type = $_GET['report_type'] ?? '';

                            if ($report_type == 'doctor') {
                                // Fetch doctors from database
                                $doctors = mysqli_query($con, "SELECT D_ID, Name FROM ssh_dr_reg ORDER BY Name ASC");
                                echo '<select name="filter_id" class="form-control" style="width:200px;">';
                                echo '<option value="">All Doctors</option>';
                                while ($doc = mysqli_fetch_assoc($doctors)) {
                                    $selected = ($selected_filter_id == $doc['D_ID']) ? 'selected' : '';
                                    echo '<option value="'.$doc['D_ID'].'" '.$selected.'>'.$doc['Name'].'</option>';
                                }
                                echo '</select>';

                            } elseif ($report_type == 'case') {
                                // Fetch surgical procedure cases from ssh_cases_indoor
                                $cases = mysqli_query($con, "SELECT `S_ID`, `Title` FROM `ssh_cases_indoor` WHERE close = 1 ORDER BY `Title` ASC");
                                echo '<select name="filter_id" class="form-control" style="width:200px;">';
                                echo '<option value="">All Cases</option>';
                                while ($case = mysqli_fetch_assoc($cases)) {
                                    $selected = ($selected_filter_id == $case['S_ID']) ? 'selected' : '';
                                    echo '<option value="'.$case['S_ID'].'" '.$selected.'>'.$case['Title'].'</option>';
                                }
                                echo '</select>';
                            }
                            ?>
                        </div>

                        <!-- Date Range -->
                        <input type="date" class="form-control m-1" name="date_from" style="width:150px;" required value="<?= $_GET['date_from'] ?? '' ?>">
                        <span class="m-2"><b>To</b></span>
                        <input type="date" class="form-control m-1" name="date_to" style="width:150px;" required value="<?= $_GET['date_to'] ?? '' ?>">

                        <input type="submit" class="btn btn-success m-1" name="search_date" value="Search" style="height:36px;">
                      
                    </div>
                </form>
            </div>

            <div id="cardCollpase4" class="collapse show">
                <div class="row bodyoftable" style="padding: 0px 4px !important;">
                    <div class="col-sm-12" style="padding: 0px 4px !important;">
                        <div class="card-box card-table-style" style="padding: 0px 4px !important;">

<?php
if (isset($_GET['search_date'])) {
    date_default_timezone_set("Asia/Karachi");
    $report_type = $_GET['report_type'] ?? '';
    $date_from = $_GET['date_from'] ?? '';
    $date_to = $_GET['date_to'] ?? '';
    
    // Hidden inputs for JavaScript
    echo '<input type="hidden" id="date_from" value="' . $date_from . '">';
    echo '<input type="hidden" id="date_to" value="' . $date_to . '">';

    if (empty($date_from) || empty($date_to) || empty($report_type)) {
        echo '<div class="alert alert-danger">Please select all filter options!</div>';
    } else {
        // Get the specific filter ID if provided
        $filter_id = $_GET['filter_id'] ?? '';
        
        /* =========================
           DOCTOR WISE REPORT
        ==========================*/
        if ($report_type == 'doctor') {
            // If specific doctor is selected
            if (!empty($filter_id)) {
                $where_clause = "AND d.D_ID = '$filter_id'";
                $doctor_name_query = "SELECT Name FROM ssh_dr_reg WHERE D_ID = '$filter_id'";
                $doctor_result = mysqli_query($con, $doctor_name_query);
                $doctor_row = mysqli_fetch_assoc($doctor_result);
                $doctor_name = $doctor_row['Name'] ?? '';
            } else {
                $where_clause = "";
                $doctor_name = "";
            }
            
            $fetch_data = "
                SELECT 
                    d.D_ID,
                    dr.Name,
                    COUNT(i.pi_id) AS numberofcase,
                    SUM(d.D_Fee) AS total_fee,
                    SUM(i.Paid) AS total_paid
                FROM ssh_p_indoor i
                JOIN ssh_p_indoor_doctors d ON i.pi_id = d.pi_id
                JOIN ssh_dr_reg dr ON d.D_ID = dr.D_ID
                WHERE d.to_paid = '1'
                  AND i.admition_type = '0'
                  AND DATE(i.admit_date) BETWEEN '$date_from' AND '$date_to'
                  $where_clause
                GROUP BY d.D_ID
                ORDER BY dr.Name
            ";

            $result = mysqli_query($con, $fetch_data);
            
            if(mysqli_num_rows($result) > 0) {
                $sr = 1;
                $total_cases = 0;
                $total_paid = 0;
                $total_fee = 0;
                $hospital_share = 0;
                
                $title = !empty($doctor_name) ? 
                    "Doctor-wise Report for Dr. $doctor_name<br>$date_from to $date_to" : 
                    "All Doctors Report<br>$date_from to $date_to";
?>

<table id="example"  class="table table-centered table-striped table-bordered mb-0 toggle-circle" >
    <thead>
        <tr>
            <th colspan="7" class="text-center">
                <h4><?= $title ?></h4>
            </th>
        </tr>
        <tr>
            <th>Sr#</th>
            <th>Doctor Name</th>
            <th>Total Cases</th>
            <th>Total Payment</th>
            <th>Doctor Fee</th>
            <th>Hospital Share</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
<?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $total_cases += $row['numberofcase'];
                    $total_paid += $row['total_paid'];
                    $total_fee += $row['total_fee'];
                    $current_hospital_share = $row['total_paid'] - $row['total_fee'];
                    $hospital_share += $current_hospital_share;
?>
        <tr>
            <td><?= $sr++; ?></td>
            <td><?= htmlspecialchars($row['Name']); ?></td>
            <td><?= $row['numberofcase']; ?></td>
            <td><?= number_format($row['total_paid'], 2); ?></td>
            <td><?= number_format($row['total_fee'], 2); ?></td>
            <td><?= number_format($row['total_paid'] - $row['total_fee'], 2); ?></td>
            <td>
                <button class="btn btn-success btn-sm" onclick="view_indoor_private(<?= $row['D_ID'] ?>, 0)">
                    <i class="fa fa-eye"></i> View Details
                </button>
            </td>
        </tr>
<?php } ?>
    </tbody>
    <tfoot style="background:#f2f2f2; font-weight: bold;">
        <tr>
            <td colspan="2" class="text-center"><b>Grand Total</b></td>
            <td><b><?= $total_cases ?></b></td>
            <td><b><?= number_format($total_paid, 2) ?></b></td>
            <td><b><?= number_format($total_fee, 2) ?></b></td>
            <td><b><?= number_format($hospital_share, 2) ?></b></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<?php
            } else {
                echo '<div class="alert alert-warning">No records found for the selected date range!</div>';
            }
        }
        
        /* =========================
           CASE WISE REPORT (Surgical Procedure Wise) - FIXED
        ==========================*/
        elseif ($report_type == 'case') {
            $filter_id = $_GET['filter_id'] ?? ''; // Get selected procedure
            
            if (!empty($filter_id)) {
                $procedure_query = "SELECT `Title` FROM `ssh_cases_indoor` WHERE `S_ID` = '$filter_id'";
                $procedure_result = mysqli_query($con, $procedure_query);
                $procedure_row = mysqli_fetch_assoc($procedure_result);
                $procedure_name = $procedure_row['Title'] ?? 'Unknown Procedure';
                $where_clause = "AND i.S_ID = '$filter_id'";
                $case_title = "Procedure: $procedure_name";
            } else {
                $where_clause = "";
                $case_title = "All Cases";
            }

            // Fixed query for case-wise report
            $fetch_data = "
                SELECT 
                    c.S_ID,
                    c.Title AS procedure_name,
                    COUNT(DISTINCT i.pi_id) AS total_cases,
                    SUM(i.Paid) AS total_paid,
                    SUM(d.D_Fee) AS total_doctor_fee,
                    (SUM(i.Paid) - SUM(d.D_Fee)) AS hospital_share
                FROM ssh_p_indoor i
                LEFT JOIN ssh_p_indoor_doctors d ON i.pi_id = d.pi_id
                LEFT JOIN ssh_cases_indoor c ON i.S_ID = c.S_ID
                WHERE d.to_paid = '1'
                  AND i.admition_type = '0'
                  AND DATE(i.admit_date) BETWEEN '$date_from' AND '$date_to'
                  $where_clause
                GROUP BY c.S_ID, c.Title
                ORDER BY c.Title ASC
            ";

       

            $result = mysqli_query($con, $fetch_data);
            
            if(!$result) {
                echo '<div class="alert alert-danger">Database Error: ' . mysqli_error($con) . '</div>';
            }
            elseif(mysqli_num_rows($result) > 0) {
                $sr = 1;
                $grand_total_cases = 0;
                $grand_total_paid = 0;
                $grand_total_fee = 0;
                $grand_total_share = 0;
?>

<table id="example"  class="table table-centered table-striped table-bordered mb-0 toggle-circle" >
    <thead>
        <tr>
            <th colspan="8" class="text-center">
                <h4>Procedure-wise Report - <?= $case_title ?></h4>
                <h6><?= date('d-m-Y', strtotime($date_from)) ?> to <?= date('d-m-Y', strtotime($date_to)) ?></h6>
            </th>
        </tr>
        <tr>
            <th>Sr#</th>
            <th>Procedure Name</th>
            <th>Total Cases</th>
            <th>Total Payment</th>
            <th>Total Doctor Fee </th>
            <th>Hospital Share</th>
            <th>Avg. Payment per Case</th>
            <!--<th>Actions</th>-->
        </tr>
    </thead>
    <tbody>
<?php 
                while ($row = mysqli_fetch_assoc($result)) {
                    $grand_total_cases += $row['total_cases'];
                    $grand_total_paid += $row['total_paid'];
                    $grand_total_fee += $row['total_doctor_fee'];
                    $grand_total_share += $row['hospital_share'];
                    $avg_payment = $row['total_cases'] > 0 ? round($row['total_paid'] / $row['total_cases'], 2) : 0;
?>
        <tr>
            <td><?= $sr++; ?></td>
            <td><?= htmlspecialchars($row['procedure_name'] ?? 'N/A'); ?></td>
            <td><?= $row['total_cases']; ?></td>
            <td><?= number_format($row['total_paid'], 2); ?></td>
            <td><?= number_format($row['total_doctor_fee'], 2); ?></td>
            <td><?= number_format($row['hospital_share'], 2); ?></td>
            <td><?= number_format($avg_payment, 2); ?></td>
            
        </tr>
<?php } ?>
    </tbody>
    <tfoot style="background:#f2f2f2; font-weight: bold;">
        <tr>
            <td colspan="2" class="text-center"><b>Grand Total</b></td>
            <td><b><?= $grand_total_cases ?></b></td>
            <td><b><?= number_format($grand_total_paid, 2) ?></b></td>
            <td><b><?= number_format($grand_total_fee, 2) ?></b></td>
            <td><b><?= number_format($grand_total_share, 2) ?></b></td>
            <td>
                <?php if ($grand_total_cases > 0): ?>
                <b><?= number_format($grand_total_paid / $grand_total_cases, 2) ?></b>
                <?php else: ?>
                <b>0.00</b>
                <?php endif; ?>
            </td>
           
        </tr>
    </tfoot>
</table>
 

<?php
            } else {
                echo '<div class="alert alert-warning">No procedure records found for the selected date range!</div>';
                
               
            }
        } else {
            echo '<div class="alert alert-danger">Invalid report type selected!</div>';
        }
    }
} else {
?>
    <div class="alert alert-info">Select Report Type and Date Range to view Records!</div>
<?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function updateFilterOptions() {
        var reportType = $('select[name="report_type"]').val();
        var container = $('#filterOptionContainer');
        var selectedFilter = $('#selected_filter_id').val();

        if (reportType === 'doctor') {
            $.ajax({
                type: "POST",
                url: "models/doctor_ledger.php",
                data: { get_doctors_list: 1 },
                success: function(data) {
                    var html = '<select name="filter_id" class="form-control m-1" style="width:200px;">';
                    html += '<option value="">All Doctors</option>';
                    html += data;
                    html += '</select>';
                    
                    container.html(html).show();
                    
                    if (selectedFilter) {
                        container.find('select').val(selectedFilter);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading doctors:", error);
                    container.html('<select name="filter_id" class="form-control m-1" style="width:200px;"><option value="">All Doctors</option></select>').show();
                }
            });

        } else if (reportType === 'case') {
            $.ajax({
                type: "POST",
                url: "models/doctor_ledger.php",
                data: { get_cases_list: 1 },
                success: function(data) {
                    var html = '<select name="filter_id" class="form-control m-1" style="width:200px;">';
                    html += '<option value="">All Cases</option>';
                    html += data;
                    html += '</select>';
                    
                    container.html(html).show();
                    
                    if (selectedFilter) {
                        container.find('select').val(selectedFilter);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading cases:", error);
                    container.html('<select name="filter_id" class="form-control m-1" style="width:200px;"><option value="">All Cases</option></select>').show();
                }
            });

        } else {
            container.hide().html('');
        }
    }
    
    // Initialize on page load if report type is already selected
    $(document).ready(function() {
        var reportType = '<?= $_GET['report_type'] ?? '' ?>';
        if (reportType) {
            updateFilterOptions();
        }
    });
    
    function pay_this_doc(idcus, whichone) {
        var get_total_payment = $("#total_payment" + idcus).val();
        $.ajax({
            type: "POST",
            url: "models/doctor_ledger.php",
            data: {
                doctor_paid_indoor: idcus,
                get_total_payment: get_total_payment,
                whichone: whichone
            },
            success: function(data) {
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                alert("An error occurred while processing your request.");
            }
        });
    }
    
    function view_indoor_private(idcus, whichone) {
        var date_from = $("#date_from").val();
        var date_to = $("#date_to").val();
        
        if(!date_from || !date_to) {
            alert("Please select date range first!");
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "models/doctor_ledger.php",
            data: {
                view_indoor_private_records_reports: idcus,
                whichone: whichone,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                $('.modalbody1').html(data);
                $('#updateinfobutton').trigger('click');
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                alert("An error occurred while loading records.");
            }
        });
    }
    
    function view_case_details(case_id) {
        var date_from = $("#date_from").val();
        var date_to = $("#date_to").val();
        
        if(!date_from || !date_to) {
            alert("Please select date range first!");
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "models/doctor_ledger.php",
            data: {
                view_case_details: 1,
                case_id: case_id,
                date_from: date_from,
                date_to: date_to
            },
            success: function(data) {
                $('.modalbody1').html(data);
                $('#updateinfobutton').trigger('click');
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                alert("An error occurred while loading case details.");
            }
        });
    }
    
    function reloadtablecontent() {
        location.reload();
    }
</script>