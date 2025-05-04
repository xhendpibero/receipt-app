<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Receipt APP</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">

    <link rel="stylesheet" href="assets/vendors/toastify/toastify.css">
    <link rel="stylesheet" href="assets/vendors/quill/quill.bubble.css">
    <link rel="stylesheet" href="assets/vendors/quill/quill.snow.css">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="shortcut icon" href="assets/images/favicon.svg" type="image/x-icon">

    <style>
.quill-content {
    max-width: 800px;
    max-height: 300px; /* Adjust this value as needed */
    margin: 0 auto;
    line-height: 1.6;
    overflow-y: auto;
    position: relative;
    padding: 4px;
    padding-bottom: 40px; /* Extra padding for shadow */
}

/* Custom scrollbar for webkit browsers */
.quill-content::-webkit-scrollbar {
    width: 8px;
}

.quill-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.quill-content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.quill-content::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Bottom shadow effect */
.quill-content::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 40px; /* Height of shadow gradient */
    background: linear-gradient(
        to bottom,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 1) 100%
    );
    pointer-events: none; /* Allows scrolling through the shadow */
}

/* Only show shadow when content is scrollable */
.quill-content.has-overflow::after {
    display: block;
}

.quill-content:not(.has-overflow)::after {
    display: none;
}

.quill-content p {
    margin-bottom: 1em;
}

.quill-content ol,
.quill-content ul {
    padding-left: 2em;
    margin-bottom: 1em;
}

.quill-content li {
    margin-bottom: 0.5em;
}

/* Optional: Add a container with border */
.quill-container {
    border: 0px solid #ddd;
    border-radius: 8px;
    padding: 1px; /* Minimal padding to contain the shadow */
    background: white;
}
</style>
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
                        <?php
                            // Get the base directory dynamically
                            function getBaseDir() {
                                $baseDir = dirname($_SERVER['SCRIPT_NAME']);
                                return rtrim($baseDir, '/') . '/';
                            }

                            // Menu configuration array
                            $baseDir = getBaseDir(); // This will give you '/recipe-app/' or '/' depending on your setup
                            $menu = [
                                'home' => [
                                    'title' => 'Menu',
                                    'items' => [
                                        [
                                            'name' => 'Home',
                                            'link' => $baseDir,
                                            'icon' => 'bi bi-grid-fill',
                                            'path' => 'home'
                                        ]
                                    ]
                                ],
                                'recipe' => [
                                    'title' => 'My Recipe',
                                    'requires_auth' => true,
                                    'items' => [
                                        [
                                            'name' => 'List Recipe',
                                            'link' => $baseDir . 'my-recipe',
                                            'icon' => 'bi bi-journal-richtext',
                                            'path' => 'my-recipe'
                                        ],
                                        [
                                            'name' => 'Add Recipe',
                                            'link' => '#',
                                            'icon' => 'bi bi-journal-plus',
                                            'modal' => 'recipeAddForm',
                                            'path' => 'add-recipe'
                                        ]
                                    ]
                                ]
                            ];

                            function isActive($menuPath) {
                                // Get the current URL path
                                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                                $baseDir = getBaseDir();
                                
                                // Remove base directory from current path
                                $currentPath = str_replace($baseDir, '', $currentPath);
                                $currentPath = trim($currentPath, '/');
                                
                                // If it's the home page
                                if ($menuPath === 'home' && ($currentPath === '' || $currentPath === 'index.php')) {
                                    return 'active';
                                }
                                
                                // For other pages
                                if ($currentPath === $menuPath) {
                                    return 'active';
                                }
                                
                                return '';
                            }

                            // Render menu
                            foreach ($menu as $section) {
                                if (isset($section['requires_auth']) && $section['requires_auth'] && empty($_SESSION['user_id'])) {
                                    continue;
                                }
                                
                                echo "<li class='sidebar-title'>{$section['title']}</li>";
                                
                                foreach ($section['items'] as $item) {
                                    $modalAttr = isset($item['modal']) ? "data-bs-toggle='modal' data-bs-target='#{$item['modal']}'" : '';
                                    ?>
                                    <li class="sidebar-item <?php echo isActive($item['path']); ?>">
                                        <a href="<?php echo $item['link']; ?>" <?php echo $modalAttr; ?> class='sidebar-link'>
                                            <i class="<?php echo $item['icon']; ?>"></i>
                                            <span><?php echo $item['name']; ?></span>
                                        </a>
                                    </li>
                                    <?php
                                }
                            }
                        ?>

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
                                                <h6 class="mb-0 text-gray-600"><?php echo $_SESSION['username']; ?></h6>
                                                <p class="mb-0 text-sm text-gray-600">User</p>
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
                                            <h6 class="dropdown-header">Hello, <?php echo $_SESSION['username']; ?>!</h6>
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
                