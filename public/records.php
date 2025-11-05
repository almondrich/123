<?php
/**
 * Pre-Hospital Care Records - View All Saved Forms
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Require authentication
require_login();

// Get current user
$current_user = get_auth_user();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Search and filter
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';

// Build query
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(form_number LIKE ? OR patient_name LIKE ? OR place_of_incident LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = "form_date >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = "form_date <= ?";
    $params[] = $date_to;
}

$where_sql = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM prehospital_forms $where_sql";
$count_stmt = db_query($count_sql, $params);
$total_records = $count_stmt->fetch()['total'];
$total_pages = ceil($total_records / $per_page);

// Get records
$sql = "SELECT 
    id, form_number, form_date, patient_name, age, gender, 
    place_of_incident, vehicle_used, status, created_at,
    arrival_hospital_name
    FROM prehospital_forms 
    $where_sql 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?";

$params[] = $per_page;
$params[] = $offset;

$stmt = db_query($sql, $params);
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Records - Pre-Hospital Care System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/tonyang-form.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .records-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .page-header {
            background: #ffffff;
            color: #212529;
            padding: 2rem;
            border-radius: 6px;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            border: 1px solid #dee2e6;
            border-left: 4px solid #0d6efd;
        }
        
        .page-header h1 {
            margin: 0;
            font-size: clamp(1.5rem, 3vw, 1.75rem);
            font-weight: 600;
            color: #212529;
        }
        
        .page-header p {
            margin: 0.5rem 0 0 0;
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        .filters-card {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #dee2e6;
        }
        
        .records-card {
            background: white;
            border-radius: 6px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            border: 1px solid #dee2e6;
        }
        
        .table-responsive {
            border-radius: 4px;
            overflow: hidden;
        }
        
        .table {
            margin-bottom: 0;
            font-size: 0.875rem;
        }
        
        .table thead {
            background-color: #f8f9fa;
        }
        
        .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .badge {
            padding: 0.35rem 0.65rem;
            font-weight: 500;
            font-size: 0.75rem;
            border-radius: 4px;
            border: 1px solid;
        }
        
        .status-completed {
            background-color: #d1e7dd;
            color: #0f5132;
            border-color: #badbcc;
        }
        
        .status-draft {
            background-color: #fff3cd;
            color: #664d03;
            border-color: #ffecb5;
        }
        
        .status-pending {
            background-color: #cff4fc;
            color: #055160;
            border-color: #b6effb;
        }
        
        .btn {
            border-radius: 4px;
            font-weight: 500;
            border-width: 1px;
            transition: all 0.2s;
        }
        
        .btn-action {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            margin: 0 0.2rem;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        
        .btn-outline-primary {
            color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .btn-outline-primary:hover {
            background-color: #0d6efd;
            color: white;
        }
        
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-outline-success {
            color: #198754;
            border-color: #198754;
        }
        
        .btn-outline-success:hover {
            background-color: #198754;
            color: white;
        }
        
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            border: 1px solid #dee2e6;
            border-left: 4px solid #0d6efd;
            transition: box-shadow 0.2s;
        }
        
        .stat-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .stat-card:nth-child(2) {
            border-left-color: #198754;
        }
        
        .stat-card:nth-child(3) {
            border-left-color: #ffc107;
        }
        
        .stat-card:nth-child(4) {
            border-left-color: #0dcaf0;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin: 0;
        }
        
        .stat-card p {
            margin: 0.5rem 0 0 0;
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
        
        .pagination {
            margin-top: 1.5rem;
        }
        
        .pagination .page-link {
            color: #0d6efd;
            border: 1px solid #dee2e6;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .form-control, .form-select {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.25);
        }
        
        @media (max-width: 768px) {
            .top-actions {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .stats-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="records-container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-folder2-open"></i> Pre-Hospital Care Records</h1>
                    <p>View and manage all saved forms | User: <?php echo e($current_user['full_name']); ?></p>
                </div>
                <div>
                    <a href="TONYANG.php" class="btn btn-light">
                        <i class="bi bi-plus-circle"></i> New Form
                    </a>
                    <a href="logout.php" class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <?php show_flash(); ?>

        <!-- Statistics -->
        <div class="stats-row">
            <div class="stat-card">
                <h3><?php echo number_format($total_records); ?></h3>
                <p><i class="bi bi-file-earmark-text"></i> Total Records</p>
            </div>
            <div class="stat-card" style="border-left-color: #28a745;">
                <h3>
                    <?php 
                    $completed_sql = "SELECT COUNT(*) as count FROM prehospital_forms WHERE status = 'completed'";
                    $completed_stmt = db_query($completed_sql);
                    echo number_format($completed_stmt->fetch()['count']);
                    ?>
                </h3>
                <p><i class="bi bi-check-circle"></i> Completed</p>
            </div>
            <div class="stat-card" style="border-left-color: #ffc107;">
                <h3>
                    <?php 
                    $today_sql = "SELECT COUNT(*) as count FROM prehospital_forms WHERE DATE(created_at) = CURDATE()";
                    $today_stmt = db_query($today_sql);
                    echo number_format($today_stmt->fetch()['count']);
                    ?>
                </h3>
                <p><i class="bi bi-calendar-check"></i> Today</p>
            </div>
            <div class="stat-card" style="border-left-color: #17a2b8;">
                <h3>
                    <?php 
                    $week_sql = "SELECT COUNT(*) as count FROM prehospital_forms WHERE YEARWEEK(created_at) = YEARWEEK(NOW())";
                    $week_stmt = db_query($week_sql);
                    echo number_format($week_stmt->fetch()['count']);
                    ?>
                </h3>
                <p><i class="bi bi-calendar-week"></i> This Week</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-card">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" 
                           placeholder="Form #, Patient, Location..." 
                           value="<?php echo e($search); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" class="form-control" name="date_from" 
                           value="<?php echo e($date_from); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" class="form-control" name="date_to" 
                           value="<?php echo e($date_to); ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="records.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Records Table -->
        <div class="records-card">
            <div class="top-actions">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul"></i> Records 
                    <span class="text-muted">(<?php echo number_format($total_records); ?> total)</span>
                </h5>
                <div>
                    <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="exportToCSV()">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                    </button>
                </div>
            </div>

            <?php if (empty($records)): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h4>No Records Found</h4>
                    <p>No pre-hospital care forms match your search criteria.</p>
                    <a href="TONYANG.php" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle"></i> Create First Form
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Form #</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Age/Gender</th>
                                <th>Incident Location</th>
                                <th>Hospital</th>
                                <th>Vehicle</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($record['form_number']); ?></strong>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($record['form_date'])); ?></td>
                                    <td><?php echo e($record['patient_name']); ?></td>
                                    <td>
                                        <?php echo e($record['age']); ?> / 
                                        <?php echo ucfirst($record['gender']); ?>
                                    </td>
                                    <td><?php echo e($record['place_of_incident'] ?: '-'); ?></td>
                                    <td><?php echo e($record['arrival_hospital_name'] ?: '-'); ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo ucfirst($record['vehicle_used'] ?: 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-<?php echo e($record['status']); ?>">
                                            <?php echo ucfirst($record['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_record.php?id=<?php echo $record['id']; ?>" 
                                           class="btn btn-sm btn-primary btn-action" 
                                           title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_record.php?id=<?php echo $record['id']; ?>" 
                                           class="btn btn-sm btn-warning btn-action" 
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button onclick="deleteRecord(<?php echo $record['id']; ?>)" 
                                                class="btn btn-sm btn-danger btn-action" 
                                                title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>">
                                    Previous
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <?php if ($i == 1 || $i == $total_pages || abs($i - $page) <= 2): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php elseif (abs($i - $page) == 3): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>">
                                    Next
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
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

        function exportToCSV() {
            window.location.href = 'api/export_records.php?search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>';
        }
    </script>
</body>
</html>
