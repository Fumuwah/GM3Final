<?php

session_start();

if(isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
} else {
    header("Location: login.php");
    exit();
}
$activePage = "payslip";
include './layout/header.php';
?>

      <div class="d-flex">
         <?php include './layout/sidebar.php'; ?>
          <div class="main-content">
            <div class="container-fluid">
                <h2>Memorandum</h2>
                <div class="d-flex justify-content-between align-items-center flex-column flex-lg-row">
                    <form class="form-inline my-3 col-12 col-lg-10 pl-0">
                        <div class="form-group mb-2 col-8 col-lg-6">
                          <label for="search-user" class="sr-only">Search Memorandum</label>
                          <input type="text" class="form-control w-100" id="search-user" placeholder="Search Memorandum">
                        </div>
                        <button type="submit" class="btn btn-primary mb-2">Search</button>
                      </form>
                    <form action="" class="col-12 col-lg-2">
                    <button type="submit" class="btn btn-block btn-success mb-2">Add Memorandum</button>
                    </form>
                </div>
                
                <div class="row p-2 p-lg-1">
                    <div class="col-12 col-lg-6 my-2">
                        <div class="memo-container d-flex flex-column flex-lg-row">
                           <div class="memo-img" style="background: url(assets/images/login-bg-low.jpg) no-repeat center center; background-size: cover;"></div>
                            <div class="memo-details">
                                <h3 class="memo-title">
                                    Public Holidays in the Philippines
                                </h3>
                                <p class="memo-excerpt">
                                    List of Regular and Special Non-Working Holidays in the Philippines
                                </p>
                                <div class="text-right">
                                    <a href="#">
                                        <img src="assets/images/arrow-right.svg" style="width: 25px;" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>   
                    </div>
                    <div class="col-12 col-lg-6 my-2">
                        <div class="memo-container d-flex flex-column flex-lg-row">
                           <div class="memo-img" style="background: url(assets/images/login-bg-low.jpg) no-repeat center center; background-size: cover;"></div>
                            <div class="memo-details">
                                <h3 class="memo-title">
                                    Public Holidays in the Philippines
                                </h3>
                                <p class="memo-excerpt">
                                    List of Regular and Special Non-Working Holidays in the Philippines
                                </p>
                                <div class="text-right">
                                    <a href="#">
                                        <img src="assets/images/arrow-right.svg" style="width: 25px;" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>   
                    </div>
                    <div class="col-12 col-lg-6 my-2">
                        <div class="memo-container d-flex flex-column flex-lg-row">
                           <div class="memo-img" style="background: url(assets/images/login-bg-low.jpg) no-repeat center center; background-size: cover;"></div>
                            <div class="memo-details">
                                <h3 class="memo-title">
                                    Public Holidays in the Philippines
                                </h3>
                                <p class="memo-excerpt">
                                    List of Regular and Special Non-Working Holidays in the Philippines
                                </p>
                                <div class="text-right">
                                    <a href="#">
                                        <img src="assets/images/arrow-right.svg" style="width: 25px;" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>   
                    </div>
                    
                    <div class="col-12">
                          <div class="d-flex justify-content-end">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
                                  <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                  <li class="page-item"><a class="page-link" href="#">1</a></li>
                                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                                  <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                </ul>
                              </nav>
                          </div>
                    </div>
                </div>
            </div>
          </div>
      </div>
   
      
    <?php include './layout/footer.php'; ?>