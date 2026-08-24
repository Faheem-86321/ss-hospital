<?php 
// This if else condition will be same
if(mysqli_num_rows($execuit)>0){
	?>
<style>
    .counter {
        color: #f51c1c;
        text-align: center;
        width: 200px;
        height: 200px;
        padding: 20px;
        margin: 0 auto;
        position: relative;
        font-family: 'Poppins', sans-serif;
    }

    .counter h3 {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #333;
        margin-bottom: 6px;
    }

    .counter p {
        font-size: 11px;
        color: #888;
        margin-bottom: 4px;
        font-weight: 500;
    }

    .counter .counter-value {
        font-size: 22px;
        font-weight: 700;
        color: #111;
        display: block;
        margin-top: 4px;
    }

    .counter .counter-icon {
        color: #fff;
        font-size: 22px;
        position: absolute;
        bottom: 25px;
        right: 28px;
        z-index: 1;
        background: #f51c1c;
        padding: 8px;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* Base Circular Animation */
    .counter:before,
    .counter:after {
        content: "";
        width: 200px;
        height: 200px;
        border: 15px solid #f51c1c;
        border-right: 15px solid transparent;
        border-bottom: 15px solid transparent;
        border-left: 15px solid transparent;
        border-radius: 50%;
        transform: translate(-50%, -50%) rotate(45deg);
        position: absolute;
        top: 50%;
        left: 50%;
        transition: 0.4s ease;
    }

    .counter:after {
        height: 187px;
        width: 187px;
        border: 3px solid #f51c1c;
        border-right: 3px solid transparent;
    }

    .counter:hover:before {
        transform: translate(-50%, -50%) rotate(90deg);
        opacity: 0.9;
    }

    /* Inner Content Circle */
    .counter .counter-content {
        background: #fff;
        width: 160px;
        height: 160px;
        padding: 40px 20px;
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        position: relative;
        z-index: 1;
    }

    /* Gradient Accent */
    .counter .counter-content:before {
        content: "";
        background: linear-gradient(45deg, #fe8605, #f51c1c);
        width: calc(100% - 10px);
        height: calc(100% - 10px);
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: -1;
        opacity: 0.2;
    }

    /* Colors for Variants */
    .counter.purple { color: #a21a6e; }
    .counter.blue { color: #0dabc6; }
    .counter.green { color: #10ce29; }

    .counter.purple .counter-icon { background: #a21a6e; }
    .counter.blue .counter-icon { background: #0dabc6; }
    .counter.green .counter-icon { background: #10ce29; }

    /* Responsive */
    @media screen and (max-width: 990px) {
        .counter { margin-bottom: 40px; }
    }
</style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-3">
                <a href="?page=all_reports" class="btn btn-danger btn-lg" style="font-size:18px;">
                    <i class="fa fa-chart-bar"></i> &nbsp; View All Reports
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="counter">
                            <div class="counter-content">
                                <h3>Total Patients</h3>
                                <p>Total</p>
                                <span class="counter-value" style="margin-top: 0px !important;">
                                    <?php 
                                    $select_query = "SELECT * FROM ssh_p_reg";
                                    $select_query_ex = mysqli_query($con,$select_query);
                                    echo mysqli_num_rows($select_query_ex);
                                    ?>
                                </span>
                            </div>
                            <div class="counter-icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="counter purple">
                            <div class="counter-content">
                                <h3>OPD Income</h3>
                                <p>Current Month</p>
                                <span class="counter-value">
                                    <?php 
                                    
                                    $select_query = "SELECT COUNT(ssh_p_dpr.MRN) AS total_patient,SUM(((ssh_p_dpr.Charges-50)-ssh_p_dpr.D_Pay) - ((((ssh_p_dpr.Charges-50)-ssh_p_dpr.D_Pay)*100)/(ssh_p_dpr.Charges-50))*((ssh_p_dpr.Charges-50)-(ssh_p_dpr.Paid-50))/100)+(50*count(MRN))  AS hosiptal FROM ssh_p_dpr,ssh_dr_reg
                                    WHERE ssh_p_dpr.D_ID = ssh_dr_reg.D_ID  AND MONTH(CONVERT(A_DATE,Date)) = MONTH(CURRENT_DATE())";
                                    $select_query_ex = mysqli_query($con,$select_query);
                                    $toppaid = 0;
                                    foreach($select_query_ex as $row){
                                        $toppaid += $row["hosiptal"];
                                    }
                                    $select_query = "SELECT *,SUM(paid)  AS paid FROM ssh_p_services
                                    WHERE MONTH(CONVERT(DATE,Date)) = MONTH(CURRENT_DATE())";
                                    $select_query_ex = mysqli_query($con,$select_query);
                                    foreach($select_query_ex as $row){
                                        $toppaid1 += $row["paid"];
                                        echo $toppaid1;
                                        
                                    }
                                    
                                    ?>
                                </span>
                            </div>
                            <div class="counter-icon">
                                <i class="fa fa-stethoscope"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <div class="counter">
                            <div class="counter-content">
                                <h3>IPD Income</h3>
                                <p>Current Month</p>
                                <span class="counter-value">
                                    <?php 
                                    $toppaid = 0;
                                    $select_query = "SELECT *, 
                                    (SELECT SUM(Paid) FROM ssh_p_indoor WHERE  MONTH(CONVERT(ssh_p_indoor.admit_date,Date)) = MONTH(CURRENT_DATE())) - (SELECT SUM(D_Fee) FROM ssh_p_indoor_doctors WHERE pi_id IN (SELECT pi_id FROM ssh_p_indoor WHERE  MONTH(CONVERT(ssh_p_indoor.admit_date,Date)) = MONTH(CURRENT_DATE()))) AS total FROM ssh_p_indoor 
                                         WHERE   MONTH(CONVERT(ssh_p_indoor.admit_date,Date)) = MONTH(CURRENT_DATE()) GROUP BY MONTH(admit_date)";
                                         $select_query_ex = mysqli_query($con,$select_query);
                                         foreach($select_query_ex as $row){
                                            $toppaid += $row["total"];
                                        }
                                        echo $toppaid;
                                        ?>
                                    </span>
                                </div>
                                <div class="counter-icon">
                                    <i class="fa fa-wheelchair"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="counter purple">
                                <div class="counter-content">
                                    <h3>Total Expense</h3>
                                    <p>Current Month</p>
                                    <span class="counter-value">
                                        <?php 
                                        $toppaid = 0;
                                        $select_query = "SELECT * FROM ssh_expenses WHERE   MONTH(CONVERT(Date,Date)) = MONTH(CURRENT_DATE())";
                                        $select_query_ex = mysqli_query($con,$select_query);
                                        foreach($select_query_ex as $row){
                                            $toppaid += $row["Amount"];
                                        }
                                        echo $toppaid;
                                        ?>
                                    </span>
                                </div>
                                <div class="counter-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>

                        <style type="text/css">
                            .new_style{
                                border-bottom: 1px solid grey; padding:10px ; 
                            }
                            @media screen and (max-width: 767px) {
                                .res1{
                                    border: none;
                                    margin-top: 10px;
                                    color: white !important;
                                    background-color: coral;
                                }
                                .res2{
                                    border: none;
                                    margin-top: 10px;
                                    color: white !important;
                                    background-color: #2a7aBe;
                                }
                                .res3{
                                    border: none;
                                    margin-top: 10px;
                                    color: white !important;
                                    background-color: #E4CD05;
                                }
                                .res4{
                                    border: none;
                                    margin-top: 10px;
                                    color: white !important;
                                    background-color: green;
                                }
                            }
                        </style>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12" >
                    <div class="row" style="padding: 18px;" >
                        <div class="col-md-6 new_style res1" style="color: coral;" >
                            <h2 style="color: #f24c4f !important;text-shadow: 0 3px 10px rgb(0 0 0 / 0.2)"><b>X-Rays</b></h2>

                            <h3 class="count_no_value text-center">
                                <?php
                                $select_query = "SELECT *,SUM(Amount) FROM ssh_expenses WHERE (Title = 'X-Ray-Small' OR Title = 'X-Ray-Big') AND MONTH(Date) = '".date('m')."' GROUP BY MONTH(Date)";
                                $select_query_ex = mysqli_query($con,$select_query);
                                if (mysqli_num_rows($select_query_ex) != 0) {
                                 foreach($select_query_ex as $month){
                                    echo $month['SUM(Amount)'];
                                }
                            }
                            else{
                                echo "0";
                            }
                            ?>
                        </h3>

                        <h4 class="text-center"> <b>Expense</b></h4>
                        <p class="text-center">Current Month</p>

                    </div>
                    <div class="col-md-6 new_style res2" style="border-left: 1px solid grey ;color: #2a7aBe;">
                        <h2>&nbsp</h2>
                        <h3 class="count_no_value text-center">
                            <?php
                            $select_query = "SELECT *,SUM(ssh_p_services.Paid) AS total FROM ssh_p_services JOIN ssh_ser_cat ON ssh_p_services.C_ID = ssh_ser_cat.C_ID  Join ssh_ser_inv ON ssh_ser_cat.ser_id = ssh_ser_inv.ID WHERE (ssh_ser_inv.ID = '1' OR ssh_ser_inv.ID = '6') AND  MONTH(ssh_p_services.Date) = '".date('m')."' GROUP BY MONTH(ssh_p_services.Date)";
                            $select_query_ex = mysqli_query($con,$select_query);
                            if (mysqli_num_rows($select_query_ex) != 0) {
                             foreach($select_query_ex as $month){
                                echo $month['total'];
                            }
                        }
                        else{
                            echo "0";
                        }
                        ?>
                    </h3>
                    <h4 class="text-center"> <b>Income</b></h4>
                    <p class="text-center">Current Month</p>
                </div>

                <div class="col-md-6 res3" style="padding-top:10px;color: #E4CD05;" >
                    <h2 style="color: #f24c4f !important;text-shadow: 0 3px 10px rgb(0 0 0 / 0.2)"><b>CT-Scan</b></h2>
                    <h3 class="count_no_value text-center">
                        <?php
                        $select_query = "SELECT *,SUM(Amount) FROM ssh_expenses WHERE Title = 'CT-Scan' AND MONTH(Date) = '".date('m')."' GROUP BY MONTH(Date)";
                        $select_query_ex = mysqli_query($con,$select_query);
                        if (mysqli_num_rows($select_query_ex) != 0) {
                         foreach($select_query_ex as $month){
                            echo $month['SUM(Amount)'];
                        }
                    }
                    else{
                        echo "0";
                    }

                    ?>
                </h3>
                <h4 class="text-center"> <b>Expense</b></h4>
                <p class="text-center">Current Month</p>

            </div>
            <div class="col-md-6 res4" style="border-left: 1px solid grey ;color: green;padding-top:10px">
                <h2>&nbsp</h2>
                <h3 class="count_no_value text-center">
                    <?php
                    $select_query = "SELECT *,SUM(ssh_p_services.Paid) AS total FROM ssh_p_services JOIN ssh_ser_cat ON ssh_p_services.C_ID = ssh_ser_cat.C_ID  Join ssh_ser_inv ON ssh_ser_cat.ser_id = ssh_ser_inv.ID WHERE (ssh_ser_inv.ID = '2') AND  MONTH(ssh_p_services.Date) = '".date('m')."' GROUP BY MONTH(ssh_p_services.Date)";
                    $select_query_ex = mysqli_query($con,$select_query);
                    if (mysqli_num_rows($select_query_ex) != 0) {
                     foreach($select_query_ex as $month){
                        echo $month['total'];
                    }
                }
                else{
                    echo "0";
                }

                ?>
            </h3>
            <h4 class="text-center"> <b>Income</b></h4>
            <p class="text-center">Current Month</p>
        </div>
    </div>
</div>
<div class="col-xl-12 mt-2" >
    <div class="card-box " style="border-right: 3px solid #f24c4f ;box-shadow: 0 3px 10px rgb(0 0 0 / 0.2)">
        <div class="float-right d-none d-md-inline-block" style="color: black !important;">
           <div class="btn-group " style="background: #f24c4f;color: black !important;">
              <button type="button" class="btn btn-xs btn-primary  m-1" >Monthly</button>
          </div>
      </div>

      <h4 class="header-title mb-3 p-2" style="background: #f24c4f;color: black !important;"><i class="fa fa-wheelchair"></i> Yealy Income & Expense</h4>

      <div dir="ltr">
        <div id="deal-analytics-ovarall" class="mt-4" data-colors="#6658dd,#f1556c"></div>
    </div>
</div> <!-- end card-box -->
</div> <!-- end col-->



<div class="col-xl-12">
    <div class="card-box pb-2" style="border-right: 3px solid #f24c4f ;box-shadow: 0 3px 10px rgb(0 0 0 / 0.2)">
        <div class="float-right d-none d-md-inline-block" style="background: #f24c4f;color: black !important;">
            <div class="btn-group" style="background: #f24c4f;color: black !important;">
                <button type="button" class="btn btn-xs btn-primary m-1">Monthly</button>
            </div>
        </div>

        <h4 class="header-title mb-3 p-2" style="background: #f24c4f;color: black !important;"> <i class="fa fa-wheelchair"></i>  Indoor Analytics</h4>

        <div dir="ltr">
            <div id="deal-analytics2" class="mt-4" data-colors="#6658dd,#1abc9c,#6658dd,#1abc9c"></div>
        </div>
    </div> <!-- end card-box -->
</div> <!-- end col-->

<div class="col-xl-12">
    <div class="card" style="border-right: 3px solid #f24c4f ;box-shadow: 0 3px 10px rgb(0 0 0 / 0.2)">
        <div class="card-body">
            <div class="float-right d-none d-md-inline-block" style="background: #f24c4f;color: black !important;">
                <div class="btn-group" style="background: #f24c4f;color: black !important;">
                    <button type="button" class="btn btn-xs btn-primary m-1">Monthly</button>
                </div>
            </div>
            <h4 class="header-title mb-0 p-2" style="background: #f24c4f;color: black !important;"> <i class="fa fa-wheelchair"></i> Dialysis Analytics</h4>

            <div id="cardCollpase5" class="collapse pt-3 show" dir="ltr">
                <div id="apex-column-1" class="apex-charts" data-colors="#6658dd,#1abc9c,#6658dd,#1abc9c,#ff0000"></div>
            </div> <!-- collapsed end -->
        </div> <!-- end card-body -->
    </div> <!-- end card-->
</div> <!-- end col-->
</div>
</div>
</div>
<script>

</script>	
<?php  }else{
    header('location:logout');
} ?>       