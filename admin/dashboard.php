<?php
/**
 * Admin Dashboard
 * View and manage pre-hospital care forms
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Require admin access
require_admin();

// Get statistics
$total_forms_stmt = db_query("SELECT COUNT(*) as count FROM prehospital_forms");
$total_forms = $total_forms_stmt->fetch()['count'];

$today_forms_stmt = db_query("SELECT COUNT(*) as count FROM prehospital_forms WHERE DATE(created_at) = CURDATE()");
$today_forms = $today_forms_stmt->fetch()['count'];

$total_users_stmt = db_query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
$total_users = $total_users_stmt->fetch()['count'];

// Get recent forms
$recent_forms_stmt = db_query("SELECT * FROM form_summary ORDER BY created_at DESC LIMIT 10");
$recent_forms = $recent_forms_stmt->fetchAll();

$current_user = get_current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pre-Hospital Care System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #004d99;
        }
        
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
        }
        
        .stat-icon.blue { background: linear-gradient(135deg, #0066cc, #004d99); }
        .stat-icon.green { background: linear-gradient(135deg, #28a745, #20c997); }
        .stat-icon.orange { background: linear-gradient(135deg, #fd7e14, #ffc107); }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin: 0.5rem 0 0 0;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }
        
        .table-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .table-card h5 {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 1.25rem;
        }
        
        .badge-status {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .btn-action {
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand">
                <i class="bi bi-speedometer2"></i> Admin Dashboard
            </span>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">
                    <i class="bi bi-person-circle"></i> <?php echo e($current_user['full_name']); ?>
                </span>
                <a href="../public/TONYANG.php" class="btn btn-light btn-sm">
                    <i class="bi bi-file-medical"></i> New Form
                </a>
                <a href="../public/logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid py-4">
        <?php show_flash(); ?>
        
        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon blue">
                            <i class="bi bi-file-earmark-medical"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <p class="stat-value"><?php echo number_format($total_forms); ?></p>
                            <p class="stat-label">Total Forms</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon green">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <p class="stat-value"><?php echo number_format($today_forms); ?></p>
                            <p class="stat-label">Today's Forms</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon orange">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <p class="stat-value"><?php echo number_format($total_users); ?></p>
                            <p class="stat-label">Active Users</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Forms Table -->
        <div class="table-card">
            <h5><i class="bi bi-clock-history"></i> Recent Forms</h5>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Form Number</th>
                            <th>Date</th>
                            <th>Patient Name</th>
                            <th>Age/Gender</th>
                            <th>Vehicle</th>
                            <th>Injuries</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_forms)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2">No forms found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_forms as $form): ?>
                                <tr>
                                    <td><strong><?php echo e($form['form_number']); ?></strong></td>
                                    <td><?php echo date('M d, Y', strtotime($form['form_date'])); ?></td>
                                    <td><?php echo e($form['patient_name']); ?></td>
                                    <td><?php echo e($form['age']); ?> / <?php echo ucfirst(e($form['gender'])); ?></td>
                                    <td>
                                        <?php if ($form['vehicle_used']): ?>
                                            <span class="badge bg-info"><?php echo ucfirst(e($form['vehicle_used'])); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($form['injury_count'] > 0): ?>
                                            <span class="badge bg-danger"><?php echo $form['injury_count']; ?> injuries</span>
                                        <?php else: ?>
                                            <span class="text-muted">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'draft' => 'bg-secondary',
                                            'completed' => 'bg-success',
                                            'archived' => 'bg-dark'
                                        ];
                                        $class = $status_class[$form['status']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge badge-status <?php echo $class; ?>">
                                            <?php echo ucfirst(e($form['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo e($form['created_by_name'] ?? 'Unknown'); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-action" title="View">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success btn-action" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-action" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
