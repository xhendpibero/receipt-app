<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vertical Navbar - Mazer Admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">

    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">
</head>

<body>
    <div id="app">
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href="index.html"><img src="assets/images/logo/logo.png" alt="Logo" srcset="" style="width: 150px;height: 80px"></a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Menu</li>

                        <li class="sidebar-item active ">
                            <a href="index.html" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Home</span>
                            </a>
                        </li>

                        <?php if (!empty($_SESSION['user_id'])) { ?>
                        <li class="sidebar-title">My Recipe</li>
                        <li class="sidebar-item ">
                            <a href="index.html" class='sidebar-link'>
                                <i class="bi bi-journal-richtext"></i>
                                <span>List Recipe</span>
                            </a>
                        </li>
                        <li class="sidebar-item ">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable" class='sidebar-link'>
                                <i class="bi bi-journal-plus"></i>
                                <span>Add Recipe</span>
                            </a>
                        </li>
                        <?php } ?>

                        <?php if (empty($_SESSION['user_id'])) { ?>
                        <li class="sidebar-title">Authentication</li>

                        <li class="sidebar-item  ">
                            <a href="login" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Login</span>
                            </a>
                        </li>
                        <li class="sidebar-item  ">
                            <a href="register" class='sidebar-link'>
                                <i class="bi bi-grid-fill"></i>
                                <span>Register</span>
                            </a>
                        </li>
                        <?php } ?>

                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        <div id="main" class='layout-navbar'>
            <header class='mb-3'>
                <nav class="navbar navbar-expand navbar-light ">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block">
                            <i class="bi bi-justify fs-3"></i>
                        </a>

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            </ul>

                            <?php if (! empty($_SESSION['user_id'])) { ?>
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="user-menu d-flex">
                                            <div class="user-name text-end me-3">
                                                <h6 class="mb-0 text-gray-600">John Ducky</h6>
                                                <p class="mb-0 text-sm text-gray-600">Administrator</p>
                                            </div>
                                            <div class="user-img d-flex align-items-center">
                                                <div class="avatar avatar-md">
                                                    <img src="assets/images/faces/1.jpg">
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <h6 class="dropdown-header">Hello, John!</h6>
                                        </li>
                                        <!-- <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-person me-2"></i> My
                                                Profile</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-gear me-2"></i>
                                                Settings</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="icon-mid bi bi-wallet me-2"></i>
                                                Wallet</a></li>
                                        <li> -->
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="api/logout.php"><i
                                                    class="icon-mid bi bi-box-arrow-left me-2"></i> Logout</a></li>
                                    </ul>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </nav>
            </header>
            <div id="main-content">
                
            <?php if (!empty($_SESSION['user_id'])) { ?>
            <!--scrolling content Modal -->
            <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalScrollableTitle">
                                Add Recipe</h5>
                            <button type="button" class="close" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i data-feather="x"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            

                        <form action="#">
                            <div class="modal-body">
                                <label>Email: </label>
                                <div class="form-group">
                                    <input type="text" placeholder="Email Address"
                                        class="form-control">
                                </div>
                                <label>Password: </label>
                                <div class="form-group">
                                    <input type="password" placeholder="Password"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-secondary"
                                    data-bs-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Close</span>
                                </button>
                                <button type="button" class="btn btn-primary ml-1"
                                    data-bs-dismiss="modal">
                                    <i class="bx bx-check d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">login</span>
                                </button>
                            </div>
                        </form>

                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Example
                                    textarea</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1"
                                    rows="3"></textarea>
                            </div>
                        </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary"
                                data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>
                            <button type="button" class="btn btn-primary ml-1"
                                data-bs-dismiss="modal">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Accept</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
