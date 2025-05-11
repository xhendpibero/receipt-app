<?php
// api/add_review.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/config.php';

function sendJsonResponse($success, $message = '', $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// Auth check
if (empty($_SESSION['user_id'])) {
    sendJsonResponse(false, 'Not authenticated', 401);
}

// Get and validate input
$data = json_decode(file_get_contents('php://input'), true);

$recipeId = filter_var($data['recipe_id'] ?? null, FILTER_VALIDATE_INT);
$rating = filter_var($data['rating'] ?? null, FILTER_VALIDATE_INT);
$comment = trim($data['comment'] ?? '');

// Validate input
if (!$recipeId || !$rating) {
    sendJsonResponse(false, 'Missing required fields', 400);
}

if ($rating < 1 || $rating > 5) {
    sendJsonResponse(false, 'Invalid rating value', 400);
}

if (strlen($comment) > 1000) { // Adjust max length as needed
    sendJsonResponse(false, 'Review comment too long', 400);
}

try {
    // Check if user already reviewed this recipe
    $stmtCheck = $pdo->prepare("
        SELECT 1 FROM recipe_ratings 
        WHERE recipe_id = ? AND user_id = ?
    ");
    $stmtCheck->execute([$recipeId, $_SESSION['user_id']]);
    
    if ($stmtCheck->fetch()) {
        sendJsonResponse(false, 'You have already reviewed this recipe', 400);
    }

    // Add the review
    $stmt = $pdo->prepare("
        INSERT INTO recipe_ratings (recipe_id, user_id, rating, comment)
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $recipeId,
        $_SESSION['user_id'],
        $rating,
        $comment
    ]);

    sendJsonResponse(true, 'Review added successfully');

} catch (PDOException $e) {
    error_log("Add Review Error: " . $e->getMessage());
    sendJsonResponse(false, 'Failed to add review', 500);
}