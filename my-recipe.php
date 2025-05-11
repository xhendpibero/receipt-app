<?php
    // config.php
    require_once 'api/config.php';

    $pageTitle    = 'Dashboard';
    $pageSubtitle = 'Your overview';

    // Get filter values from URL
    $filters = [
        'category' => $_GET['category'] ?? null,
        'rating' => $_GET['rating'] ?? null,
        'author' => $_SESSION['user_id'] ?? null
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
            <div class="row" style="place-content: center">
                <div class="col-12 col-xl-3 col-lg-4  order-md-1 order-last">
                    <h3>All Recipe</h3>
                    <a class="text-subtitle" href='#' data-bs-toggle='modal' data-bs-target='#recipeAddForm'>Add Recipe</p>
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
                        <div class="col text-end">
        <!-- Category Filter -->
        <div class="btn-group mb-1">
            <div class="dropdown icon-right">
                <button class="btn <?php echo isset($_GET['category']) ? 'btn-success' : 'btn-primary'; ?> dropdown-toggle me-1 d-flex gap-2 align-items-center" 
                        type="button"
                        id="categoryFilter" 
                        data-bs-toggle="dropdown"
                        aria-haspopup="true" 
                        aria-expanded="false">
                    Filter Category <i class="bi bi-filter"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="categoryFilter">
                    <?php foreach ($categories as $category): ?>
                        <a class="dropdown-item <?php echo ($filters['category'] == $category['id']) ? 'active' : ''; ?>" 
                           href="<?php echo $baseUrl . '?' . http_build_query(array_merge($filters, ['category' => $category['id']])); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Rating Filter -->
        <div class="btn-group mb-1">
            <div class="dropdown icon-right">
                <button class="btn <?php echo isset($_GET['rating']) ? 'btn-success' : 'btn-primary'; ?> dropdown-toggle me-1 d-flex gap-2 align-items-center" 
                        type="button"
                        id="ratingFilter" 
                        data-bs-toggle="dropdown"
                        aria-haspopup="true" 
                        aria-expanded="false">
                    Filter Rating <i class="bi bi-star"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="ratingFilter">
                    <?php for($i = 5; $i >= 1; $i--): ?>
                        <a class="dropdown-item <?php echo ($filters['rating'] == $i) ? 'active' : ''; ?>"
                           href="<?php echo $baseUrl . '?' . http_build_query(array_merge($filters, ['rating' => $i])); ?>">
                            <?php echo str_repeat('★', $i) . str_repeat('☆', 5-$i); ?> and up
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        
        <!-- Reset Filter -->
        <div class="btn-group mb-1">
            <div class="dropdown icon-right">
                <a href="<?php echo $baseUrl; ?>" class="btn btn-danger me-1 d-flex gap-2 align-items-center">
                    Reset Filter <i class="bi bi-x-circle"></i>
                </a>
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

                <?php if (empty($recipes)): ?>
                    <div class="col-12 text-center">
                        <p>No recipes found.</p>
                    </div>
                <?php else: ?>
                <?php foreach ($recipes as $recipe): 
                    // Convert image_urls string to array using custom delimiter
                    $images = $recipe['image_urls'] ? explode('|||', $recipe['image_urls']) : [];
                    // Get first image or use placeholder
                    $mainImage = !empty($images) ? $images[0] : 'assets/images/placeholder.jpg';
                    
                    // Calculate star rating
                    $rating = round($recipe['avg_rating']);
                ?>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                        // Dummy value
                                        $rating    = $recipe['avg_rating'];
                                        $maxStars  = 5;

                                        // Compute full, half and empty stars
                                        $fullStars  = floor($rating);
                                        $halfStar   = ($rating - $fullStars) >= 0.5 ? 1 : 0;
                                        $emptyStars = $maxStars - $fullStars - $halfStar;
                                    ?>
                                    <div class="col-12 col-sm-12 col-md-4 p-3 text-center">
                                        <img
                                            class="img-fluid mb-2"
                                            src="<?php echo htmlspecialchars($mainImage); ?>"
                                            alt="<?php echo htmlspecialchars($recipe['name']); ?>"
                                        >

                                        <div class="mt-2">
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
                                        <h4 class="card-title"><?php echo htmlspecialchars($recipe['name']); ?></h4>
                                        <div class="mb-2 text-muted">
                                            <small>Uploaded at: <?php echo strftime("%d %B %Y", strtotime($recipe['updated_at'])); ?></small>
                                        </div>
                                        <p class="card-text"><?php echo displayQuillContent($recipe['description']); ?></p>
                                    </div>
                                </div>
                                <div class="card-footer d-flex justify-content-between pb-0">
                                    <div class="mb-2 text-muted">
                                        <span>By: <?php echo htmlspecialchars($recipe['author']); ?></span>
                                        <?php if ($recipe['category_name']): ?>
                                            <span class="ms-2">Category: <?php echo htmlspecialchars($recipe['category_name']); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($recipe)); ?>)" >Edit</button>
                                        <!-- OR if using simple version -->
                                        <button 
                                            onclick="deleteRecipe(<?php echo htmlspecialchars($recipe['id']); ?>)" 
                                            class="btn btn-danger btn-sm">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                </div>
            </div>
        </section>
    </div>
<?php
    include 'components/modal-edit.php';

    include 'components/footer.php';