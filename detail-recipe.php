<?php
    // config.php
    require_once 'api/config.php';

    $pageTitle    = 'Dashboard';
    $pageSubtitle = 'Your overview';

    // Get filter values from URL
    $filters = [
        'category' => $_GET['category'] ?? null,
        'rating' => $_GET['rating'] ?? null,
        'author' => $_GET['author'] ?? null
    ];
    
    // Get filtered recipes
    $recipes = getFilteredRecipes($pdo, $filters);
    
    // Get categories and authors for filter dropdowns
    $categories = getCategories($pdo);
    $authors = getAuthors($pdo);
    
    // Current URL without query parameters
    $baseUrl = strtok($_SERVER["REQUEST_URI"], '?');

    include 'components/header.php';
?>

<div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>Detail Recipe</h3>
                            <p class="text-subtitle text-muted">Best recipe you can find!</p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="./">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Recipe</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="row">

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>With controls</h4>
                                    <p>A carousel with previous and next control</p>
                                </div>
                                <div class="card-body">
                                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="assets/images/samples/banana.jpg" class="d-block w-100"
                                                    alt="...">
                                            </div>
                                            <div class="carousel-item">
                                                <img src="assets/images/samples/bg-mountain.jpg" class="d-block w-100"
                                                    alt="...">
                                            </div>
                                        </div>
                                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                                            data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </a>
                                        <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                                            data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Slides only</h4>
                                    <p>A carousel without slide control</p>
                                </div>
                                <div class="card-body">
                                    <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            <div class="carousel-item active">
                                                <img src="assets/images/samples/building.jpg" class="d-block w-100"
                                                    alt="...">
                                            </div>
                                            <div class="carousel-item">
                                                <img src="assets/images/samples/architecture1.jpg" class="d-block w-100"
                                                    alt="...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Ratings</h4>
                                    <p>A carousel with previous and next control</p>
                                </div>
                                <div class="card-body">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            
<?php
    include 'components/footer.php';