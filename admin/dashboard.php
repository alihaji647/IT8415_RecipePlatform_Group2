<?php 
// Start PHP session to maintain user login state
session_start();
// Include database configuration and connection
require '../config/database.php'; 

// 🔥 ADMIN CHECK - Verify user is logged in and has admin role
if(!isLoggedIn() || getUserRole() !== 'admin') {
    // Redirect non-admin users to login with error message
    header('Location: ../login.php?error=admin');
    exit();
}

// Safe stats function - Safely execute COUNT queries with error handling
function getSafeCount($pdo, $query) {
    try {
        // Execute query and return first column value, default to 0 if empty
        return $pdo->query($query)->fetchColumn() ?: 0;
    } catch (Exception $e) {
        // Return 0 on any database error to prevent crashes
        return 0;
    }
}
?>
<!-- Include common header with navigation, CSS, etc. -->
<?php include '../includes/header.php'; ?>
<!-- Main container using Bootstrap fluid layout -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Welcome alert with security icon and dynamic username -->
            <div class="alert alert-success border-start border-5 border-primary">
                <i class="fas fa-shield-alt me-2"></i>
                Welcome, <strong><?=getUsername()?></strong>! 🛡️ Admin Dashboard
            </div>
            
            <!-- Main dashboard title -->
            <h2 class="mb-4">📊 Dashboard Overview</h2>
            
            <!-- Stats Cards - 4 key metrics in a responsive grid -->
            <div class="row mb-4 g-4">
                <!-- Total Users Card -->
                <div class="col-md-3">
                    <div class="card text-white bg-primary text-center h-100 recipe-card">
                        <div class="card-body">
                            <!-- Display total user count from database -->
                            <h3 class="display-4"><?=getSafeCount($pdo, "SELECT COUNT(*) FROM dbproj_users")?></h3>
                            <p class="mb-0"><i class="fas fa-users"></i> Total Users</p>
                        </div>
                    </div>
                </div>
                <!-- Total Recipes Card -->
                <div class="col-md-3">
                    <div class="card text-white bg-success text-center h-100 recipe-card">
                        <div class="card-body">
                            <!-- Display total recipe count -->
                            <h3 class="display-4"><?=getSafeCount($pdo, "SELECT COUNT(*) FROM dbproj_recipes")?></h3>
                            <p class="mb-0"><i class="fas fa-utensils"></i> Total Recipes</p>
                        </div>
                    </div>
                </div>
                <!-- Top Rated Recipes Card (average rating *10 for display) -->
                <div class="col-md-3">
                    <div class="card text-white bg-warning text-center h-100 recipe-card">
                        <div class="card-body">
                            <h3 class="display-4"><?=getSafeCount($pdo, "SELECT AVG(rating_avg) FROM dbproj_recipes WHERE rating_avg >= 4.5")*10?></h3>
                            <p class="mb-0"><i class="fas fa-star"></i> Top Rated</p>
                        </div>
                    </div>
                </div>
                <!-- Draft Recipes Card -->
                <div class="col-md-3">
                    <div class="card text-white bg-danger text-center h-100 recipe-card">
                        <div class="card-body">
                            <!-- Count recipes with draft status -->
                            <h3 class="display-4"><?=getSafeCount($pdo, "SELECT COUNT(*) FROM dbproj_recipes WHERE status='draft'")?></h3>
                            <p class="mb-0"><i class="fas fa-eye-slash"></i> Drafts</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main content row with recent recipes and quick actions -->
            <div class="row">
                <!-- Recent Recipes Table (70% width on large screens) -->
                <div class="col-lg-8">
                    <div class="card">
                        <!-- Card header with primary background -->
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Recipes</h4>
                        </div>
                        <div class="card-body p-0">
                            <!-- Responsive table wrapper -->
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <!-- Table header with fixed column widths -->
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="80">#</th>
                                            <th>Title</th>
                                            <th>Author</th>
                                            <th>Status</th>
                                            <th>Views</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // 🔥 PERFECT QUERY FOR YOUR TABLE - Fetch recent recipes with user join
                                        try {
                                            $stmt = $pdo->query("
                                                SELECT 
                                                    r.id, 
                                                    r.title, 
                                                    r.description,
                                                    r.status,
                                                    r.views,
                                                    r.rating_avg,
                                                    u.username,
                                                    r.created_at
                                                FROM dbproj_recipes r 
                                                LEFT JOIN dbproj_users u ON r.user_id = u.id 
                                                ORDER BY r.created_at DESC 
                                                LIMIT 10
                                            ");
                                            
                                            // Check if no recipes exist
                                            if($stmt->rowCount() === 0) {
                                        ?>
                                        <!-- Empty state row with call-to-action -->
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fas fa-utensils fa-3x mb-3 opacity-50 d-block"></i>
                                                <h5>No recipes yet</h5>
                                                <a href="../add_recipe.php" class="btn btn-primary">Create First Recipe</a>
                                            </td>
                                        </tr>
                                        <?php } else {
                                            // Loop through recent recipes
                                            while($recipe = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        ?>
                                        <!-- Recipe row with all details -->
                                        <tr class="align-middle">
                                            <td>
                                                <!-- Recipe ID -->
                                                <strong>#<?=$recipe['id']?></strong>
                                            </td>
                                            <td>
                                                <!-- Truncated title with ellipsis -->
                                                <strong><?=htmlspecialchars(substr($recipe['title'], 0, 40))?></strong>
                                                <?php if(strlen($recipe['title']) > 40) echo '<small class="text-muted">...</small>'; ?>
                                            </td>
                                            <td>
                                                <!-- Author badge (handles null usernames) -->
                                                <span class="badge <?=empty($recipe['username']) ? 'bg-secondary' : 'bg-info'?>">
                                                    <?=htmlspecialchars($recipe['username'] ?? 'Unknown')?>
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Dynamic status badge -->
                                                <span class="badge 
                                                    <?= $recipe['status'] === 'published' ? 'bg-success' : 'bg-warning' ?>">
                                                    <?= ucfirst($recipe['status']) ?>
                                                </span>
                                            </td>
                                            <td><!-- Formatted view count -->
                                                <strong><?=number_format($recipe['views'] ?? 0)?></strong></td>
                                            <td>
                                                <!-- Action buttons group -->
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <!-- View recipe in new tab -->
                                                    <a href="../recipe.php?id=<?=$recipe['id']?>" 
                                                       class="btn btn-outline-primary" target="_blank" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <!-- Edit recipe (placeholder link) -->
                                                    <a href="#" class="btn btn-outline-success" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <!-- Delete button with confirmation -->
                                                    <button class="btn btn-outline-danger" onclick="confirmDelete(<?=$recipe['id']?>)" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php 
                                            } // End recipe loop
                                        }
                                        } catch(PDOException $e) {
                                            // Error handling for database issues
                                            echo '<tr><td colspan="6" class="text-center bg-danger text-white py-3">';
                                            echo 'Database Error: ' . $e->getMessage();
                                            echo '</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions Sidebar (30% width on large screens) -->
                <div class="col-lg-4">
                    <!-- Sticky sidebar for easy access -->
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                        </div>
                        <!-- List of admin navigation links -->
                        <div class="list-group list-group-flush">
                            <!-- Manage Users link -->
                            <a href="manage_users.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-users text-primary me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">Manage Users</div>
                                    <small class="text-muted">Edit, ban, promote users</small>
                                </div>
                            </a>
                            <!-- Manage Recipes link -->
                            <a href="manage_recipes.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-utensils text-success me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">Manage Recipes</div>
                                    <small class="text-muted">Approve, delete recipes</small>
                                </div>
                            </a>
                            <!-- Reports link -->
                            <a href="reports.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-flag text-warning me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">View Reports</div>
                                    <small class="text-muted">Handle user reports</small>
                                </div>
                            </a>
                            <!-- View live site link -->
                            <a href="../index.php" class="list-group-item list-group-item-action d-flex align-items-center">
                                <i class="fas fa-globe text-info me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">View Live Site</div>
                                </div>
                            </a>
                            <!-- Logout link -->
                            <a href="../logout.php" class="list-group-item list-group-item-action d-flex align-items-center text-danger">
                                <i class="fas fa-sign-out-alt me-3 fs-4"></i>
                                <div>
                                    <div class="fw-bold">Logout</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for delete confirmation with AJAX -->
<script>
function confirmDelete(id) {
    // Show native browser confirmation dialog
    if(confirm(`Delete recipe #${id}? This cannot be undone.`)) {
        // Send DELETE request and reload page on success
        fetch(`delete_recipe.php?id=${id}`, {method: 'DELETE'})
            .then(() => location.reload());
    }
}
</script>

<!-- Include common footer with scripts, closing tags, etc. -->
<?php include '../includes/footer.php'; ?>