<?php
/**
 * View Pre-Hospital Care Record Details
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Require authentication
require_login();

// Get record ID
$record_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($record_id <= 0) {
    set_flash('Invalid record ID', 'error');
    redirect('records.php');
}

// Get record details
$sql = "SELECT * FROM prehospital_forms WHERE id = ?";
$stmt = db_query($sql, [$record_id]);
$record = $stmt->fetch();

if (!$record) {
    set_flash('Record not found', 'error');
    redirect('records.php');
}

// Get injuries for this record
$injury_sql = "SELECT * FROM injuries WHERE form_id = ? ORDER BY injury_number";
$injury_stmt = db_query($injury_sql, [$record_id]);
$injuries = $injury_stmt->fetchAll();

// Get current user
$current_user = get_auth_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Record - <?php echo e($record['form_number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/tonyang-form.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        
        .view-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .view-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 10px 10px 0 0;
        }
        
        .view-body {
            background: white;
            padding: 2rem;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .info-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .info-section:last-child {
            border-bottom: none;
        }
        
        .info-section h4 {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .info-item {
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 6px;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .info-value {
            font-size: 1.05rem;
            color: #333;
        }
        
        .injury-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1rem;
        }
        
        .injury-card {
            background: var(--bg-light);
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid var(--primary-color);
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white;
            }
            
            .view-container {
                max-width: 100%;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="view-container">
        <div class="view-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-file-earmark-text"></i> <?php echo e($record['form_number']); ?></h2>
                    <p class="mb-0">Pre-Hospital Care Form Details</p>
                </div>
                <div class="no-print">
                    <a href="records.php" class="btn btn-light me-2">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-light">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <div class="view-body">
            <!-- Basic Information -->
            <div class="info-section">
                <h4><i class="bi bi-info-circle"></i> Basic Information</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Form Date</div>
                        <div class="info-value"><?php echo date('F d, Y', strtotime($record['form_date'])); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Departure Time</div>
                        <div class="info-value"><?php echo e($record['departure_time'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Arrival Time</div>
                        <div class="info-value"><?php echo e($record['arrival_time'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Vehicle Used</div>
                        <div class="info-value"><?php echo ucfirst($record['vehicle_used'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Driver</div>
                        <div class="info-value"><?php echo e($record['driver_name'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="badge bg-success"><?php echo ucfirst($record['status']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="info-section">
                <h4><i class="bi bi-person-fill"></i> Patient Information</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Patient Name</div>
                        <div class="info-value"><?php echo e($record['patient_name']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value"><?php echo date('F d, Y', strtotime($record['date_of_birth'])); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Age</div>
                        <div class="info-value"><?php echo e($record['age']); ?> years old</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?php echo ucfirst($record['gender']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Civil Status</div>
                        <div class="info-value"><?php echo ucfirst($record['civil_status'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?php echo e($record['address'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Occupation</div>
                        <div class="info-value"><?php echo e($record['occupation'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Place of Incident</div>
                        <div class="info-value"><?php echo e($record['place_of_incident'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Incident Time</div>
                        <div class="info-value"><?php echo e($record['incident_time'] ?: 'N/A'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Vital Signs -->
            <div class="info-section">
                <h4><i class="bi bi-activity"></i> Vital Signs</h4>
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">Initial</h5>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Blood Pressure</div>
                                <div class="info-value"><?php echo e($record['initial_bp'] ?: 'N/A'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Temperature</div>
                                <div class="info-value"><?php echo $record['initial_temp'] ? $record['initial_temp'] . '°C' : 'N/A'; ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Pulse</div>
                                <div class="info-value"><?php echo $record['initial_pulse'] ? $record['initial_pulse'] . ' BPM' : 'N/A'; ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">SPO2</div>
                                <div class="info-value"><?php echo $record['initial_spo2'] ? $record['initial_spo2'] . '%' : 'N/A'; ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Consciousness</div>
                                <div class="info-value"><?php echo ucfirst($record['initial_consciousness'] ?: 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">Follow-up</h5>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Blood Pressure</div>
                                <div class="info-value"><?php echo e($record['followup_bp'] ?: 'N/A'); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Temperature</div>
                                <div class="info-value"><?php echo $record['followup_temp'] ? $record['followup_temp'] . '°C' : 'N/A'; ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Pulse</div>
                                <div class="info-value"><?php echo $record['followup_pulse'] ? $record['followup_pulse'] . ' BPM' : 'N/A'; ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">SPO2</div>
                                <div class="info-value"><?php echo $record['followup_spo2'] ? $record['followup_spo2'] . '%' : 'N/A'; ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Consciousness</div>
                                <div class="info-value"><?php echo ucfirst($record['followup_consciousness'] ?: 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Injuries -->
            <?php if (!empty($injuries)): ?>
            <div class="info-section">
                <h4><i class="bi bi-bandaid"></i> Injuries Marked (<?php echo count($injuries); ?>)</h4>
                <div class="injury-list">
                    <?php foreach ($injuries as $injury): ?>
                        <div class="injury-card">
                            <strong>Injury #<?php echo $injury['injury_number']; ?></strong>
                            <div class="mt-2">
                                <span class="badge bg-danger"><?php echo ucfirst($injury['injury_type']); ?></span>
                                <span class="badge bg-secondary"><?php echo ucfirst($injury['body_view']); ?> View</span>
                            </div>
                            <?php if ($injury['notes']): ?>
                                <div class="mt-2 text-muted small">
                                    <?php echo e($injury['notes']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Hospital Information -->
            <div class="info-section">
                <h4><i class="bi bi-hospital"></i> Hospital & Team Information</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Hospital Name</div>
                        <div class="info-value"><?php echo e($record['arrival_hospital_name'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Team Leader</div>
                        <div class="info-value"><?php echo e($record['team_leader'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Data Recorder</div>
                        <div class="info-value"><?php echo e($record['data_recorder'] ?: 'N/A'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">1st Aider</div>
                        <div class="info-value"><?php echo e($record['first_aider'] ?: 'N/A'); ?></div>
                    </div>
                </div>
                
                <?php if ($record['team_leader_notes']): ?>
                    <div class="mt-3">
                        <div class="info-label">Team Leader Notes</div>
                        <div class="alert alert-info">
                            <?php echo nl2br(e($record['team_leader_notes'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Record Metadata -->
            <div class="info-section">
                <h4><i class="bi bi-clock-history"></i> Record Information</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Created At</div>
                        <div class="info-value"><?php echo date('F d, Y g:i A', strtotime($record['created_at'])); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Last Updated</div>
                        <div class="info-value"><?php echo date('F d, Y g:i A', strtotime($record['updated_at'])); ?></div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 no-print">
                <a href="edit_record.php?id=<?php echo $record['id']; ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit Record
                </a>
                <a href="records.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Records
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
