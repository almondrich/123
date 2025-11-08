<?php
/**
 * Export Pre-Hospital Care Records to CSV
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Require authentication
require_login();

// Rate limiting - prevent abuse and resource exhaustion
if (!check_rate_limit('export_records', 10, 3600)) { // 10 exports per hour
    set_flash('Too many export requests. Please wait before exporting again.', 'error');
    redirect('../public/records.php');
}

// Get filters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($_GET['date_to']) : '';

// Validate date formats if provided
if (!empty($date_from) && !validate_date($date_from)) {
    set_flash('Invalid date_from format', 'error');
    redirect('../public/records.php');
}
if (!empty($date_to) && !validate_date($date_to)) {
    set_flash('Invalid date_to format', 'error');
    redirect('../public/records.php');
}

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

// Get records with result limit to prevent resource exhaustion
// Limit to 10,000 records per export to prevent memory issues
$sql = "SELECT 
    form_number, form_date, patient_name, age, gender, civil_status,
    address, occupation, place_of_incident, incident_time,
    vehicle_used, driver_name, arrival_hospital_name,
    initial_bp, initial_temp, initial_pulse, initial_spo2,
    team_leader, status, created_at
    FROM prehospital_forms 
    $where_sql 
    ORDER BY created_at DESC
    LIMIT 10000";

$stmt = db_query($sql, $params);
$records = $stmt->fetchAll();

// Log export activity
log_activity('export_records', "Exported " . count($records) . " records");

// Set headers for CSV download
$filename = 'prehospital_records_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for Excel UTF-8 support
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, [
    'Form Number',
    'Form Date',
    'Patient Name',
    'Age',
    'Gender',
    'Civil Status',
    'Address',
    'Occupation',
    'Place of Incident',
    'Incident Time',
    'Vehicle Used',
    'Driver',
    'Hospital',
    'Initial BP',
    'Initial Temp',
    'Initial Pulse',
    'Initial SPO2',
    'Team Leader',
    'Status',
    'Created At'
]);

// Add data rows
foreach ($records as $record) {
    fputcsv($output, [
        $record['form_number'],
        $record['form_date'],
        $record['patient_name'],
        $record['age'],
        ucfirst($record['gender']),
        ucfirst($record['civil_status'] ?: ''),
        $record['address'],
        $record['occupation'],
        $record['place_of_incident'],
        $record['incident_time'],
        ucfirst($record['vehicle_used'] ?: ''),
        $record['driver_name'],
        $record['arrival_hospital_name'],
        $record['initial_bp'],
        $record['initial_temp'],
        $record['initial_pulse'],
        $record['initial_spo2'],
        $record['team_leader'],
        ucfirst($record['status']),
        $record['created_at']
    ]);
}

fclose($output);
exit;
