
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GM3 Builder Home Page</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
 
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body,html{
            overflow-x: hidden;
        }
        .sidebar-link{
            list-style: none;
            padding:7px 30px;
        }
        .sidebar-link a{
            color: rgb(32, 32, 32);
        }
        .sidebar-container{
            position: relative;
            height: 92vh !important;
            top: 0px;
             padding: 20px 0px;
            min-width: 250px;
            /* height: 92vh; */
            background-color: rgb(250, 250, 250);
            z-index: 1;
            /* margin-left: -250px; */
            transition: .4s all ease-in-out;
        }
        
        .arrow-container{
            position: absolute;
            font-weight: bold;
            font-size: 40px;
            background-color: rgb(26, 26, 26);
            padding:0px 12px;
            line-height:43px;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            left:100%;
            top: 50%;
            transform: translate(-50%,-50%) rotate(0deg);
            
        }
        #arrow{
            transition: .4s all ease-in-out;
        }
        .main-content{
            width: 100%;
            padding-top: 30px;
        }

        .sidebar-link.active-link{
            background: rgb(0,123,255,0.5);
        }
        .modal{
            background-color: rgba(0,0,0,.4);
        }
        .memo-container{
            background-color: rgb(238, 238, 238);
        }
        .memo-container > *{
            flex: 0 0 50%;
        }
        .memo-container .memo-details{
            padding: 30px;
        }
        @media screen and (max-width: 992px){
            .memo-img{
                padding:120px;
            }
            .memo-container > *{
              flex: 0 0 100%;
            }
        }
        .accordion:not(.active){
            display: none;
        }

        @media screen and (min-width: 768px){
            .main-content{
                padding: 30px;
            }
        }
        .hamburger{
            width: 32px;
            display: none;
            cursor: pointer;
        }
        .hamburger > p{
            height: 4px;
            width: 100%;
            background: #bdbdbd;
        }
        .hamburger > p:not(:last-child){
          
            margin: 5px 0px;
        }
        /* .splide{
            height: 100%;
        } */
        @media screen and (max-width: 768px){
            .sidebar-container{
                 margin-left: -250px;
            }
            .arrow-container{
              transform: translate(-50%,-50%) rotate(180deg);
              
          }
          
        }
        @media screen and (max-width: 992px){
            .hamburger{
                display: block;
            }
            .nav-phone{
                width: 100%;
            }
            .navbar-nav{
                display: none;
            }
        }

        #account-icon{
            cursor: pointer;
        }
        #logout-dropdown{
            display: none;
        }
        .header-icons{
            width: 20px;
            cursor: pointer;
        }
        #payroll-dropdown{
            height: 0px;
            overflow: hidden;
            transition: .2s all ease-in-out;
        }

        #payroll-dropdown.active{
            height: auto;
        }
    </style>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <style>
        .material-symbols-outlined {
        font-variation-settings:
        'FILL' 0,
        'wght' 400,
        'GRAD' 0,
        'opsz' 20
        }
        </style>
    </head>
<body>
    <nav class="navbar navbar-light bg-light flex-column flex-lg-row align-items-start">
        <div class="d-flex align-items-center nav-phone justify-content-between">
            <a class="navbar-brand font-weight-bold bg-blue" href="index.php" >
                <img src="assets/images/gm3-logo-small.png" width="30" height="30" class="d-inline-block align-top" alt="">
                GM3 Builders
              </a>
        </div>
        <ul class="navbar-nav flex-row flex-column flex-lg-row">
            <li class="nav-item active mx-3 d-flex align-items-center">
                <img src="assets/images/home.png" class="header-icons mr-2" href="index.php"> <a class="nav-link" href="homev2.php">Home<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item mx-3 d-flex align-items-center">
            <img src="assets/images/about.png" class="header-icons mr-1"> <a class="nav-link" href="about.php">About</a>
            </li>
            <li class="nav-item mx-3" id="notification-icon">
                <a class="nav-link" href="#"><img src="assets/images/notification.png" class="header-icons" alt=""></a>
            </li>
            <li class="nav-item mx-3">
                <img src="assets/images/account.png" alt="" style="width:39px;" id="account-icon">
            </li>
            <li class="pos-relative" id="logout-dropdown">
                <div class="custom-dropdown-right" aria-labelledby="navbarDropdownMenuLink">
                    <a class="dropdown-item" href="logout.php">Logout</a>
                  </div>
            </li>
            <li class="pos-relative" id="notification-dropdown">
                <div class="custom-dropdown-right" aria-labelledby="navbarDropdownMenuLink">
                    <div class="d-flex p-3 border-bottom dropdown-list">
                        <div class="pr-3"  style="flex:0 0 91%;">
                           <p class="font-weight-bold p-0 m-0">Christian Jake Francisco requests leave approval ayo</p>
                           <p class="p-0 m-0">Sept 29, 8:44 AM</p>
                        </div>
                        <div style="flex:0 0 30px;">
                            <img src="assets/images/green-check.svg" style="width:30px; cursor:pointer;" alt="">
                        </div>
                    </div>
                    <div class="d-flex p-3 border-bottom dropdown-list">
                        <div class="pr-3"  style="flex:0 0 91%;">
                           <p class="font-weight-bold p-0 m-0">Christian Jake Francisco requests leave approval</p>
                           <p class="p-0 m-0">Sept 29, 8:44 AM</p>
                        </div>
                        <div style="flex:0 0 30px;">
                            <img src="assets/images/red-cross.svg" style="width:30px; cursor:pointer;" alt="">
                        </div>
                    </div>

                  </div>
            </li>
         
        </ul>
      </nav>