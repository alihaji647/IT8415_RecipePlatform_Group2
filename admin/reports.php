<?php 
require '../config/database.php'; 
if(!isset($_SESSION['user_id']) || getUserRole() != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get popular recipes (last 30 days)
$stmt = $pdo->prepare("
    SELECT 
        r.title, 
        r.recipe_id,
        COUNT(*) as view_count,
        AVG(rr.rating) as avg_rating,
        COUNT(DISTINCT rr.user_id) as total_ratings
    FROM dbproj_recipes r
    LEFT JOIN dbproj_recipe_ratings rr ON r.recipe_id = rr.recipe_id
    WHERE r.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY r.recipe_id
    ORDER BY view_count DESC
    LIMIT 10
");
$stmt->execute();
$popular = $stmt->fetchAll();
?>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">📊 Reports & Analytics</h2>
            
            <!-- Popular Recipes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>🔥 Most Popular Recipes (Last 30 Days)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
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
                                <?php foreach($popular as $recipe): ?>
                                <tr>
                                    <td>
                                        <strong><?=$recipe['title']?></strong><br>
                                        <small>ID: <?=$recipe['recipe_id']?></small>
                                    </td>
                                    <td><span class="badge bg-primary"><?=$recipe['view_count']?></span></td>
                                    <td>
                                        <?php 
                                        $rating = $recipe['avg_rating'] ?? 0;
                                        echo round($rating, 1);
                                        echo str_repeat('⭐', (int)$rating);
                                        ?>
                                    </td>
                                    <td><?=$recipe['total_ratings']?></td>
                                    <td>
                                        <a href="../recipe.php?id=<?=$recipe['recipe_id']?>" 
                                           class="btn btn-sm btn-outline-primary" target="_blank">View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- User Activity Report -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>👥 Top Active Users</h4>
                </div>
                <div class="card-body">
                    <?php
                    $users = $pdo->query("
                        SELECT u.username, u.user_id, 
                               COUNT(r.recipe_id) as recipes_count,
                               COUNT(DISTINCT rr.rating_id) as ratings_count
                        FROM dbproj_users u
                        LEFT JOIN dbproj_recipes r ON u.user_id = r.user_id
                        LEFT JOIN dbproj_recipe_ratings rr ON u.user_id = rr.user_id
                        GROUP BY u.user_id
                        ORDER BY (recipes_count + ratings_count) DESC
                        LIMIT 10
                    ")->fetchAll();
                    ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Recipes</th>
                                    <th>Ratings</th>
                                    <th>Total Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $user): ?>
                                <tr>
                                    <td><a href="manage_users.php?user_id=<?=$user['user_id']?>"><?=$user['username']?></a></td>
                                    <td><?=$user['recipes_count']?></td>
                                    <td><?=$user['ratings_count']?></td>
                                    <td><strong><?=$user['recipes_count'] + $user['ratings_count']?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reports Summary -->
            <div class="card">
                <div class="card-header">
                    <h4>⚠️ User Reports</h4>
                </div>
                <div class="card-body">
                    <?php
                    $reports = $pdo->query("
                        SELECT r.*, u.username as reporter 
                        FROM dbproj_reports r 
                        JOIN dbproj_users u ON r.user_id = u.user_id 
                        ORDER BY r.created_at DESC 
                        LIMIT 5
                    ")->fetchAll();
                    ?>
                    <?php if(empty($reports)): ?>
                        <div class="alert alert-info">No pending reports</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Reporter</th>
                                        <th>Recipe ID</th>
                                        <th>Reason</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($reports as $report): ?>
                                    <tr class="table-warning">
                                        <td><?=$report['reporter']?></td>
                                        <td>#<?=$report['recipe_id']?></td>
                                        <td><?=substr($report['reason'], 0, 50)?>...</td>
                                        <td><?=date('M j', strtotime($report['created_at']))?></td>
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
<?php include '../includes/footer.php'; ?>
