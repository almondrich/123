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
        html, body {
            height: 100%;
            overflow-y: auto;
            background-color: #f0f2f5;
        }

        .view-container {
            max-width: 100%;
            margin: 1rem auto;
            padding: 0 0.5rem;
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
            overflow: visible;
        }

        @media (max-width: 768px) {
            .view-container {
                margin: 1rem auto;
                padding: 0 0.5rem;
            }

            .view-header, .view-body {
                padding: 1rem;
            }

            .info-section h4 {
                font-size: 1.1rem;
            }
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
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tbody>
                            <tr>
                                <th width="30%">Form Date</th>
                                <td><?php echo date('F d, Y', strtotime($record['form_date'])); ?></td>
                            </tr>
                            <tr>
                                <th>Departure Time</th>
                                <td><?php echo e($record['departure_time'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Arrival Time</th>
                                <td><?php echo e($record['arrival_time'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Vehicle Used</th>
                                <td><?php echo ucfirst($record['vehicle_used'] ?: 'N/A'); ?></td>
                            </tr>

                            <tr>
                                <th>Driver</th>
                                <td><?php echo e($record['driver_name'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><span class="badge bg-success"><?php echo ucfirst($record['status']); ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Scene Information -->
            <div class="info-section">
                <h4><i class="bi bi-geo-alt"></i> Scene Information</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tbody>
                            <tr>
                                <th width="30%">Arrival Scene Location</th>
                                <td><?php echo e($record['arrival_scene_location'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Arrival Scene Time</th>
                                <td><?php echo e($record['arrival_scene_time'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Departure Scene Location</th>
                                <td><?php echo e($record['departure_scene_location'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Departure Scene Time</th>
                                <td><?php echo e($record['departure_scene_time'] ?: 'N/A'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="info-section">
                <h4><i class="bi bi-person-fill"></i> Patient Information</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tbody>
                            <tr>
                                <th width="30%">Patient Name</th>
                                <td><?php echo e($record['patient_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Date of Birth</th>
                                <td><?php echo date('F d, Y', strtotime($record['date_of_birth'])); ?></td>
                            </tr>
                            <tr>
                                <th>Age</th>
                                <td><?php echo e($record['age']); ?> years old</td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td><?php echo ucfirst($record['gender']); ?></td>
                            </tr>
                            <tr>
                                <th>Civil Status</th>
                                <td><?php echo ucfirst($record['civil_status'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td><?php echo e($record['address'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Zone</th>
                                <td><?php echo e($record['zone'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Occupation</th>
                                <td><?php echo e($record['occupation'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Place of Incident</th>
                                <td><?php echo e($record['place_of_incident'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Zone Landmark</th>
                                <td><?php echo e($record['zone_landmark'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Incident Time</th>
                                <td><?php echo e($record['incident_time'] ?: 'N/A'); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Informant Details -->
            <div class="info-section">
                <h4><i class="bi bi-person-lines-fill"></i> Informant Details</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tbody>
                            <tr>
                                <th width="30%">Informant Name</th>
                                <td><?php echo e($record['informant_name'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Informant Address</th>
                                <td><?php echo e($record['informant_address'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Arrival Type</th>
                                <td><?php echo ucfirst($record['arrival_type'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Call Arrival Time</th>
                                <td><?php echo e($record['call_arrival_time'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Contact Number</th>
                                <td><?php echo e($record['contact_number'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Relationship to Victim</th>
                                <td><?php echo e($record['relationship_victim'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Persons Present Upon Arrival</th>
                                <td>
                                    <?php
                                    $persons_present = $record['persons_present'];
                                    if ($persons_present) {
                                        $decoded = json_decode($persons_present, true);
                                        if ($decoded && is_array($decoded)) {
                                            echo '<ul class="list-unstyled mb-0">';
                                            foreach ($decoded as $person) {
                                                echo '<li>' . e($person) . '</li>';
                                            }
                                            echo '</ul>';
                                        } else {
                                            echo e($persons_present);
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Vital Signs -->
            <div class="info-section">
                <h4><i class="bi bi-activity"></i> Vital Signs</h4>
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">Initial</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <th width="40%">Blood Pressure</th>
                                        <td><?php echo e($record['initial_bp'] ?: 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Temperature</th>
                                        <td><?php echo $record['initial_temp'] ? $record['initial_temp'] . '°C' : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Pulse</th>
                                        <td><?php echo $record['initial_pulse'] ? $record['initial_pulse'] . ' BPM' : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>SPO2</th>
                                        <td><?php echo $record['initial_spo2'] ? $record['initial_spo2'] . '%' : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Consciousness</th>
                                        <td><?php echo ucfirst($record['initial_consciousness'] ?: 'N/A'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3">Follow-up</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <tbody>
                                    <tr>
                                        <th width="40%">Blood Pressure</th>
                                        <td><?php echo e($record['followup_bp'] ?: 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Temperature</th>
                                        <td><?php echo $record['followup_temp'] ? $record['followup_temp'] . '°C' : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Pulse</th>
                                        <td><?php echo $record['followup_pulse'] ? $record['followup_pulse'] . ' BPM' : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>SPO2</th>
                                        <td><?php echo $record['followup_spo2'] ? $record['followup_spo2'] . '%' : 'N/A'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Consciousness</th>
                                        <td><?php echo ucfirst($record['followup_consciousness'] ?: 'N/A'); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Injuries -->
            <?php if (!empty($injuries)): ?>
            <div class="info-section">
                <h4><i class="bi bi-bandaid"></i> Injuries Marked (<?php echo count($injuries); ?>)</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="10%">Injury #</th>
                                <th width="30%">Type</th>
                                <th width="20%">View</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($injuries as $injury): ?>
                                <tr>
                                    <td><?php echo $injury['injury_number']; ?></td>
                                    <td><span class="badge bg-danger"><?php echo ucfirst($injury['injury_type']); ?></span></td>
                                    <td><span class="badge bg-secondary"><?php echo ucfirst($injury['body_view']); ?> View</span></td>
                                    <td><?php echo e($injury['notes'] ?: 'N/A'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Hospital Information -->
            <div class="info-section">
                <h4><i class="bi bi-hospital"></i> Hospital & Team Information</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tbody>
                            <tr>
                                <th width="30%">Arrival Hospital Name</th>
                                <td><?php echo e($record['arrival_hospital_name'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Arrival Hospital Time</th>
                                <td><?php echo e($record['arrival_hospital_time'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Departure Hospital Location</th>
                                <td><?php echo e($record['departure_hospital_location'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Departure Hospital Time</th>
                                <td><?php echo e($record['departure_hospital_time'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Arrival Station Time</th>
                                <td><?php echo e($record['arrival_station_time'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Team Leader</th>
                                <td><?php echo e($record['team_leader'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Data Recorder</th>
                                <td><?php echo e($record['data_recorder'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Logistic</th>
                                <td><?php echo e($record['logistic'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>1st Aider</th>
                                <td><?php echo e($record['first_aider'] ?: 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>2nd Aider</th>
                                <td><?php echo e($record['second_aider'] ?: 'N/A'); ?></td>
                            </tr>
                        </tbody>
                    </table>
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
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <tbody>
                            <tr>
                                <th width="30%">Created At</th>
                                <td><?php echo date('F d, Y g:i A', strtotime($record['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <th>Last Updated</th>
                                <td><?php echo date('F d, Y g:i A', strtotime($record['updated_at'])); ?></td>
                            </tr>
                        </tbody>
                    </table>
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
