<?php

session_start();
include 'database.php';

// Ensure session contains role_name and employee_id
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}
$payslipID = $_GET['pid'];
$query = "SELECT pr.payroll_id, e.firstname, e.middlename, e.lastname, 
        pr.payroll_period, e.project_name, e.employee_number,  ps.position_name, 
        pr.totalHrs, pr.allowance, pr.gross,
        e.withhold_tax, e.sss_con, e.philhealth_con, e.pag_ibig_con, pr.other_deduc,
        pr.total_deduc, pr.netpay, pr.netpay
        FROM payroll pr 
        LEFT JOIN employees e ON e.employee_id = pr.employee_id
        LEFT JOIN positions ps ON ps.position_id = e.employee_id
        WHERE pr.payroll_id = :payslipID
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':payslipID', $payslipID, PDO::PARAM_INT);
$stmt->execute();
$payslip = $stmt->fetch(PDO::FETCH_ASSOC);

$activePage = 'payslip';
include './layout/header.php';
?>
<style>
    /* Colors #1f4d88 */
    .main * {
        margin: 0;
        padding: 0;
        font-family: 'Roboto';
    }

    .main {
        padding: 50px;
    }

    .main header,
    .main footer,
    .main aside,
    .main nav,
    .main form,
    .main iframe,
    .main .menu,
    .main .hero,
    .main .adslot {
        display: none;
    }

    .main .title {
        column-span: all;
        display: flex;
        align-items: baseline;
        gap: 1em;
        flex-wrap: wrap;
    }

    .main .title img {
        width: 100%;
    }

    .main .d-flex {
        display: flex;
    }

    .main .bg-blue {
        background-color: #1f4d88;
    }

    .main .text-white {
        color: white;
    }

    .main .justify-content-center {
        justify-content: center;
    }

    .main .justify-content-between {
        justify-content: space-between;
    }

    .main .align-items-center {
        align-items: center;
    }

    .main .personal-info-container {
        margin-top: 20px;
        ;
    }

    .main .my-10 {
        margin: 10px 0px;
    }

    .main .py-10 {
        padding-top: 10px;
        padding-bottom: 10px0;
    }

    .main .p-10 {
        padding: 10px;
    }

    .main .flex-grow {
        flex-grow: 1;
    }

    .main .text-center {
        text-align: center;
    }

    .main .text-right {
        text-align: right;
    }

    .main .bg-gray {
        background: #f1f1f1;
    }

    .main .gap-5 {
        gap: 5px;
    }

    .main .mt-20 {
        margin-top: 20px;
    }

    @media only screen and (max-width:500px) {
        .main {
            padding: 5px;
        }
    }
</style>
<div class="d-flex">
    <?php include './layout/sidebar.php'; ?>
    <div class="main" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary text-light p-2" id="print_payslip">Print Payslip</button>
        </div>
        <article id="payslip_overview">
            <div class="title">
                <img src="./assets/images/gm3template.jpg" alt="">
            </div>
            <div class="pay-period mt-20">
                <div class="personal-info text-white">
                    <h4 class="bg-blue p-10">Pay Period</h4>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 50%;"><?php echo $payslip['payroll_period'] ?></p>
                    <p class=" bg-gray" style="flex: 1 1 50%;"><?php echo $payslip['project_name'] ?></p>
                </div>
            </div>
            <div class="personal-info-container">
                <div class="personal-info text-white ">
                    <h4 class="bg-blue p-10">Personal Information</h4>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 flex-grow bg-gray"><?php echo $payslip['employee_number'] ?></p>
                    <p class="p-10 flex-grow text-center bg-gray"><?php echo $payslip['firstname'] . ' ' . $payslip['middlename'] . ' ' . $payslip['lastname'] ?></</p>
                    <p class=" flex-grow text-right bg-gray"><?php echo $payslip['position_name'] ?></p>
                </div>
            </div>



            <div class="pay-period mt-20">
                <div class="text-white">
                    <h4 class="bg-blue p-10"></h4>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Regular Hours</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 80%;"><?php echo $payslip['totalHrs'] ?></p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Allowance</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 80%;"><?php echo $payslip['allowance'] ?></p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 20%;"><strong>GROSS PAY</strong></p>
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 80%;"><strong><?php echo $payslip['gross'] ?></strong></p>
            </div>


            <div class="deduction-container mt-20">
                <div class="text-white">
                    <h4 class="bg-blue p-10">Deductions:</h4>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Withholding Tax</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 80%;"><?php echo $payslip['withhold_tax'] ?></p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">SSS Contribution</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 80%;"><?php echo $payslip['sss_con'] ?></p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Philhealth</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 80%;"><?php echo $payslip['philhealth_con'] ?></p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Pag-Ibig</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 80%;"><?php echo $payslip['pag_ibig_con'] ?></p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Others</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 80%;"><?php echo $payslip['other_deduc'] ?></p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 20%;"><strong>Total Deduction</strong></p>
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 80%;"><strong><?php echo $payslip['total_deduc'] ?></strong></p>
                </div>

                <div class="d-flex gap-5 mt-20">
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 20%;"><strong>Net Pay</strong></p>
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 80%;"><strong><?php echo $payslip['netpay'] ?></strong></p>
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <div class="text-center">
                    <h3 style="padding-top: 10px; border-top: 2px solid black; margin-top:70px;"><?php echo strtoupper($payslip['firstname'] . ' ' . $payslip['middlename'] . ' ' . $payslip['lastname']) ?></h3>
                    <p class="m-0 p-0">Signature Over Printed Name / Date</p>
                </div>
            </div>

        </article>
    </div>
</div>


<?php include './layout/script.php'; ?>
<script>
    $(function() {
        $('#print_payslip').click(function() {
            const payslipContent = $('#payslip_overview').html();

            // Create a new window
            const printWindow = window.open('', '', 'height=800,width=1000');
            printWindow.document.write('<html><head><title>Payslip Overview</title>');
            printWindow.document.write(`
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="assets/css/style.css">
                <link rel="stylesheet" href="assets/css/main.css">
                <link rel="stylesheet" href="assets/css/print_payslip.css">
                <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
            `);
            printWindow.document.write('</head><body>');

            printWindow.document.write(payslipContent);
            printWindow.document.write('</body></html>');

            // Print the content
            printWindow.document.close();
            printWindow.print();
        })
    })
</script>
<?php include './layout/footer.php'; ?>