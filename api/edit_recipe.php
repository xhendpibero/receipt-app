<?php
// api/edit_recipe.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

// --- Helper function for JSON response ---
function sendJsonResponse($success, $message = '', $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// --- 1) Auth check ---
if (empty($_SESSION['user_id'])) {
    sendJsonResponse(false, 'Not authenticated', 401);
}

// --- 2) Decode JSON payload ---
$body = file_get_contents('php://input');
$data = json_decode($body, true);

// Basic check if decoding worked and required fields exist
if (!is_array($data) || !isset($data['id'], $data['name'], $data['description'])) {
    sendJsonResponse(false, 'Invalid or incomplete input data', 400);
}

// --- 3) Get and Validate Input Data ---
$recipeId = filter_var($data['id'], FILTER_VALIDATE_INT);
$name = trim($data['name']);
$description = trim($data['description']);
$categoryId = isset($data['category_id']) ? trim($data['category_id']) : null;
$images = $data['images'] ?? []; // Should be an array, default to empty

// Validate recipe ID
if (!$recipeId || $recipeId <= 0) {
    sendJsonResponse(false, 'Invalid recipe ID', 400);
}

// Server-side validation
if (empty($name) || strlen($name) > 255) {
    sendJsonResponse(false, 'Recipe name is required and must be <= 255 characters', 400);
}
if (empty($description)) {
    sendJsonResponse(false, 'Recipe detail is required', 400);
}

// Validate category_id if provided
if ($categoryId !== null) {
    if (!filter_var($categoryId, FILTER_VALIDATE_INT) || (int)$categoryId <= 0) {
        sendJsonResponse(false, 'Invalid category ID format', 400);
    }
    $categoryId = (int)$categoryId;
}

// --- 4) Start Database Transaction ---
try {
    $pdo->beginTransaction();

    // --- 4.1) Verify ownership ---
    $stmtCheck = $pdo->prepare("
        SELECT 1 FROM recipes 
        WHERE id = ? AND user_id = ?
    ");
    $stmtCheck->execute([$recipeId, $_SESSION['user_id']]);
    if (!$stmtCheck->fetch()) {
        sendJsonResponse(false, 'Recipe not found or access denied', 403);
    }

    // --- 5) Update Recipe ---
    $stmt = $pdo->prepare("
        UPDATE recipes 
        SET name = :name,
            description = :desc,
            category_id = :cat,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :rid AND user_id = :uid
    ");
    
    $stmt->execute([
        ':rid'  => $recipeId,
        ':uid'  => $_SESSION['user_id'],
        ':name' => $name,
        ':desc' => $description,
        ':cat'  => $categoryId
    ]);

    // --- 6) Handle Images ---
    $imageErrors = [];
    $maxImageSize = 1 * 1024 * 1024; // 1 MB
    $maxImageCount = 3;

    // First, delete existing images if new images are provided
    if (is_array($images)) {
        $stmtDelete = $pdo->prepare("DELETE FROM recipe_images WHERE recipe_id = ?");
        $stmtDelete->execute([$recipeId]);

        if (count($images) > $maxImageCount) {
            $images = array_slice($images, 0, $maxImageCount);
            $imageErrors[] = "More than {$maxImageCount} images provided; only processing the first {$maxImageCount}.";
        }

        foreach ($images as $index => $b64) {
            // Skip if it's already a URL (existing image)
            if (filter_var($b64, FILTER_VALIDATE_URL)) {
                continue;
            }

            // Ensure it's a string
            if (!is_string($b64)) {
                $imageErrors[] = "Image #{$index}: Data is not a string.";
                continue;
            }

            // Parse data URL
            if (!preg_match('/^data:image\/([a-zA-Z0-9]+);base64,/', $b64, $matches)) {
                $imageErrors[] = "Image #{$index}: Invalid Base64 data URL format.";
                continue;
            }

            $mimeType = 'image/' . strtolower($matches[1]);
            if (!in_array($mimeType, ['image/png', 'image/jpeg', 'image/jpg'])) {
                $imageErrors[] = "Image #{$index}: Unsupported image type ({$mimeType}).";
                continue;
            }

            // Decode Base64
            $rawBase64 = substr($b64, strpos($b64, ',') + 1);
            $imgData = base64_decode($rawBase64);

            if ($imgData === false) {
                $imageErrors[] = "Image #{$index}: Failed to decode Base64 string.";
                continue;
            }

            // Size check
            if (strlen($imgData) > $maxImageSize) {
                $imageErrors[] = "Image #{$index}: Image size exceeds limit.";
                continue;
            }

            // Validate image data
            $imageInfo = @getimagesizefromstring($imgData);
            if ($imageInfo === false) {
                $imageErrors[] = "Image #{$index}: Invalid image data.";
                continue;
            }

            // Insert image
            $stmtImg = $pdo->prepare("
                INSERT INTO recipe_images (recipe_id, image_url)
                VALUES (:rid, :base64data)
            ");
            
            try {
                $stmtImg->execute([
                    ':rid' => $recipeId,
                    ':base64data' => $b64
                ]);
            } catch (PDOException $e) {
                $imageErrors[] = "Image #{$index}: Database error during insertion.";
            }
        }
    }

    // Commit transaction
    $pdo->commit();

    // --- 7) Return Success Response ---
    $responseMessage = 'Recipe updated successfully!';
    if (!empty($imageErrors)) {
        $responseMessage .= ' Some images could not be processed: ' . implode('; ', $imageErrors);
    }

    sendJsonResponse(true, $responseMessage);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log the error in production
    error_log("Edit Recipe Error: " . $e->getMessage());
    sendJsonResponse(false, 'A database error occurred while updating the recipe.', 500);
}