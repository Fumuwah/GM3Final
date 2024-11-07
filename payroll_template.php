<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GM3 Payroll</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
        /* Colors #1f4d88 */
        *{
            margin: 0;
            padding: 0;
            font-family: 'Roboto';
        }
        body{
            padding:50px;
        }
        header, footer, aside, nav, form, iframe, .menu, .hero, .adslot {
            display: none;
        }
        .title {
            column-span: all;
            display: flex;
            align-items: baseline;
            gap: 1em;
            flex-wrap: wrap;
        }
        .title img{ 
            width: 100%;
        }
        .d-flex{
            display:flex;
        }
        .bg-blue{
            background-color: #1f4d88;
        }
        .text-white{
            color:white;
        }
        .justify-content-center{
            justify-content: center;
        }
        .justify-content-between{
            justify-content: space-between;
        }
        .align-items-center{
            align-items:center;
        }
        .personal-info-container{
            margin-top: 40px;;
        }
        .my-10{
            margin: 10px 0px;
        }
        .py-10{
           padding-top:10px;
           padding-bottom: 10px0;
        }
        .p-10{
            padding:10px;
        }
        .flex-grow{
            flex-grow: 1;
        }
        .text-center{
            text-align: center;
        }
        .text-right{
            text-align: right;
        }
        .bg-gray{
            background: #f1f1f1;
        }
        .gap-5{
            gap:5px;
        }

        .mt-20{
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <article>
        <div class="title">
            <img src="./assets/images/form-header-removed-bg.png" alt="">
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
             <div class="personal-info text-white">
                <h4 class="bg-blue p-10">Pay Period</h4>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 flex-grow bg-gray">10-15</p>
             </div>
        </div>

        <div class="pay-period mt-20">
             <div class="text-white">
                <h4 class="bg-blue p-10">Earnings</h4>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-gray" style="flex: 1 1 40%;">Basic Salary</p>
                <p class="p-10 bg-gray" style="flex: 1 1 60%;">10000</p>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-gray" style="flex: 1 1 40%;">Allowance</p>
                <p class="p-10 bg-gray" style="flex: 1 1 60%;">2000</p>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-gray" style="flex: 1 1 40%;">Overtime (OT)</p>
                <p class="p-10 bg-gray" style="flex: 1 1 60%;">400</p>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-gray" style="flex: 1 1 40%;">Leave Pay (SL/VL & Holiday)</p>
                <p class="p-10 bg-gray" style="flex: 1 1 60%;">10</p>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-blue text-white" style="flex: 1 1 40%;"><strong>Total</strong></p>
                <p class="p-10 bg-blue text-white" style="flex: 1 1 60%;"><strong>10</strong></p>
             </div>
        </div>


        <div class="deduction-container mt-20">
             <div class="text-white">
                <h4 class="bg-blue p-10">Deduction</h4>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-gray" style="flex: 1 1 40%;">Undertime</p>
                <p class="p-10 bg-gray" style="flex: 1 1 60%;">20</p>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-gray" style="flex: 1 1 40%;">Cash Advance (No Interest)</p>
                <p class="p-10 bg-gray" style="flex: 1 1 60%;">2000</p>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-blue text-white" style="flex: 1 1 40%;"><strong>Total</strong></p>
                <p class="p-10 bg-blue text-white" style="flex: 1 1 60%;"><strong>10</strong></p>
             </div>
        </div>

        <div class="payroll-summary-container mt-20">
             <div class="text-white">
                <h4 class="bg-blue p-10">Payroll Summary</h4>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-gray" style="flex: 1 1 40%;">Gross Pay</p>
                <p class="p-10 bg-gray" style="flex: 1 1 60%;">20</p>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-gray" style="flex: 1 1 40%;">Total Deduction</p>
                <p class="p-10 bg-gray" style="flex: 1 1 60%;">2000</p>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-gray" style="flex: 1 1 40%;">Net Pay</p>
                <p class="p-10 bg-gray" style="flex: 1 1 60%;">2000</p>
             </div>
             <div class="d-flex gap-5">
                <p class="p-10 bg-blue text-white" style="flex: 1 1 40%;"><strong>Total</strong></p>
                <p class="p-10 bg-blue text-white" style="flex: 1 1 60%;"><strong>10</strong></p>
             </div>
        </div>
        
        <div class="signatures-container" style="margin-top:70px;">
            <div class="d-flex align-items-center justify-content-between">
                <p class="p-10 text-center" style="flex: 0 0 30%; border-top: 2px solid black;">Prepared By</p>
                <p class="p-10 text-center" style="flex: 0 0 30%; border-top: 2px solid black;">Date Prepared</p>
            </div>
        </div>
       
    </article>
</body>
</html>