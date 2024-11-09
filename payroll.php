<?php

session_start();
include 'database.php';

$activePage = 'payroll';

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    echo "Session is not set. Redirecting to login...";
    header("Location: login.php");
    exit();
}

include './layout/header.php';
?>
<style>
    .sidebar,
    .sidebar-section {
        overflow: visible;
    }

    #payrollform input,
    #payrollform select {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 4px;
        transition: box-shadow 0.3s ease;
    }


    #payrollform input:focus,
    #payrollform select:focus {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
        border-color: #28a745;
    }


    #payrollform .card {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
</style>
<div class="d-flex">
    <?php include './layout/sidebar.php'; ?>
    <div class="main-content" style="max-height: calc(100vh - 80px);overflow-y:scroll">
    <div class="container-fluid pl-5">
            <h2>Employee Payroll</h2>
            <form action="submit_payroll.php" id="payrollform" method="post">
                <div class="row flex-wrap">

                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <div class="container-fluid pl-5">
                            <div class="card-body">
                                <h4>Employee Detail</h4>
                                <div class="col-4">Payroll Period:</div>
                                <div class="form-group div form-row align-items-center">
                                    <div class="col-6">
                                        <input type="date" class="form-control" placeholder="From" name="fromDay" id="fromDay">
                                    </div>
                                    <div class="col-6">
                                        <input type="date" class="form-control" placeholder="To" name="toDay" id="toDay">
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="employee_number">Employee No</label>
                                    <input type="text" class="form-control" id="employee_number" name="employee_number">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">Name:</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="hire_date">Date Hired</label>
                                    <input type="date" class="form-control" id="hire_date" name="hire_date">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="position">Position</label>
                                    <select name="position_id" id="position" class="form-control">
                                        <option value="">Select Position</option>
                                        <option value="Owner">Owner</option>
                                        <option value="HR/Admin Manager">HR/Admin Manager</option>
                                        <option value="Foreman">Foreman</option>
                                        <option value="Leadman">Leadman</option>
                                        <option value="Civil Engineer">Civil Engineer</option>
                                        <option value="Mechanical Engineer">Mechanical Engineer</option>
                                        <option value="Laborer">Laborer</option>
                                        <option value="HR Officer">HR Officer</option>
                                        <option value="Field Coordinator">Field Coordinator</option>
                                        <option value="Warehouse Man">Warehouse Man</option>
                                        <option value="Electrician">Electrician</option>
                                        <option value="Mason">Mason</option>
                                        <option value="Surveyor">Surveyor</option>
                                        <option value="Driver">Driver</option>
                                        <option value="Project Engineer">Project Engineer</option>
                                        <option value="Safety Officer">Safety Officer</option>
                                        <option value="Helper">Helper</option>
                                        <option value="Architectural Designer">Architectural Designer</option>
                                        <option value="Admin Specialist">Admin Specialist</option>
                                        <option value="HR Specialist">HR Specialist</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="role_id">Role</label>
                                    <select name="role_id" id="role" class="form-control">
                                        <option value="">Select Role</option>
                                        <option value="Super Admin">Super Admin</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Employee">Employee</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h4>Deductions</h4>
                                <div class="form-group mb-3">
                                    <label for="">Withholding Tax</label>
                                    <input type="text" class="form-control" name="withhold_tax" id="withhold_tax">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">SSS Contribution</label>
                                    <input type="text" class="form-control" name="sss_con" id="sss_con">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Philhealth</label>
                                    <input type="text" class="form-control" name="philhealth_con" id="philhealth_con">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Pag-Ibig</label>
                                    <input type="text" class="form-control" name="pag-ibig_con" id="pag-ibig_con">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Others</label>
                                    <input type="text" class="form-control" name="other_deduc" id="other_deduc">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12 mt-4 mt-md-0">
                        <div class="card">
                            <div class="card-body">
                                <h4>Rate</h4>
                                <div class="form-group div form-row align-items-center">
                                </div>
                                <div class="form-group div form-row align-items-center">
                                    <div class="col-4">Basic Salary:</div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" name="basic_salary" id="basic_salary">
                                    </div>
                                </div>
                                <div class="form-group div form-row align-items-center">
                                    <div class="col-4">Allowance:</div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" name="allowance" id="allowance">
                                    </div>
                                </div>
                                <div class="form-group div form-row align-items-center">
                                    <div class="col-4">Monthly:</div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" name="monthly" id="monthly">
                                    </div>
                                </div>
                                <div class="form-group div form-row align-items-center">
                                    <div class="col-4">Daily:</div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" name="daily" id="daily">
                                    </div>
                                </div>
                                <div class="form-group div form-row align-items-center">
                                    <div class="col-4">Hourly:</div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" name="hourly" id="hourly">
                                    </div>
                                </div>
                                <div class="form-group div form-row align-items-center">
                                    <div class="col-4">Regular Hours:</div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" name="total_hrs" id="total_hrs">
                                    </div>
                                </div>
                                <div class="form-group div form-row align-items-center">
                                    <div class="col-4">Overtime:</div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" name="other_ot" id="other_ot">
                                    </div>
                                </div>
                                <div class="form-group div form-row align-items-center">
                                    <div class="col-4">Total Hours:</div>
                                    <div class="col-8">
                                        <input type="text" class="form-control" name="totalhrs" id="totalhrs">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-12 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h4>Salary</h4>
                                <div class="form-group mb-3">
                                    <label for="">Special Holiday</label>
                                    <input type="text" class="form-control" name="special_holiday" id="special_holiday">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Special Leave</label>
                                    <input type="text" class="form-control" name="special_leave" id="special_leave">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Gross</label>
                                    <input type="text" class="form-control" name="gross" id="gross">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Cash Advance</label>
                                    <input type="text" class="form-control" name="cash_adv" id="cash_adv">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Total Deductions</label>
                                    <input type="text" class="form-control" name="total_deduc" id="total_deduc">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="">Net Pay</label>
                                    <input type="text" class="form-control" name="netpay" id="netpay">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function calculateTotalDeductions() {
            const withholdTax = parseFloat(document.getElementById('withhold_tax').value) || 0;
            const sssCon = parseFloat(document.getElementById('sss_con').value) || 0;
            const philhealthCon = parseFloat(document.getElementById('philhealth_con').value) || 0;
            const pagIbigCon = parseFloat(document.getElementById('pag-ibig_con').value) || 0;
            const otherDeduc = parseFloat(document.getElementById('other_deduc').value) || 0;

            const totalDeductions = withholdTax + sssCon + philhealthCon + pagIbigCon + otherDeduc;

            document.getElementById('total_deduc').value = totalDeductions.toFixed(2);

            calculateNetPay();
        }

        function calculateSalary() {
            const basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
            const allowance = parseFloat(document.getElementById('allowance').value) || 0;
            const roleName = document.getElementById('role').value;

            const monthly = basicSalary + allowance;

            const divisionFactor = (roleName === 'Employee') ? 24 : 26;
            const daily = monthly / divisionFactor;
            const hourly = daily / 8;

            document.getElementById('monthly').value = monthly.toFixed(2);
            document.getElementById('daily').value = daily.toFixed(2);
            document.getElementById('hourly').value = hourly.toFixed(2);

            calculateGross();
        }


        function calculateTotalHours() {
            const totalHrs = parseFloat(document.getElementById('total_hrs').value) || 0;
            const otherOt = parseFloat(document.getElementById('other_ot').value) || 0;

            const combinedHours = totalHrs + otherOt;

            document.getElementById('totalhrs').value = combinedHours.toFixed(2);
        }

        function calculateGross() {
            const totalhrs = parseFloat(document.getElementById('totalhrs').value) || 0;
            const hourly = parseFloat(document.getElementById('hourly').value) || 0;
            const specialHoliday = parseFloat(document.getElementById('special_holiday').value) || 0;
            const specialLeave = parseFloat(document.getElementById('special_leave').value) || 0;

            const gross = (totalhrs * hourly) - specialHoliday - specialLeave;

            document.getElementById('gross').value = gross.toFixed(2);

            calculateNetPay();
        }

        function calculateNetPay() {
            const totalDeductions = parseFloat(document.getElementById('total_deduc').value) || 0;
            const cashAdv = parseFloat(document.getElementById('cash_adv').value) || 0;
            const gross = parseFloat(document.getElementById('gross').value) || 0;

            const netpay = gross - totalDeductions - cashAdv;

            document.getElementById('netpay').value = netpay.toFixed(2);
        }

        document.getElementById('basic_salary').addEventListener('input', calculateSalary);
        document.getElementById('allowance').addEventListener('input', calculateSalary);

        document.getElementById('total_hrs').addEventListener('input', () => {
            calculateTotalHours();
            calculateGross();
        });

        document.getElementById('other_ot').addEventListener('input', () => {
            calculateTotalHours();
            calculateGross();
        });

        document.getElementById('hourly').addEventListener('input', calculateGross);
        document.getElementById('special_holiday').addEventListener('input', calculateGross);
        document.getElementById('special_leave').addEventListener('input', calculateGross);

        document.getElementById('withhold_tax').addEventListener('input', calculateTotalDeductions);
        document.getElementById('sss_con').addEventListener('input', calculateTotalDeductions);
        document.getElementById('philhealth_con').addEventListener('input', calculateTotalDeductions);
        document.getElementById('pag-ibig_con').addEventListener('input', calculateTotalDeductions);
        document.getElementById('other_deduc').addEventListener('input', calculateTotalDeductions);

        document.getElementById('cash_adv').addEventListener('input', calculateNetPay);

        function clearFields() {
            const fields = [
                'name', 'hire_date', 'position', 'role', 'basic_salary', 'monthly', 'daily',
                'hourly', 'total_hrs', 'other_ot', 'salary', 'special_holiday', 'allowance',
                'special_leave', 'gross', 'netpay', 'total_deduc', 'cash_adv', 'totalhrs', 'total_deduc',
                'withhold_tax', 'sss_con', 'philhealth_con', 'pag-ibig_con', 'other_deduc'
            ];

            fields.forEach(field => {
                const element = document.getElementById(field);

                if (element) {
                    if (field === 'name') {
                        element.value = '';
                    } else if (field === 'basic_salary') {
                        element.value = '';
                    } else {
                        element.value = '0';
                    }
                } else {
                    console.warn(`Element with id '${field}' not found.`);
                }
            });
        }

        document.getElementById('employee_number').addEventListener('blur', function() {
            const employeeNumber = this.value.trim();
            const fromDay = document.getElementById('fromDay').value;
            const toDay = document.getElementById('toDay').value;

            if (employeeNumber && fromDay && toDay) {
                fetch(`get_employee.php?employee_number=${employeeNumber}&fromDay=${fromDay}&toDay=${toDay}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            document.getElementById('name').value = data.name || '';
                            document.getElementById('hire_date').value = data.hire_date || '';
                            document.getElementById('position').value = data.position_name || '';
                            document.getElementById('role').value = data.role_name || '';
                            document.getElementById('basic_salary').value = data.basic_salary || '';
                            document.getElementById('allowance').value = data.allowance || '0';
                            document.getElementById('total_hrs').value = data.total_hrs || '0';
                            document.getElementById('other_ot').value = data.other_ot || '0';

                            calculateSalary();
                            calculateTotalHours();
                            calculateGross();
                            calculateNetPay();
                            calculateTotalDeductions();
                        }
                    })
                    .catch(error => console.error('Error fetching employee details:', error));
            } else {
                clearFields();
            }
        });


        var deleteBtn = document.querySelectorAll('.delete-btn');
        var deleteModal = document.querySelector('#delete-modal');
        var closeModal = document.querySelectorAll('.close-modal');

        closeModal.forEach((d) => {
            d.addEventListener('click', function(i) {
                var modalParent = d.parentNode.parentNode.parentNode.parentNode;
                deleteModal.classList.add('fade');
                setTimeout(function() {
                    deleteModal.style.display = 'none';
                }, 400);
            });
        });

        deleteBtn.forEach((d) => {
            d.addEventListener('click', function() {
                deleteModal.classList.remove('fade');
                deleteModal.style.display = 'block';
            });
        });
    </script>


    <?php include './layout/footer.php'; ?>