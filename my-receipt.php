<?php
    // dashboard.php
    $pageTitle    = 'Dashboard';
    $pageSubtitle = 'Your overview';
    include 'components/header.php';
?>

    <div class="page-heading">
        <div class="page-title">
            <div class="row" style="place-content: center">
                <div class="col-12 col-xl-3 col-lg-4  order-md-1 order-last">
                    <h3>List All Receipt</h3>
                    <p class="text-subtitle text-muted">The best receipt only in this website</p>
                </div>
                <div class="col-12 col-xl-5 col-lg-6 order-md-2 order-first align-content-center">
                    <!-- <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Layout Vertical Navbar
                            </li>
                        </ol>
                    </nav> -->

                    <div class="row" style="justify-self: end;">
                        <div class="col">
                        <div class="btn-group mb-1">
                            <div class="dropdown icon-right">
                                <button class="btn btn-primary dropdown-toggle me-1" type="button"
                                    id="dropdownMenuButtonIconRight" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    Filter Category <i class="bi bi-error-circle ml-50"></i>
                                </button>
                                <div class="dropdown-menu"
                                    aria-labelledby="dropdownMenuButtonIconRight">
                                    <a class="dropdown-item justify-content-between" href="#">Option 1
                                        <i class="bi bi-bar-chart-alt-2 ml-50"></i></a>
                                    <a class="dropdown-item justify-content-between" href="#">Option 2
                                        <i class="bi bi-bell ml-50"></i></a>
                                    <a class="dropdown-item justify-content-between" href="#">Option 3
                                        <i class="bi bi-time ml-50"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group mb-1">
                            <div class="dropdown icon-right">
                                <button class="btn btn-primary dropdown-toggle me-1" type="button"
                                    id="dropdownMenuButtonIconRight" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    Filter Rating <i class="bi bi-error-circle ml-50"></i>
                                </button>
                                <div class="dropdown-menu"
                                    aria-labelledby="dropdownMenuButtonIconRight">
                                    <a class="dropdown-item justify-content-between" href="#">Option 1
                                        <i class="bi bi-bar-chart-alt-2 ml-50"></i></a>
                                    <a class="dropdown-item justify-content-between" href="#">Option 2
                                        <i class="bi bi-bell ml-50"></i></a>
                                    <a class="dropdown-item justify-content-between" href="#">Option 3
                                        <i class="bi bi-time ml-50"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="btn-group mb-1">
                            <div class="dropdown icon-right">
                                <button class="btn btn-danger me-1" type="button">
                                    Reset Filter </i>
                                </button>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="section">
            
            <div class="row" style="place-content: center">
                <div class="col-xl-8 col-lg-10 col-12">

                <?php
                    $test10 = 10;
                    // Render full stars
                    for ($j = 0; $j < 10; $j++) {
                ?>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                        // Dummy value
                                        $rating    = 3.5;
                                        $maxStars  = 5;

                                        // Compute full, half and empty stars
                                        $fullStars  = floor($rating);
                                        $halfStar   = ($rating - $fullStars) >= 0.5 ? 1 : 0;
                                        $emptyStars = $maxStars - $fullStars - $halfStar;
                                    ?>
                                    <div class="col-12 col-sm-12 col-md-4 p-3 text-center">
                                        <img
                                            class="img-fluid mb-2"
                                            src="assets/images/samples/banana.jpg"
                                            alt="Card image cap"
                                        >

                                        <div class="mb-2">
                                            <?php
                                                // Render full stars
                                                for ($i = 0; $i < $fullStars; $i++) {
                                                    echo '<i class="bi bi-star-fill text-warning"></i> ';
                                                }
                                                // Render half star if needed
                                                if ($halfStar) {
                                                    echo '<i class="bi bi-star-half text-warning"></i> ';
                                                }
                                                // Render empty stars
                                                for ($i = 0; $i < $emptyStars; $i++) {
                                                    echo '<i class="bi bi-star text-warning"></i> ';
                                                }
                                            ?>
                                        </div>

                                        <div>
                                            <span class="badge bg-primary">
                                            Rating <?php echo number_format($rating, 1); ?>/<?php echo $maxStars; ?>
                                            </span>
                                        </div>
                                    </div>
                                        
                                    <div class="col-12 col-sm-12 col-md-8 mt-1">
                                        <h4 class="card-title">Receipt Buah apa hayo</h4>
                                        <p class="card-text">
                                            Gummies bonbon apple pie fruitcake icing biscuit apple pie jelly-o sweet
                                            roll. Toffee
                                            sugar plum sugar plum jelly-o jujubes bonbon dessert carrot cake.
                                        </p>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between pb-0">
                                    <span>Uploaded: 10 Mei 2025</span>
                                    <button class="btn btn-light-primary">Read More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>

                </div>
            </div>
        </section>
    </div>

<?php
    include 'components/footer.php';