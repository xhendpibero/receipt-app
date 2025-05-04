<?php
// api/add_recipe.php
session_start();
header('Content-Type: application/json');

// Assuming config.php is one directory up from the 'api' folder
require_once __DIR__ . '/config.php'; // Adjust path if necessary

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

// Basic check if decoding worked and basic required fields exist
if (!is_array($data) || !isset($data['name'], $data['description'])) {
    sendJsonResponse(false, 'Invalid or incomplete input data', 400);
}

// --- 3) Get and Validate Input Data ---
$name = trim($data['name']);
$description = trim($data['description']);
// Use ?? null to handle case where category_id is not sent or is empty
$categoryId = isset($data['category_id']) ? trim($data['category_id']) : null;
$images = $data['images'] ?? []; // Should be an array, default to empty

// Server-side validation for name and description length
if (empty($name) || strlen($name) > 255) { // Assuming VARCHAR(255) for name
     sendJsonResponse(false, 'Recipe name is required and must be <= 255 characters', 400);
}
if (empty($description)) {
     sendJsonResponse(false, 'Recipe detail is required', 400);
}

// Validate category_id if provided and not null
if ($categoryId !== null) {
    if (!filter_var($categoryId, FILTER_VALIDATE_INT) || (int)$categoryId <= 0) {
         sendJsonResponse(false, 'Invalid category ID format', 400);
    }
    // Optional: Check if category_id exists in the categories table
    // $stmtCat = $pdo->prepare("SELECT 1 FROM categories WHERE id = ?");
    // $stmtCat->execute([$categoryId]);
    // if (!$stmtCat->fetch()) {
    //     sendJsonResponse(false, 'Category does not exist', 400);
    // }
    // Ensure it's an integer for the SQL binding
    $categoryId = (int)$categoryId;
}


// --- 4) Start Database Transaction ---
// Use transactions to ensure either the recipe and ALL valid images are saved, or none are.
try {
    $pdo->beginTransaction();

    // --- 5) Insert Recipe ---
    $stmt = $pdo->prepare("
      INSERT INTO recipes (user_id, name, description, category_id)
      VALUES (:uid, :name, :desc, :cat)
    ");
    $stmt->execute([
      ':uid'  => $_SESSION['user_id'],
      ':name' => $name,
      ':desc' => $description,
      // PDO handles null correctly for integer columns
      ':cat'  => $categoryId
    ]);
    $recipeId = $pdo->lastInsertId();

    // --- 6) Handle Images (Store Base64) ---
    $imageErrors = []; // Track image processing issues

    // Server-side limits (should match or be stricter than JS)
    $maxImageSize = 1 * 1024 * 1024; // 1 MB
    $maxImageCount = 3;

    if (count($images) > $maxImageCount) {
         // This might happen if JS fails, process only the first $maxImageCount
         $images = array_slice($images, 0, $maxImageCount);
         $imageErrors[] = "More than {$maxImageCount} images provided; only processing the first {$maxImageCount}.";
    }

    foreach ($images as $index => $b64) {
        // Ensure it's a string
        if (!is_string($b64)) {
             $imageErrors[] = "Image #{$index}: Data is not a string.";
             continue;
        }

        // Parse out the data URL parts (data:image/png;base64,...)
        if (!preg_match('/^data:image\/([a-zA-Z0-9]+);base64,/', $b64, $matches)) {
            $imageErrors[] = "Image #{$index}: Invalid Base64 data URL format.";
            continue; // Skip this image if format is wrong
        }

        $mimeType = 'image/' . strtolower($matches[1]); // e.g., 'image/png'
        // Basic check for allowed MIME types
        if (!in_array($mimeType, ['image/png', 'image/jpeg', 'image/jpg'])) {
             $imageErrors[] = "Image #{$index}: Unsupported image type ({$mimeType}). Only PNG, JPG, JPEG allowed.";
             continue;
        }

        // Decode the Base64 string
        $rawBase64 = substr($b64, strpos($b64, ',') + 1);
        $imgData = base64_decode($rawBase64);

        if ($imgData === false) {
            $imageErrors[] = "Image #{$index}: Failed to decode Base64 string.";
            continue;
        }

        // Server-side size check on decoded data
        if (strlen($imgData) > $maxImageSize) {
            $imageErrors[] = "Image #{$index}: Image data size exceeds {$maxImageSize} bytes.";
            continue;
        }

        // Optional: Server-side image validation using getimagesizefromstring
        // This checks if the decoded data is actually a valid image format
        $imageInfo = @getimagesizefromstring($imgData); // Suppress warnings
        if ($imageInfo === false) {
             $imageErrors[] = "Image #{$index}: Invalid image data or corrupt file.";
             continue;
        }
        // Double check MIME type from actual image data if needed
        // if (!in_array($imageInfo['mime'], ['image/png', 'image/jpeg'])) { ... }


        // --- Insert the Base64 string into the database ---
        // !! REQUIRES recipe_images.image_url (or new column) to be TEXT or LONGTEXT !!
        $stmtImg = $pdo->prepare("
          INSERT INTO recipe_images (recipe_id, image_url) -- 'image_url' column name is confusing here, but using it as requested
          VALUES (:rid, :base64data)
        ");
        try {
            $stmtImg->execute([
              ':rid' => $recipeId,
              ':base64data' => $b64 // Store the *original* data URL string
            ]);
        } catch (PDOException $e) {
            // Catch DB errors during image insertion
            $imageErrors[] = "Image #{$index}: Database error during insertion (" . $e->getMessage() . ")";
            // Don't break the loop, try inserting others, but collect error
        }
    }

    // If we reached here without throwing PDO exceptions, commit
    $pdo->commit();

    // --- 7) Return Success (with image errors if any) ---
    // Even if some images failed, if the recipe and at least some images were saved, it's often considered a partial success.
    // You might adjust this logic based on your requirements (e.g., rollback if ZERO images saved).
    $responseMessage = 'Recipe added successfully!';
    if (!empty($imageErrors)) {
        $responseMessage .= ' Some images could not be processed: ' . implode('; ', $imageErrors);
    }

    sendJsonResponse(true, $responseMessage);

} catch (PDOException $e) {
    // --- Handle Transaction Failure ---
    if ($pdo->inTransaction()) {
        $pdo->rollBack(); // Rollback the entire transaction
    }
    // Log the actual database error $e->getMessage() in production!
    sendJsonResponse(false, 'A database error occurred while saving the recipe.', 500);
}

?>