<?php 
// Start session for admin authentication
session_start();
// Include database connection
require '../config/database.php'; 
// Include utility functions
require '../includes/functions.php';

// Admin access check using session and role function
if(!isset($_SESSION['user_id']) || getUserRole() != 'admin') {
    // Redirect non-admins to login
    header('Location: ../login.php');
    exit();
}

// Popular Recipes (Fixed SQL) - Last 30 days most viewed recipes with ratings
$stmt = $pdo->prepare("
    SELECT r.title, r.id as recipe_id, COUNT(*) as view_count,
           AVG(rr.rating) as avg_rating, COUNT(DISTINCT rr.user_id) as total_ratings
    FROM dbproj_recipes r
    LEFT JOIN dbproj_recipe_ratings rr ON r.id = rr.recipe_id
    WHERE r.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY r.id
    ORDER BY view_count DESC LIMIT 10
");
// Execute prepared statement (no parameters needed)
$stmt->execute();
// Fetch all popular recipes as array
$popular = $stmt->fetchAll();
?>

<!-- Include header with navigation and styles -->
<?php include '../includes/header.php'; ?>
<!-- Full-width container -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Main reports title -->
            <h2 class="mb-4">📊 Reports & Analytics</h2>
            
            <!-- Popular Recipes Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>🔥 Most Popular Recipes (Last 30 Days)</h4>
                </div>
                <div class="card-body">
                    <!-- Responsive table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <!-- Table header -->
                            <thead class="table-dark">
                                <tr>
                                    <th>Recipe</th>
                                    <th>Views</th>
                                    <th>Avg Rating</th>
                                    <th>Total Ratings</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Empty state check -->
                                <?php if(empty($popular)): ?>
                                    <tr><td colspan="5" class="text-center text-muted">No recipes found in last 30 days</td></tr>
                                <?php else: ?>
                                    <!-- Loop through popular recipes -->
                                    <?php foreach($popular as $recipe): ?>
                                    <tr>
                                        <!-- Recipe title with ID -->
                                        <td>
                                            <strong><?=htmlspecialchars($recipe['title'])?></strong><br>
                                            <small class="text-muted">ID: <?=$recipe['recipe_id']?></small>
                                        </td>
                                        <!-- View count badge -->
                                        <td><span class="badge bg-primary"><?=$recipe['view_count']?></span></td>
                                        <!-- Average rating with stars -->
                                        <td>
                                            <?php $rating = $recipe['avg_rating'] ?? 0; ?>
                                            <?=round($rating, 1)?><?=str_repeat('⭐', (int)$rating)?>
                                        </td>
                                        <!-- Total unique ratings -->
                                        <td><?=$recipe['total_ratings']?></td>
                                        <!-- View recipe link -->
                                        <td>
                                            <a href="../recipe.php?id=<?=$recipe['recipe_id']?>" 
                                               class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Active Users Card (FIXED - No more SQL error!) -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>👥 Top Active Users</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Query top users by combined recipes + ratings activity
                    $users = $pdo->query("
                        SELECT u.username, u.id as user_id, 
                               COUNT(r.id) as recipes_count,
                               COUNT(DISTINCT rr.rating_id) as ratings_count
                        FROM dbproj_users u
                        LEFT JOIN dbproj_recipes r ON u.id = r.user_id
                        LEFT JOIN dbproj_recipe_ratings rr ON u.id = rr.user_id
                        GROUP BY u.id
                        ORDER BY (COUNT(r.id) + COUNT(DISTINCT rr.rating_id)) DESC
                        LIMIT 10
                    ")->fetchAll();
                    ?>
                    <!-- Responsive table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <!-- Table header -->
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Recipes</th>
                                    <th>Ratings</th>
                                    <th>Total Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Empty state -->
                                <?php if(empty($users)): ?>
                                    <tr><td colspan="4" class="text-center text-muted">No active users found</td></tr>
                                <?php else: ?>
                                    <!-- Loop through active users -->
                                    <?php foreach($users as $user): ?>
                                    <tr>
                                        <!-- Username link to manage user -->
                                        <td><a href="manage_users.php?user_id=<?=$user['user_id']?>" class="fw-bold"><?=$user['username']?></a></td>
                                        <!-- Recipes count badge -->
                                        <td><span class="badge bg-success"><?=$user['recipes_count']?></span></td>
                                        <!-- Ratings count badge -->
                                        <td><span class="badge bg-info"><?=$user['ratings_count']?></span></td>
                                        <!-- Total activity score -->
                                        <td><strong class="text-primary"><?=$user['recipes_count'] + $user['ratings_count']?></strong></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- User Reports Card -->
            <div class="card">
                <div class="card-header">
                    <h4>⚠️ User Reports</h4>
                </div>
                <div class="card-body">
                    <?php
                    // Fetch 5 most recent reports with reporter info
                    $reports = $pdo->query("
                        SELECT r.*, u.username as reporter 
                        FROM dbproj_reports r 
                        JOIN dbproj_users u ON r.user_id = u.user_id 
                        ORDER BY r.created_at DESC 
                        LIMIT 5
                    ")->fetchAll();
                    ?>
                    <!-- No reports message -->
                    <?php if(empty($reports)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No pending reports
                        </div>
                    <?php else: ?>
                        <!-- Reports table -->
                        <div class="table-responsive">
                            <table class="table table-sm table-warning">
                                <thead>
                                    <tr>
                                        <th>Reporter</th>
                                        <th>Recipe ID</th>
                                        <th>Reason</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Loop through recent reports -->
                                    <?php foreach($reports as $report): ?>
                                    <tr>
                                        <!-- Reporter username -->
                                        <td><strong><?=htmlspecialchars($report['reporter'])?></strong></td>
                                        <!-- Reported recipe ID -->
                                        <td><span class="badge bg-danger">#<?=$report['recipe_id'] ?? $report['id'] ?? 'N/A'?></span></td>
                                        <!-- Truncated report reason -->
                                        <td><?=htmlspecialchars(substr($report['reason'] ?? '', 0, 50))?>...</td>
                                        <!-- Formatted report date/time -->
                                        <td><small class="text-muted"><?=date('M j, H:i', strtotime($report['created_at']))?></small></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Include footer with scripts -->
<?php include '../includes/footer.php'; ?>