<?php
// api/delete_recipe.php
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

// --- 2) Get and validate recipe ID ---
$body = file_get_contents('php://input');
$data = json_decode($body, true);

$recipeId = filter_var($data['id'] ?? null, FILTER_VALIDATE_INT);
if (!$recipeId) {
    sendJsonResponse(false, 'Invalid recipe ID', 400);
}

// --- 3) Delete recipe and related data ---
try {
    $pdo->beginTransaction();

    // First verify ownership
    $stmtCheck = $pdo->prepare("
        SELECT 1 FROM recipes 
        WHERE id = ? AND user_id = ?
    ");
    $stmtCheck->execute([$recipeId, $_SESSION['user_id']]);
    if (!$stmtCheck->fetch()) {
        sendJsonResponse(false, 'Recipe not found or access denied', 403);
    }

    // Delete recipe images
    // Note: Due to ON DELETE CASCADE, this isn't strictly necessary,
    // but included for clarity and if you need to handle image cleanup
    $stmtImages = $pdo->prepare("
        DELETE FROM recipe_images 
        WHERE recipe_id = ?
    ");
    $stmtImages->execute([$recipeId]);

    // Delete recipe ratings
    // Note: Also handled by CASCADE, but included for clarity
    $stmtRatings = $pdo->prepare("
        DELETE FROM recipe_ratings 
        WHERE recipe_id = ?
    ");
    $stmtRatings->execute([$recipeId]);

    // Finally, delete the recipe
    $stmtRecipe = $pdo->prepare("
        DELETE FROM recipes 
        WHERE id = ? AND user_id = ?
    ");
    $stmtRecipe->execute([$recipeId, $_SESSION['user_id']]);

    // Check if recipe was actually deleted
    if ($stmtRecipe->rowCount() === 0) {
        throw new Exception('Failed to delete recipe');
    }

    $pdo->commit();
    sendJsonResponse(true, 'Recipe deleted successfully');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Delete Recipe Error: " . $e->getMessage());
    sendJsonResponse(false, 'An error occurred while deleting the recipe', 500);
}