<?php

session_start();

// Ensure session contains role_name and employee_id
if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$activePage = 'payslip';

include './layout/header.php';
?>
<style>
        /* Colors #1f4d88 */
        .main-content *{
            margin: 0;
            padding: 0;
            font-family: 'Roboto';
        }
        .main-content{
            padding:50px;
        }
        .main-content header,    .main-content footer,    .main-content aside,    .main-content nav,    .main-content form,    .main-content iframe,    .main-content .menu,    .main-content .hero,    .main-content .adslot {
            display: none;
        }
        .main-content .title {
            column-span: all;
            display: flex;
            align-items: baseline;
            gap: 1em;
            flex-wrap: wrap;
        }
        .main-content .title img{ 
            width: 100%;
        }
        .main-content .d-flex{
            display:flex;
        }
        .main-content .bg-blue{
            background-color: #1f4d88;
        }
        .main-content .text-white{
            color:white;
        }
        .main-content .justify-content-center{
            justify-content: center;
        }
        .main-content .justify-content-between{
            justify-content: space-between;
        }
        .main-content .align-items-center{
            align-items:center;
        }
        .main-content .personal-info-container{
            margin-top: 20px;;
        }
        .main-content .my-10{
            margin: 10px 0px;
        }
        .main-content .py-10{
           padding-top:10px;
           padding-bottom: 10px0;
        }
        .main-content .p-10{
            padding:10px;
        }
        .main-content .flex-grow{
            flex-grow: 1;
        }
        .main-content .text-center{
            text-align: center;
        }
        .main-content .text-right{
            text-align: right;
        }
        .main-content .bg-gray{
            background: #f1f1f1;
        }
        .main-content .gap-5{
            gap:5px;
        }

        .main-content .mt-20{
            margin-top: 20px;
        }

        @media only screen and (max-width:500px){
         .main-content{
            padding:5px;
         }
        }
    </style>
      <div class="d-flex">
         <?php include './layout/sidebar.php'; ?>
         <div class="main-content" style="max-height: calc(100vh - 70px);overflow-y:scroll">
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
   

      <?php include './layout/footer.php'; ?>