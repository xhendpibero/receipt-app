<?php
    // detail_recipe.php or recipe_detail.php
    require_once 'api/config.php';

    // Get recipe ID from URL parameter
    $recipeId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

    if (!$recipeId) {
        header('Location: home');
        exit;
    }

    // Fetch recipe details with author, category, ratings, and images
    try {
        $stmt = $pdo->prepare("
            SELECT 
                r.id,
                r.user_id,
                r.category_id,
                r.name,
                r.description,
                r.created_at,
                r.updated_at,
                u.username as author_name,
                c.name as category_name,
                COUNT(DISTINCT rr.id) as rating_count,
                COALESCE(AVG(rr.rating), 0) as avg_rating,
                GROUP_CONCAT(DISTINCT ri.image_url SEPARATOR '|||') as image_urls
            FROM recipes r
            LEFT JOIN users u ON u.id = r.user_id
            LEFT JOIN categories c ON c.id = r.category_id
            LEFT JOIN recipe_ratings rr ON rr.recipe_id = r.id
            LEFT JOIN recipe_images ri ON ri.recipe_id = r.id
            WHERE r.id = ?
            GROUP BY r.id
        ");
        
        $stmt->execute([$recipeId]);
        $recipe = $stmt->fetch();

        if (!$recipe) {
            header('Location: home');
            exit;
        }

        // Get recipe ratings with comments
        $stmtRatings = $pdo->prepare("
            SELECT 
                rr.id,
                rr.rating,
                rr.comment,
                rr.created_at,
                rr.user_id,
                u.username as reviewer_name
            FROM recipe_ratings rr
            JOIN users u ON u.id = rr.user_id
            WHERE rr.recipe_id = ?
            ORDER BY rr.created_at DESC
        ");
        
        $stmtRatings->execute([$recipeId]);
        $ratings = $stmtRatings->fetchAll();

    } catch (PDOException $e) {
        // Log error and redirect
        error_log("Recipe Detail Error: " . $e->getMessage());
        header('Location: home');
        exit;
    }

    // Helper function to format date
    function formatDate($date) {
        return date('d M Y', strtotime($date));
    }

    include 'components/header.php';
?>

<div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3><?php echo htmlspecialchars($recipe['name']); ?></h3>
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

                        <div class="col-md-5">
                            <div class="card text-center">
                                
                                <?php
                                    // Dummy value
                                    $rating    = $recipe['avg_rating'];
                                    $maxStars  = 5;

                                    // Compute full, half and empty stars
                                    $fullStars  = floor($rating);
                                    $halfStar   = ($rating - $fullStars) >= 0.5 ? 1 : 0;
                                    $emptyStars = $maxStars - $fullStars - $halfStar;
                                ?>

                                <div class="card-body">
                                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner">

                                            <!-- Image Carousel -->
                                            <?php if ($recipe['image_urls']): 
                                                $images = explode('|||', $recipe['image_urls']);
                                            ?>
                                                <?php foreach ($images as $key => $image): ?>
                                                    <div class="carousel-item <?php echo $key === 0 ? 'active' : ''; ?>">
                                                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Recipe Image" class="w-100">
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
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
                                    <br>
                                    <small class="text-muted">
                                        Posted on <?php echo formatDate($recipe['created_at']); ?>
                                    </small>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Detail Recipe</h4>
                                    <div>
                                        <small class="text-muted">Posted by: 
                                            <strong><?php echo htmlspecialchars($recipe['author_name']); ?></strong>
                                        </small>
                                        <br>
                                        <small class="text-muted">Category: 
                                            <strong><?php echo htmlspecialchars($recipe['category_name']); ?></strong>
                                        </small>
                                    </div>
                                </div>
                                <div class="card-body">
                                    
                                    <!-- Recipe Content -->
                                    <div class="recipe-content mb-4">
                                        <?php echo $recipe['description']; ?>
                                    </div>

                                    <!-- Action Buttons -->
                                    <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $recipe['user_id']): ?>
                                    <div class="mb-2">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($recipe)); ?>)" 
                                                class="btn btn-primary">
                                            <i class="bi bi-pencil"></i> Edit Recipe
                                        </button>
                                        <button onclick="deleteRecipe(<?php echo $recipe['id']; ?>)" 
                                                class="btn btn-danger">
                                            <i class="bi bi-trash"></i> Delete Recipe
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Comments & Ratings</h4>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($ratings)): ?>
                                        <!-- No comment -->
                                        <div class="alert alert-light-primary color-primary">
                                            <i class="bi bi-exclamation-circle"></i> 
                                            No comments yet. Be the first to comment!
                                        </div>
                                    <?php else: ?>
                                        <!-- List comment and rating users -->
                                        <div class="comment-list">
                                            <?php foreach ($ratings as $rating): ?>
                                                <div class="comment-item d-flex mb-4">
                                                    <!-- User Avatar -->
                                                    <div class="avatar avatar-lg me-3">
                                                        <img src="assets/images/faces/1.jpg" alt="User Avatar">
                                                    </div>
                                                    
                                                    <!-- Comment Content -->
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0"><?php echo htmlspecialchars($rating['reviewer_name']); ?></h6>
                                                            <small class="text-muted">
                                                                <?php echo formatDate($rating['created_at']); ?>
                                                            </small>
                                                        </div>
                                                        
                                                        <!-- Star Rating Display -->
                                                        <div class="rating my-2">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <i class="bi bi-star<?php echo $i <= $rating['rating'] ? '-fill' : ''; ?> 
                                                                text-warning"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                        
                                                        <!-- Comment Text -->
                                                        <p class="comment-text">
                                                            <?php echo nl2br(htmlspecialchars($rating['comment'])); ?>
                                                        </p>

                                                        <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] === $rating['user_id']): ?>
                                                            <!-- Delete Comment Button (if user owns the comment) -->
                                                            <button onclick="deleteComment(<?php echo $rating['id']; ?>)" 
                                                                    class="btn btn-sm btn-light-danger">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>

                                            <?php if (count($ratings) > 5): ?>
                                                <!-- Load More Button -->
                                                <button id="loadMoreComments" class="btn btn-light-primary btn-block">
                                                    Load More Comments
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($_SESSION['user_id']) && $recipe['user_id'] !== $_SESSION['user_id']): ?>
                                        <!-- Form comment if already login -->
                                        <div class="comment-form mt-4">
                                            <h6>Add Your Comment</h6>
                                            <form id="commentForm" class="form">
                                                <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                                                
                                                <!-- Star Rating Input -->
                                                <div class="form-group mb-3">
                                                    <label>Your Rating</label>
                                                    <div class="star-rating">
                                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                                            <input type="radio" id="star<?php echo $i; ?>" 
                                                                name="rating" value="<?php echo $i; ?>">
                                                            <label for="star<?php echo $i; ?>" title="<?php echo $i; ?> stars">
                                                                <i class="bi bi-star-fill"></i>
                                                            </label>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                
                                                <!-- Comment Text Input -->
                                                <div class="form-group mb-3">
                                                    <label for="comment">Your Comment</label>
                                                    <textarea class="form-control" id="comment" 
                                                            name="comment" rows="4" 
                                                            placeholder="Write your comment here..."></textarea>
                                                </div>
                                                
                                                <!-- Submit Button -->
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-send"></i> Submit Comment
                                                </button>
                                            </form>
                                        </div>
                                    <?php elseif (empty($_SESSION['user_id']) || $recipe['user_id'] !== $_SESSION['user_id']): ?>
                                        <!-- If not login show login button -->
                                        <div class="text-center mt-4">
                                            <p>Please login to leave a comment</p>
                                            <a href="./login" class="btn btn-primary">
                                                <i class="bi bi-box-arrow-in-right"></i> Login to Comment
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Add these styles to your CSS -->
                        <style>
                        .comment-item {
                            border-bottom: 1px solid #eee;
                            padding-bottom: 1rem;
                        }

                        .comment-item:last-child {
                            border-bottom: none;
                        }

                        .star-rating {
                            display: flex;
                            flex-direction: row-reverse;
                            justify-content: flex-end;
                        }

                        .star-rating input {
                            display: none;
                        }

                        .star-rating label {
                            cursor: pointer;
                            font-size: 1.5rem;
                            color: #ddd;
                            padding: 0 0.1em;
                        }

                        .star-rating label:hover,
                        .star-rating label:hover ~ label,
                        .star-rating input:checked ~ label {
                            color: #ffd700;
                        }

                        .comment-text {
                            white-space: pre-line;
                            margin: 0.5rem 0;
                        }

                        .avatar img {
                            width: 48px;
                            height: 48px;
                            border-radius: 50%;
                            object-fit: cover;
                        }
                        </style>

                        <!-- Add this JavaScript -->
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const commentForm = document.getElementById('commentForm');
                            if (commentForm) {
                                commentForm.addEventListener('submit', async (e) => {
                                    e.preventDefault();

                                    // Get form data
                                    const formData = new FormData(commentForm);
                                    const payload = {
                                        recipe_id: formData.get('recipe_id'),
                                        rating: formData.get('rating'),
                                        comment: formData.get('comment')
                                    };

                                    // Validate input
                                    if (!payload.rating) {
                                        Toastify({
                                            text: "Please select a rating",
                                            duration: 3000,
                                            style: { background: '#ff5f6d' }
                                        }).showToast();
                                        return;
                                    }

                                    if (!payload.comment.trim()) {
                                        Toastify({
                                            text: "Please write a comment",
                                            duration: 3000,
                                            style: { background: '#ff5f6d' }
                                        }).showToast();
                                        return;
                                    }

                                    try {
                                        const response = await fetch('api/add_review.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify(payload)
                                        });

                                        const data = await response.json();

                                        if (data.success) {
                                            Toastify({
                                                text: "Comment added successfully!",
                                                duration: 3000,
                                                style: { background: '#00b09b' }
                                            }).showToast();

                                            // Reload page to show new comment
                                            setTimeout(() => window.location.reload(), 1000);
                                        } else {
                                            throw new Error(data.message);
                                        }
                                    } catch (error) {
                                        Toastify({
                                            text: error.message || "Error adding comment",
                                            duration: 3000,
                                            style: { background: '#ff5f6d' }
                                        }).showToast();
                                    }
                                });
                            }

                            // Delete comment function
                            window.deleteComment = async (commentId) => {
                                if (!confirm('Are you sure you want to delete this comment?')) {
                                    return;
                                }

                                try {
                                    const response = await fetch('api/delete_review.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({ id: commentId })
                                    });

                                    const data = await response.json();

                                    if (data.success) {
                                        Toastify({
                                            text: "Comment deleted successfully!",
                                            duration: 3000,
                                            style: { background: '#00b09b' }
                                        }).showToast();

                                        // Reload page to update comments
                                        setTimeout(() => window.location.reload(), 1000);
                                    } else {
                                        throw new Error(data.message);
                                    }
                                } catch (error) {
                                    Toastify({
                                        text: error.message || "Error deleting comment",
                                        duration: 3000,
                                        style: { background: '#ff5f6d' }
                                    }).showToast();
                                }
                            };

                            // Load more comments functionality
                            const loadMoreBtn = document.getElementById('loadMoreComments');
                            if (loadMoreBtn) {
                                let page = 1;
                                loadMoreBtn.addEventListener('click', async () => {
                                    try {
                                        const response = await fetch(`api/get_comments.php?recipe_id=${recipeId}&page=${++page}`);
                                        const data = await response.json();

                                        if (data.success && data.comments.length > 0) {
                                            // Append new comments to the list
                                            const commentList = document.querySelector('.comment-list');
                                            data.comments.forEach(comment => {
                                                // Add comment HTML
                                            });

                                            if (data.comments.length < 5) {
                                                loadMoreBtn.style.display = 'none';
                                            }
                                        } else {
                                            loadMoreBtn.style.display = 'none';
                                        }
                                    } catch (error) {
                                        console.error('Error loading more comments:', error);
                                    }
                                });
                            }
                        });
                        </script>
                    </div>
                </section>

            </div>

            
<?php
    include 'components/modal-edit.php';

    include 'components/footer.php';