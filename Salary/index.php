<?php

// =====================================================
// HELPER FUNCTIONS
// =====================================================

function old($key)
{
    return isset($_POST[$key])
        ? htmlspecialchars($_POST[$key])
        : '';
}

function num($key)
{
    if (isset($_POST[$key]) && is_numeric($_POST[$key])) {
        return (float) $_POST[$key];
    } else {
        return 0;
    }
}

function fmt($number)
{
    return number_format($number, 2);
}


// =====================================================
// MONTHS
// =====================================================

$months = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
];


// =====================================================
// DEFAULT VALUES
// =====================================================

$calculated = false;
$error = false;
$error_message = '';


// =====================================================
// CALCULATION
// =====================================================

if (isset($_POST['calculate'])) {

    // Employee details
    $emp_id      = $_POST['emp_id'];
    $emp_name    = $_POST['emp_name'];
    $designation = $_POST['designation'];
    $month       = $_POST['month'];

    // Salary details
    $basic     = num('basic');
    $allowance = num('allowance');
    $ot_hours  = num('ot_hours');
    $ot_rate   = num('ot_rate');
    $advance   = num('advance');
    $other     = num('other');


    // =================================================
    // SWITCH CASE VALIDATION
    // =================================================

    /*
       Validation code:

       1 = Basic salary error
       2 = Allowance error
       3 = OT hours error
       4 = OT rate error
       5 = Deduction error
       6 = Valid
    */

    if ($basic <= 0) {

        $validation = 1;

    } elseif ($allowance < 0) {

        $validation = 2;

    } elseif ($ot_hours < 0) {

        $validation = 3;

    } elseif ($ot_rate < 0) {

        $validation = 4;

    } elseif ($advance < 0 || $other < 0) {

        $validation = 5;

    } else {

        $validation = 6;
    }


    // =================================================
    // SWITCH CASE
    // =================================================

    switch ($validation) {

        case 1:

            $error = true;

            $error_message =
                'Basic salary must be greater than 0.';

            break;


        case 2:

            $error = true;

            $error_message =
                'Allowance cannot be negative.';

            break;


        case 3:

            $error = true;

            $error_message =
                'OT hours cannot be negative.';

            break;


        case 4:

            $error = true;

            $error_message =
                'OT rate cannot be negative.';

            break;


        case 5:

            $error = true;

            $error_message =
                'Deductions cannot be negative.';

            break;


        case 6:

            // =========================================
            // VALID DATA
            // =========================================

            // OT Payment
            $ot_pay =
                $ot_hours * $ot_rate;


            // Gross Salary
            $gross =
                $basic +
                $allowance +
                $ot_pay;


            // Employee EPF 8%
            $epf =
                $basic * 0.08;


            // Employer EPF 12%
            $epf_er =
                $basic * 0.12;


            // Employer ETF 3%
            $etf =
                $basic * 0.03;


            // Total deductions
            $total_ded =
                $epf +
                $advance +
                $other;


            // Net Salary
            $net =
                $gross -
                $total_ded;


            // =========================================
            // SECOND SWITCH CASE
            // CHECK NET SALARY
            // =========================================

            if ($net < 0) {

                $net_status = 1;

            } else {

                $net_status = 2;
            }


            switch ($net_status) {

                case 1:

                    $error = true;

                    $error_message =
                        'Total deductions are more than the gross salary.';

                    break;


                case 2:

                    $calculated = true;

                    break;
            }

            break;


        default:

            $error = true;

            $error_message =
                'Unknown validation error.';

            break;
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Office Salary Calculator</title>


<style>

:root {

    --primary: #1e3a5f;
    --primary-dark: #14294a;
    --accent: #2f80ed;
    --bg: #eef2f7;
    --card: #ffffff;
    --border: #dbe2ec;
    --text: #1f2937;
    --muted: #6b7280;
    --radius: 10px;
    --shadow:
        0 4px 16px
        rgba(30,58,95,0.10);
}


* {
    box-sizing: border-box;
}


body {

    margin: 0;

    font-family:
        "Segoe UI",
        Roboto,
        Arial,
        sans-serif;

    background: var(--bg);

    color: var(--text);

    font-size: 14px;

    line-height: 1.5;
}


/* ================= HEADER ================= */

.topbar {

    background:
        linear-gradient(
            135deg,
            var(--primary-dark),
            var(--primary) 60%,
            #2c5282
        );

    color: white;

    padding: 16px 32px;

    display: flex;

    justify-content:
        space-between;

    align-items: center;

    box-shadow:
        0 2px 10px
        rgba(0,0,0,0.2);
}


.brand {

    font-size: 20px;

    font-weight: 700;

    letter-spacing: 0.5px;
}


.sub {

    font-size: 12px;

    opacity: 0.85;
}


/* ================= LAYOUT ================= */

.wrap {

    max-width: 1100px;

    margin: 30px auto;

    padding: 0 15px;

    display: flex;

    gap: 24px;

    flex-wrap: wrap;

    align-items:
        flex-start;
}


.panel {

    background:
        var(--card);

    border-radius:
        var(--radius);

    box-shadow:
        var(--shadow);

    flex: 1;

    min-width: 320px;

    overflow: hidden;
}


.panel h2 {

    margin: 0;

    padding: 14px 20px;

    font-size: 16px;

    color: white;

    background:
        var(--primary);

    border-left:
        5px solid var(--accent);
}


.panel .body {

    padding: 22px;
}


/* ================= FORM ================= */

.row {

    display: flex;

    gap: 14px;
}


.field {

    flex: 1;

    margin-bottom: 14px;
}


label {

    display: block;

    font-size: 12px;

    font-weight: 600;

    color: #4b5563;

    margin-bottom: 5px;
}


input,
select {

    width: 100%;

    padding: 10px 12px;

    border:
        1px solid
        var(--border);

    border-radius: 6px;

    font-size: 14px;

    background: #f9fafc;
}


input:focus,
select:focus {

    outline: none;

    background: white;

    border-color:
        var(--accent);

    box-shadow:
        0 0 0 3px
        rgba(47,128,237,0.18);
}


/* ================= SECTION TITLE ================= */

.section-title {

    font-size: 12px;

    font-weight: 700;

    color: var(--primary);

    text-transform: uppercase;

    letter-spacing: 1px;

    padding-bottom: 6px;

    margin: 10px 0 14px;

    border-bottom:
        2px solid
        var(--border);
}


/* ================= BUTTONS ================= */

.btns {

    display: flex;

    gap: 10px;

    margin-top: 8px;
}


button {

    padding: 11px 20px;

    border: none;

    border-radius: 6px;

    font-size: 14px;

    font-weight: 600;

    cursor: pointer;
}


.btn-primary {

    background:
        linear-gradient(
            135deg,
            var(--primary),
            #2c5282
        );

    color: white;

    flex: 1;
}


.btn-primary:hover {

    opacity: 0.9;
}


.btn-light {

    background: #e8edf4;

    color: var(--text);
}


.btn-light:hover {

    background: #d9e1ec;
}


/* ================= PAYSLIP ================= */

table {

    width: 100%;

    border-collapse:
        collapse;

    margin-bottom: 16px;
}


th {

    text-align: left;

    background: #f1f5fa;

    font-size: 12px;

    text-transform:
        uppercase;

    padding: 9px 10px;

    color: var(--primary);

    border-bottom:
        2px solid
        var(--border);
}


td {

    padding: 9px 10px;

    border-bottom:
        1px solid
        #edf1f6;
}


tr:nth-child(even) td {

    background:
        #fafbfd;
}


td.amt,
th.amt {

    text-align: right;
}


tr.sum td {

    font-weight: 700;

    background:
        #eef3fa;
}


tr.net td {

    font-weight: 700;

    font-size: 17px;

    background:
        linear-gradient(
            135deg,
            var(--primary),
            #2c5282
        );

    color: white;

    padding: 13px 14px;
}


/* ================= EMPLOYEE INFO ================= */

.info {

    display: flex;

    justify-content:
        space-between;

    flex-wrap: wrap;

    gap: 10px 20px;

    margin-bottom: 18px;

    padding: 12px 14px;

    background: #f1f5fa;

    border-radius: 8px;

    font-size: 13px;
}


.info span {

    color: var(--muted);
}


.note {

    font-size: 12px;

    color: var(--muted);
}


/* ================= RESPONSIVE ================= */

@media (max-width: 600px) {

    .row {

        flex-direction:
            column;

        gap: 0;
    }


    .topbar {

        padding: 14px 18px;

        flex-direction:
            column;

        gap: 5px;

        align-items:
            flex-start;
    }
}


/* ================= PRINT ================= */

@media print {

    body {

        background: white;
    }


    .topbar,
    #formPanel,
    .btns-print {

        display: none;
    }


    .wrap {

        margin: 0;

        max-width: 100%;
    }


    .panel {

        box-shadow: none;
    }
}

</style>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>


<body>


<!-- ================= HEADER ================= -->

<div class="topbar">

    <div class="brand">

        ABC Company (Pvt) Ltd

    </div>


    <div class="sub">

        HR & Payroll Department

    </div>

</div>



<div class="wrap">


<!-- =================================================
     SALARY FORM
================================================= -->

<div class="panel" id="formPanel">


<h2>

    Employee Salary Calculator

</h2>


<div class="body">


<form method="post">


<!-- EMPLOYEE DETAILS -->

<div class="section-title">

    Employee Details

</div>


<div class="row">


<div class="field">

<label>
    Employee ID
</label>

<input
    type="text"
    name="emp_id"
    value="<?php echo old('emp_id'); ?>"
    required>

</div>


<div class="field">

<label>
    Employee Name
</label>

<input
    type="text"
    name="emp_name"
    value="<?php echo old('emp_name'); ?>"
    required>

</div>


</div>



<div class="row">


<div class="field">

<label>
    Designation
</label>

<input
    type="text"
    name="designation"
    value="<?php echo old('designation'); ?>">

</div>



<div class="field">

<label>
    Month
</label>


<select name="month">

<?php

foreach ($months as $m) {

    if (
        isset($_POST['month']) &&
        $_POST['month'] == $m
    ) {

        echo
            "<option selected>$m</option>";

    } else {

        echo
            "<option>$m</option>";
    }
}

?>

</select>

</div>

</div>



<!-- EARNINGS -->

<div class="section-title">

    Earnings

</div>


<div class="row">


<div class="field">

<label>
    Basic Salary
</label>

<input
    type="number"
    name="basic"
    min="0"
    step="any"
    value="<?php echo old('basic'); ?>"
    required>

</div>


<div class="field">

<label>
    Allowance
</label>

<input
    type="number"
    name="allowance"
    min="0"
    step="any"
    value="<?php echo old('allowance'); ?>">

</div>

</div>



<div class="row">


<div class="field">

<label>
    OT Hours
</label>

<input
    type="number"
    name="ot_hours"
    min="0"
    step="any"
    value="<?php echo old('ot_hours'); ?>">

</div>


<div class="field">

<label>
    OT Rate (per hour)
</label>

<input
    type="number"
    name="ot_rate"
    min="0"
    step="any"
    value="<?php echo old('ot_rate'); ?>">

</div>

</div>



<!-- DEDUCTIONS -->

<div class="section-title">

    Deductions

</div>


<div class="row">


<div class="field">

<label>
    Salary Advance
</label>

<input
    type="number"
    name="advance"
    min="0"
    step="any"
    value="<?php echo old('advance'); ?>">

</div>


<div class="field">

<label>
    Other Deductions
</label>

<input
    type="number"
    name="other"
    min="0"
    step="any"
    value="<?php echo old('other'); ?>">

</div>

</div>



<!-- BUTTONS -->

<div class="btns">


<button
    type="submit"
    name="calculate"
    class="btn-primary">

    Calculate Salary

</button>


<button
    type="button"
    class="btn-light"
    onclick="clearForm()">

    Clear

</button>


</div>


</form>

</div>

</div>



<!-- =================================================
     PAYSLIP
================================================= -->

<?php if ($calculated) { ?>


<div class="panel">


<h2>

    Payslip

</h2>


<div class="body">


<!-- EMPLOYEE INFO -->

<div class="info">


<div>

<span>
    Employee ID:
</span>

<b>

<?php
echo htmlspecialchars($emp_id);
?>

</b>

</div>



<div>

<span>
    Name:
</span>

<b>

<?php
echo htmlspecialchars($emp_name);
?>

</b>

</div>



<div>

<span>
    Designation:
</span>

<b>

<?php

if ($designation != '') {

    echo htmlspecialchars($designation);

} else {

    echo '-';
}

?>

</b>

</div>



<div>

<span>
    Month:
</span>

<b>

<?php
echo htmlspecialchars($month);
?>

</b>

</div>


</div>



<!-- EARNINGS -->

<table>

<tr>

<th>
    Earnings
</th>

<th class="amt">
    Amount
</th>

</tr>


<tr>

<td>
    Basic Salary
</td>

<td class="amt">

<?php
echo fmt($basic);
?>

</td>

</tr>


<tr>

<td>
    Allowance
</td>

<td class="amt">

<?php
echo fmt($allowance);
?>

</td>

</tr>


<tr>

<td>
    OT Payment
</td>

<td class="amt">

<?php
echo fmt($ot_pay);
?>

</td>

</tr>


<tr class="sum">

<td>
    Gross Salary
</td>

<td class="amt">

<?php
echo fmt($gross);
?>

</td>

</tr>

</table>



<!-- DEDUCTIONS -->

<table>

<tr>

<th>
    Deductions
</th>

<th class="amt">
    Amount
</th>

</tr>


<tr>

<td>
    EPF (Employee 8%)
</td>

<td class="amt">

<?php
echo fmt($epf);
?>

</td>

</tr>


<tr>

<td>
    Salary Advance
</td>

<td class="amt">

<?php
echo fmt($advance);
?>

</td>

</tr>


<tr>

<td>
    Other Deductions
</td>

<td class="amt">

<?php
echo fmt($other);
?>

</td>

</tr>


<tr class="sum">

<td>
    Total Deductions
</td>

<td class="amt">

<?php
echo fmt($total_ded);
?>

</td>

</tr>

</table>



<!-- NET SALARY -->

<table>

<tr class="net">

<td>
    NET SALARY
</td>

<td class="amt">

<?php
echo fmt($net);
?>

</td>

</tr>

</table>



<!-- EMPLOYER CONTRIBUTION -->

<p class="note">

Employer contributions:

EPF 12% =

<b>

<?php
echo fmt($epf_er);
?>

</b>

|

ETF 3% =

<b>

<?php
echo fmt($etf);
?>

</b>

</p>



<!-- PRINT -->

<div class="btns btns-print">

<button
    type="button"
    class="btn-primary"
    onclick="window.print()">

    Print Payslip

</button>

</div>


</div>

</div>


<?php } ?>


</div>



<!-- =================================================
     JAVASCRIPT
================================================= -->

<script>

function clearForm()
{

    Swal.fire({

        icon: 'warning',

        title: 'Clear the form?',

        text:
            'All entered data will be removed.',

        showCancelButton: true,

        confirmButtonText:
            'Yes, clear it',

        cancelButtonText:
            'Cancel',

        confirmButtonColor:
            '#d33',

        cancelButtonColor:
            '#6b7280'

    }).then(function(result) {

        if (result.isConfirmed) {

            window.location.href =
                window.location.pathname;

        }

    });

}

</script>



<!-- =================================================
     ERROR MESSAGE
================================================= -->

<?php if ($error) { ?>

<script>

Swal.fire({

    icon: 'error',

    title: 'Invalid Salary Details',

    text:
        <?php
        echo json_encode(
            $error_message
        );
        ?>,

    confirmButtonColor:
        '#1e3a5f'

});

</script>

<?php } ?>



<!-- =================================================
     SUCCESS MESSAGE
================================================= -->

<?php if ($calculated) { ?>

<script>

Swal.fire({

    icon: 'success',

    title: 'Salary Calculated!',

    html:

        <?php

        echo json_encode(

            'Net salary for <b>' .
            htmlspecialchars($emp_name) .
            '</b><br>' .

            '<span style="' .
            'font-size:26px;' .
            'font-weight:700;' .
            'color:#1e3a5f;">' .

            fmt($net) .

            '</span>'

        );

        ?>,

    confirmButtonText:
        'View Payslip',

    confirmButtonColor:
        '#1e3a5f',

    timer: 3500,

    timerProgressBar: true

});

</script>

<?php } ?>


</body>

</html>
