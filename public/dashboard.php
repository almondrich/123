<?php
/**
 * User Dashboard
 * View and manage personal pre-hospital care forms
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Require authentication
require_login();

// Get current user
$current_user = get_auth_user();

// Get user statistics
$user_id = $current_user['id'];

$total_forms_stmt = db_query("SELECT COUNT(*) as count FROM prehospital_forms WHERE created_by = ?", [$user_id]);
$total_forms = $total_forms_stmt->fetch()['count'];

$today_forms_stmt = db_query("SELECT COUNT(*) as count FROM prehospital_forms WHERE created_by = ? AND DATE(created_at) = CURDATE()", [$user_id]);
$today_forms = $today_forms_stmt->fetch()['count'];

$pending_forms_stmt = db_query("SELECT COUNT(*) as count FROM prehospital_forms WHERE created_by = ? AND status IN ('draft', 'pending')", [$user_id]);
$pending_forms = $pending_forms_stmt->fetch()['count'];

$completed_forms_stmt = db_query("SELECT COUNT(*) as count FROM prehospital_forms WHERE created_by = ? AND status = 'completed'", [$user_id]);
$completed_forms = $completed_forms_stmt->fetch()['count'];

// Get recent forms
$recent_forms_stmt = db_query("SELECT * FROM prehospital_forms WHERE created_by = ? ORDER BY created_at DESC LIMIT 10", [$user_id]);
$recent_forms = $recent_forms_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Pre-Hospital Care System</title>
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
        .stat-icon.purple { background: linear-gradient(135deg, #6f42c1, #e83e8c); }

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

        .welcome-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .welcome-section h2 {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .welcome-section p {
            color: #6c757d;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Include the navbar -->
    <?php include '../includes/navbar.php'; ?>

    <div class="container-fluid py-4">
        <?php show_flash(); ?>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>Welcome back, <?php echo e($current_user['full_name']); ?>!</h2>
                    <p>Here's an overview of your pre-hospital care forms and recent activity.</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="TONYANG.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> Create New Form
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
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

            <div class="col-md-3">
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

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon orange">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <p class="stat-value"><?php echo number_format($pending_forms); ?></p>
                            <p class="stat-label">Pending Forms</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon purple">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <p class="stat-value"><?php echo number_format($completed_forms); ?></p>
                            <p class="stat-label">Completed Forms</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Forms Table -->
        <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="bi bi-clock-history"></i> Your Recent Forms</h5>
                <a href="records.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-list-ul"></i> View All Records
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Form Number</th>
                            <th>Date</th>
                            <th>Patient Name</th>
                            <th>Age/Gender</th>
                            <th>Vehicle</th>
                            <th>Hospital</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_forms)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2">No forms found</p>
                                    <a href="TONYANG.php" class="btn btn-primary mt-2">
                                        <i class="bi bi-plus-circle"></i> Create Your First Form
                                    </a>
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
                                    <td><?php echo e($form['arrival_hospital_name'] ?: '-'); ?></td>
                                    <td>
                                        <?php
                                        $status_class = [
                                            'draft' => 'bg-secondary',
                                            'completed' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'archived' => 'bg-dark'
                                        ];
                                        $class = $status_class[$form['status']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge badge-status <?php echo $class; ?>">
                                            <?php echo ucfirst(e($form['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="view_record.php?id=<?php echo $form['id']; ?>"
                                               class="btn btn-outline-primary btn-action" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="edit_record.php?id=<?php echo $form['id']; ?>"
                                               class="btn btn-outline-success btn-action" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-outline-danger btn-action" title="Delete"
                                                    onclick="deleteRecord(<?php echo $form['id']; ?>)">
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
    <script>
        function deleteRecord(id) {
            if (confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
                fetch('api/delete_record.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Record deleted successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error deleting record');
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>
