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

// Helper function to format time with AM/PM
function format_time($time) {
    if (empty($time) || $time === '00:00:00' || $time === '0') {
        return 'N/A';
    }
    // Convert 24-hour time to 12-hour format with AM/PM
    return date('g:i A', strtotime($time));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Record - <?php echo e($record['form_number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/view-record.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
    <div class="record-wrapper">
        <div class="form-container">
            <!-- Header -->
            <div class="header">
                <img src="uploads/logo.png" alt="Logo" class="logo">
                <h1><i class="bi bi-file-medical-fill"></i> PRE-HOSPITAL CARE FORM</h1>
                <div class="form-meta">
                    <div class="form-meta-item">
                        <i class="bi bi-hash"></i>
                        <span><?php echo e($record['form_number']); ?></span>
                    </div>
                    <div class="form-meta-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span class="badge-status badge-success"><?php echo ucfirst($record['status']); ?></span>
                    </div>
                    <div class="form-meta-item">
                        <i class="bi bi-calendar-event"></i>
                        <span><?php echo date('F d, Y', strtotime($record['form_date'])); ?></span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Top) -->
            <div class="action-buttons no-print">
                <a href="records.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Records
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="edit_record.php?id=<?php echo $record['id']; ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>

            <!-- Record Content -->
            <div class="record-content">

        <!-- Basic Information -->
        <div class="section-header">
            <i class="bi bi-info-circle-fill"></i>
            BASIC INFORMATION
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Form Number</label>
                <div class="value"><?php echo e($record['form_number']); ?></div>
            </div>
            <div class="form-group">
                <label>Form Date</label>
                <div class="value"><?php echo date('F d, Y', strtotime($record['form_date'])); ?></div>
            </div>
            <div class="form-group">
                <label>Status</label>
                <div class="value">
                    <?php
                    $status_class = [
                        'completed' => 'badge-success',
                        'pending' => 'badge-info',
                        'draft' => 'badge-info'
                    ];
                    $class = $status_class[$record['status']] ?? 'badge-info';
                    ?>
                    <span class="badge-status <?php echo $class; ?>"><?php echo ucfirst($record['status']); ?></span>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Departure Time (From Station)</label>
                <div class="value"><?php echo format_time($record['departure_time']); ?></div>
            </div>
            <div class="form-group">
                <label>Arrival Time (To Station)</label>
                <div class="value"><?php echo format_time($record['arrival_time']); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Vehicle Used</label>
                <div class="value"><?php echo ucfirst($record['vehicle_used'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Driver Name</label>
                <div class="value"><?php echo e($record['driver_name'] ?: 'N/A'); ?></div>
            </div>
        </div>

        <!-- Scene Information -->
        <div class="section-header">
            <i class="bi bi-geo-alt-fill"></i>
            SCENE INFORMATION
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Arrival Scene Location</label>
                <div class="value"><?php echo e($record['arrival_scene_location'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Arrival Scene Time</label>
                <div class="value"><?php echo format_time($record['arrival_scene_time']); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Departure Scene Location</label>
                <div class="value"><?php echo e($record['departure_scene_location'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Departure Scene Time</label>
                <div class="value"><?php echo format_time($record['departure_scene_time']); ?></div>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="section-header">
            <i class="bi bi-person-fill"></i>
            PATIENT INFORMATION
        </div>
        <div class="form-row">
            <div class="form-group full-width" data-field="patient_name">
                <label>Patient Name</label>
                <div class="value"><?php echo e($record['patient_name']); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Date of Birth</label>
                <div class="value"><?php echo date('F d, Y', strtotime($record['date_of_birth'])); ?></div>
            </div>
            <div class="form-group" data-field="age">
                <label>Age</label>
                <div class="value"><?php echo e($record['age']); ?> years old</div>
            </div>
            <div class="form-group" data-field="gender">
                <label>Gender</label>
                <div class="value"><?php echo ucfirst($record['gender']); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Civil Status</label>
                <div class="value"><?php echo ucfirst($record['civil_status'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Occupation</label>
                <div class="value"><?php echo e($record['occupation'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <div class="value"><?php echo e($record['contact_number'] ?: 'N/A'); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group full-width">
                <label>Address</label>
                <div class="value"><?php echo e($record['address'] ?: 'N/A'); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Zone</label>
                <div class="value"><?php echo e($record['zone'] ?: 'N/A'); ?></div>
            </div>
        </div>

        <!-- Incident Details -->
        <div class="section-header">
            <i class="bi bi-exclamation-triangle-fill"></i>
            INCIDENT DETAILS
        </div>
        <div class="form-row">
            <div class="form-group full-width" data-field="place_of_incident">
                <label>Place of Incident</label>
                <div class="value"><?php echo e($record['place_of_incident'] ?: 'N/A'); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Zone Landmark</label>
                <div class="value"><?php echo e($record['zone_landmark'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group" data-field="incident_time">
                <label>Incident Time</label>
                <div class="value"><?php echo format_time($record['incident_time']); ?></div>
            </div>
        </div>

        <!-- Informant Details -->
        <div class="section-header">
            <i class="bi bi-telephone-fill"></i>
            INFORMANT DETAILS
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Informant Name</label>
                <div class="value"><?php echo e($record['informant_name'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Relationship to Victim</label>
                <div class="value"><?php echo e($record['relationship_victim'] ?: 'N/A'); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group full-width">
                <label>Informant Address</label>
                <div class="value"><?php echo e($record['informant_address'] ?: 'N/A'); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Arrival Type</label>
                <div class="value"><?php echo ucfirst($record['arrival_type'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Call Arrival Time</label>
                <div class="value"><?php echo format_time($record['call_arrival_time']); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group full-width">
                <label>Persons Present Upon Arrival</label>
                <div class="value">
                    <?php
                    $persons_present = $record['persons_present'];
                    if ($persons_present) {
                        $decoded = json_decode($persons_present, true);
                        if ($decoded && is_array($decoded)) {
                            echo implode(', ', array_map('e', $decoded));
                        } else {
                            echo e($persons_present);
                        }
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Vital Signs -->
        <div class="section-header">
            <i class="bi bi-heart-pulse-fill"></i>
            VITAL SIGNS
        </div>
        <div class="vital-signs-grid">
            <div class="vital-box">
                <h4><i class="bi bi-clipboard-pulse"></i> Initial Assessment</h4>
                <div class="form-group">
                    <label>Time</label>
                    <div class="value"><?php echo format_time($record['initial_time']); ?></div>
                </div>
                <div class="form-group">
                    <label>Blood Pressure</label>
                    <div class="value"><?php echo e($record['initial_bp'] ?: 'N/A'); ?></div>
                </div>
                <div class="form-group">
                    <label>Temperature</label>
                    <div class="value"><?php echo $record['initial_temp'] ? $record['initial_temp'] . '°C' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>Pulse</label>
                    <div class="value"><?php echo $record['initial_pulse'] ? $record['initial_pulse'] . ' BPM' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>Respiratory Rate</label>
                    <div class="value"><?php echo $record['initial_resp_rate'] ? $record['initial_resp_rate'] . ' /min' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>Pain Score</label>
                    <div class="value"><?php echo $record['initial_pain_score'] !== null ? $record['initial_pain_score'] . '/10' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>SpO2</label>
                    <div class="value"><?php echo $record['initial_spo2'] ? $record['initial_spo2'] . '%' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>Spinal Injury</label>
                    <div class="value"><?php echo ucfirst($record['initial_spinal_injury'] ?: 'N/A'); ?></div>
                </div>
                <div class="form-group">
                    <label>Consciousness</label>
                    <div class="value"><?php echo ucfirst($record['initial_consciousness'] ?: 'N/A'); ?></div>
                </div>
            </div>

            <div class="vital-box">
                <h4><i class="bi bi-arrow-repeat"></i> Follow-up Assessment</h4>
                <div class="form-group">
                    <label>Time</label>
                    <div class="value"><?php echo format_time($record['followup_time']); ?></div>
                </div>
                <div class="form-group">
                    <label>Blood Pressure</label>
                    <div class="value"><?php echo e($record['followup_bp'] ?: 'N/A'); ?></div>
                </div>
                <div class="form-group">
                    <label>Temperature</label>
                    <div class="value"><?php echo $record['followup_temp'] ? $record['followup_temp'] . '°C' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>Pulse</label>
                    <div class="value"><?php echo $record['followup_pulse'] ? $record['followup_pulse'] . ' BPM' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>Respiratory Rate</label>
                    <div class="value"><?php echo $record['followup_resp_rate'] ? $record['followup_resp_rate'] . ' /min' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>Pain Score</label>
                    <div class="value"><?php echo $record['followup_pain_score'] !== null ? $record['followup_pain_score'] . '/10' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>SpO2</label>
                    <div class="value"><?php echo $record['followup_spo2'] ? $record['followup_spo2'] . '%' : 'N/A'; ?></div>
                </div>
                <div class="form-group">
                    <label>Consciousness</label>
                    <div class="value"><?php echo ucfirst($record['followup_consciousness'] ?: 'N/A'); ?></div>
                </div>
            </div>
        </div>

        <!-- Injury Mapping -->
        <div class="section-header">
            <i class="bi bi-person-bounding-box"></i>
            INJURY MAPPING
        </div>
        <div class="body-diagram-container">
            <div class="body-diagram-content">
                <div class="body-views">
                    <div class="body-view">
                        <div class="view-label">FRONT VIEW</div>
                        <div class="body-image-container" id="frontContainer">
                            <img src="images/body-front.png" alt="Body Front" class="body-image">
                        </div>
                    </div>

                    <div class="body-view">
                        <div class="view-label">BACK VIEW</div>
                        <div class="body-image-container" id="backContainer">
                            <img src="images/body-back.png" alt="Body Back" class="body-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($injuries)): ?>
        <div class="section-header">
            <i class="bi bi-bandaid-fill"></i>
            INJURIES DETAILS (<?php echo count($injuries); ?>)
        </div>
        <div class="injuries-table-container">
            <table class="injuries-table">
                <thead>
                    <tr>
                        <th>Injury #</th>
                        <th>Type</th>
                        <th>View</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($injuries as $injury): ?>
                    <tr>
                        <td><?php echo $injury['injury_number']; ?></td>
                        <td><?php echo ucfirst($injury['injury_type']); ?></td>
                        <td><?php echo ucfirst($injury['body_view']); ?> View</td>
                        <td><?php echo e($injury['notes'] ?: 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Hospital Information -->
        <div class="section-header">
            <i class="bi bi-hospital-fill"></i>
            HOSPITAL INFORMATION
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Arrival Hospital Name</label>
                <div class="value"><?php echo e($record['arrival_hospital_name'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Arrival Hospital Time</label>
                <div class="value"><?php echo format_time($record['arrival_hospital_time']); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Departure Hospital Location</label>
                <div class="value"><?php echo e($record['departure_hospital_location'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Departure Hospital Time</label>
                <div class="value"><?php echo format_time($record['departure_hospital_time']); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Arrival Back to Station Time</label>
                <div class="value"><?php echo format_time($record['arrival_station_time']); ?></div>
            </div>
        </div>

        <!-- Team Information -->
        <div class="section-header">
            <i class="bi bi-people-fill"></i>
            TEAM INFORMATION
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Team Leader</label>
                <div class="value"><?php echo e($record['team_leader'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Data Recorder</label>
                <div class="value"><?php echo e($record['data_recorder'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>Logistic</label>
                <div class="value"><?php echo e($record['logistic'] ?: 'N/A'); ?></div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>1st Aider</label>
                <div class="value"><?php echo e($record['first_aider'] ?: 'N/A'); ?></div>
            </div>
            <div class="form-group">
                <label>2nd Aider</label>
                <div class="value"><?php echo e($record['second_aider'] ?: 'N/A'); ?></div>
            </div>
        </div>

        <?php if ($record['team_leader_notes']): ?>
        <div class="section-header">
            <i class="bi bi-journal-text"></i>
            TEAM LEADER NOTES
        </div>
        <div class="form-row">
            <div class="form-group full-width">
                <div class="value multiline"><?php echo nl2br(e($record['team_leader_notes'])); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Record Information -->
        <div class="section-header">
            <i class="bi bi-clock-history"></i>
            RECORD INFORMATION
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Created At</label>
                <div class="value"><?php echo date('F d, Y g:i A', strtotime($record['created_at'])); ?></div>
            </div>
            <div class="form-group">
                <label>Last Updated</label>
                <div class="value"><?php echo date('F d, Y g:i A', strtotime($record['updated_at'])); ?></div>
            </div>
        </div>

            </div><!-- End record-content -->

            <!-- Action Buttons (Bottom) -->
            <div class="action-buttons no-print">
                <a href="records.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Records
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="edit_record.php?id=<?php echo $record['id']; ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div><!-- End form-container -->
    </div><!-- End record-wrapper -->

    <script>
        // Function to render injury markers on the body diagram
        function renderInjuryMarkers() {
            const injuries = <?php echo json_encode($injuries); ?>;

            injuries.forEach(injury => {
                const containerId = injury.body_view === 'front' ? 'frontContainer' : 'backContainer';
                const container = document.getElementById(containerId);

                if (container) {
                    const marker = document.createElement('div');
                    marker.className = `injury-marker ${injury.injury_type}`;
                    marker.style.left = injury.coordinate_x + '%';
                    marker.style.top = injury.coordinate_y + '%';
                    marker.textContent = injury.injury_number;
                    marker.title = `${injury.injury_type.charAt(0).toUpperCase() + injury.injury_type.slice(1)} - ${injury.notes || 'No notes'}`;

                    container.appendChild(marker);
                }
            });
        }

        // Initialize markers when page loads
        document.addEventListener('DOMContentLoaded', renderInjuryMarkers);
    </script>
</body>
</html>
