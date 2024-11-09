<?php
session_start();

if (!isset($_SESSION['role_name']) || !isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit();
}

$activePage = 'generate_payroll_site';
// $role = $_SESSION['role'];
include './layout/header.php';
?>
<div class="d-flex align-items-stretch">
    <?php include './layout/sidebar.php'; ?>
    <div class="main p-3" style="max-height: calc(100vh - 80px);overflow-y:scroll">
        <div class="container-fluid">
            <h2>Payroll Summary</h2>

            <form class="form-row align-items-center">
                <div class="form-group col-md-3 mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-user" name="search" placeholder="Search User"
                            value="">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary ml-2">Search</button> <!-- Updated margin-left to ml-2 -->
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-2 mb-2">
                    <select name="" id="" class="form-control">
                        <option value="">Select Project</option>
                        <option value="IT Office">IT Office</option>
                        <option value="Accounting Office">Accounting Office</option>
                        <option value="Budget Office">Budget Office</option>
                    </select>
                </div>

                <div class="form-group col-md-2 mb-2">
                    <select name="" id="" class="form-control">
                        <option value="">Role</option>
                        <option value="Site 1">Site 1</option>
                        <option value="Site 2">Site 2</option>
                    </select>
                </div>

                <div class="form-group col-md-2 mb-2">
                    <select name="" id="" class="form-control">
                        <option value="">Payroll Period</option>
                        <option value="January">January</option>
                        <option value="February">February</option>
                        <option value="March">March</option>
                        <option value="April">April</option>
                    </select>
                </div>

                <div class="form-group col-md-1 mb-2">
                    <input type="number" class="form-control" placeholder="From">
                </div>

                <div class="form-group col-md-1 mb-2">
                    <input type="number" class="form-control" placeholder="To">
                </div>

                <div class="form-group col-md-1 mb-2">
                    <button type="submit" class="btn btn-primary ml-2" value="Generate">Generate</button>
                </div>
            </form>

            <div class="row mt-4">
                <div class="col-12 table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th colspan=2 class="text-center">Rate</th>
                                <th colspan=2 class="text-center">Payroll</th>
                            </tr>
                            <tr>
                                <th>Emp #</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Basic Salary</th>
                                <th>Allowance</th>
                                <th>REG</th>
                                <th>OT</th>
                                <th>S.H/V.L</th>
                                <th>Gross</th>
                                <th>Cash Advance</th>
                                <th>Deduction</th>
                                <th>Netpay</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#100</td>
                                <td>Ramon Salvador</td>
                                <td>Mechanical Engineer</td>
                                <td>1000</td>
                                <td>100</td>
                                <td>423</td>
                                <td>424</td>
                                <td>546</td>
                                <td>64564</td>
                                <td>234</td>
                                <td>534</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './layout/footer.php'; ?>