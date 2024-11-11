<?php

session_start();

// Ensure session contains role_name and employee_id
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}


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
    <div class="main" style="max-height: calc(100vh - 70px);overflow-y:scroll">
        <article>
            <div class="title">
                <img src="./assets/images/gm3template.jpg" alt="">
            </div>
            <div class="pay-period mt-20">
                <div class="personal-info text-white">
                    <h4 class="bg-blue p-10">Pay Period</h4>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 50%;">October 11-15, 2024</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 50%;">Project #75 Coron</p>
                </div>
            </div>
            <div class="personal-info-container">
                <div class="personal-info text-white ">
                    <h4 class="bg-blue p-10">Personal Information</h4>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 flex-grow bg-gray">Employee #001</p>
                    <p class="p-10 flex-grow text-center bg-gray">Justin Kyle Caragan</p>
                    <p class="p-10 flex-grow text-right bg-gray">Mechanical Engineer</p>
                </div>
            </div>



            <div class="pay-period mt-20">
                <div class="text-white">
                    <h4 class="bg-blue p-10"></h4>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Regular Hours</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">104</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 60%;">10000</p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Allowance/Others</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">___________</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 60%;">2000</p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;"></p>
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">___________</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 60%;"></p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 40%;"><strong>GROSS PAY</strong></p>
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 60%;"><strong>12000</strong></p>
                </div>
            </div>


            <div class="deduction-container mt-20">
                <div class="text-white">
                    <h4 class="bg-blue p-10">Deductions:</h4>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Withholding Tax</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">___________</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 60%;">0</p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">SSS Contribution</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">___________</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 60%;">200</p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Philhealth</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">___________</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 60%;">50</p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Pag-Ibig</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">___________</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 60%;">0</p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">Others</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 20%;">___________</p>
                    <p class="p-10 bg-gray" style="flex: 1 1 60%;">0</p>
                </div>
                <div class="d-flex gap-5">
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 40%;"><strong>Total Deduction</strong></p>
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 60%;"><strong>250</strong></p>
                </div>

                <div class="d-flex gap-5 mt-20">
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 40%;"><strong>Net Pay</strong></p>
                    <p class="p-10 bg-blue text-white" style="flex: 1 1 60%;"><strong>11750</strong></p>
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <div class="text-center">
                    <h3 style="padding-top: 10px; border-top: 2px solid black; margin-top:70px;">Justin Kyle Caragan</h3>
                    <p class="m-0 p-0">Signature Over Printed Name / Date</p>
                </div>
            </div>

        </article>
    </div>
</div>


<?php include './layout/script.php'; ?>
<?php include './layout/footer.php'; ?>