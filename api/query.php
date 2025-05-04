<?php
// query.php

// Function to get all recipes
function getRecipes($pdo) {
    $sql = "SELECT 
                r.id,
                r.user_id,
                r.category_id,
                r.name,
                r.description,
                r.created_at,
                r.updated_at,
                COUNT(DISTINCT rr.id) AS rating_count,
                COALESCE(AVG(rr.rating), 0) AS avg_rating,
                GROUP_CONCAT(DISTINCT ri.image_url SEPARATOR '|||') AS image_urls,
                u.username AS author,
                c.name AS category_name
            FROM recipes r
            LEFT JOIN recipe_ratings rr ON rr.recipe_id = r.id
            LEFT JOIN recipe_images ri ON ri.recipe_id = r.id
            LEFT JOIN users u ON u.id = r.user_id
            LEFT JOIN categories c ON c.id = r.category_id
            GROUP BY r.id
            ORDER BY r.created_at DESC";
    
    try {
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return [];
    }
}

// Function to get recipes by user ID
function getUserRecipes($pdo, $userId) {
    $sql = "SELECT 
                r.id,
                r.user_id,
                r.category_id,  
                r.name,
                r.description,
                r.created_at,
                r.updated_at,
                COUNT(DISTINCT rr.id) AS rating_count,
                COALESCE(AVG(rr.rating), 0) AS avg_rating,
                GROUP_CONCAT(DISTINCT ri.image_url SEPARATOR '|||') AS image_urls,
                u.username AS author,
                c.name AS category_name
            FROM recipes r
            LEFT JOIN recipe_ratings rr ON rr.recipe_id = r.id
            LEFT JOIN recipe_images ri ON ri.recipe_id = r.id
            LEFT JOIN users u ON u.id = r.user_id
            LEFT JOIN categories c ON c.id = r.category_id
            WHERE r.user_id = :user_id
            GROUP BY r.id
            ORDER BY r.created_at DESC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return [];
    }
}

// Function to get a single recipe by ID
function getRecipe($pdo, $recipeId) {
    $sql = "SELECT 
                r.id,
                r.user_id,
                r.category_id,
                r.name,
                r.description,
                r.created_at,
                r.updated_at,
                COUNT(DISTINCT rr.id) AS rating_count,
                COALESCE(AVG(rr.rating), 0) AS avg_rating,
                GROUP_CONCAT(DISTINCT ri.image_url SEPARATOR '|||') AS image_urls,
                u.username AS author,
                c.name AS category_name
            FROM recipes r
            LEFT JOIN recipe_ratings rr ON rr.recipe_id = r.id
            LEFT JOIN recipe_images ri ON ri.recipe_id = r.id
            LEFT JOIN users u ON u.id = r.user_id
            LEFT JOIN categories c ON c.id = r.category_id
            WHERE r.id = :recipe_id
            GROUP BY r.id";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['recipe_id' => $recipeId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return null;
    }
}

function getFilteredRecipes($pdo, $filters = []) {
    $where = [];
    $params = [];
    
    $sql = "SELECT 
                r.id,
                r.user_id,
                r.category_id,
                r.name,
                r.description,
                r.created_at,
                r.updated_at,
                COUNT(DISTINCT rr.id) AS rating_count,
                COALESCE(AVG(rr.rating), 0) AS avg_rating,
                GROUP_CONCAT(DISTINCT ri.image_url SEPARATOR '|||') AS image_urls,
                u.username AS author,
                c.name AS category_name
            FROM recipes r
            LEFT JOIN recipe_ratings rr ON rr.recipe_id = r.id
            LEFT JOIN recipe_images ri ON ri.recipe_id = r.id
            LEFT JOIN users u ON u.id = r.user_id
            LEFT JOIN categories c ON c.id = r.category_id";

    // Category filter
    if (!empty($filters['category'])) {
        $where[] = "r.category_id = :category_id";
        $params['category_id'] = $filters['category'];
    }

    // Rating filter
    if (!empty($filters['rating'])) {
        $where[] = "COALESCE(AVG(rr.rating), 0) >= :rating";
        $params['rating'] = $filters['rating'];
    }

    // Author filter
    if (!empty($filters['author'])) {
        $where[] = "r.user_id = :author_id";
        $params['author_id'] = $filters['author'];
    }

    // Add WHERE clause if we have conditions
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    // Always group by recipe ID
    $sql .= " GROUP BY r.id";

    // Add ordering
    $sql .= " ORDER BY r.created_at DESC";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Filter Query Error: " . $e->getMessage());
        return [];
    }
}

// Function to get all categories
function getCategories($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Function to get all authors
function getAuthors($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, username FROM users ORDER BY username");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}