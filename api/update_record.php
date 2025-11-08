<?php
/**
 * Update Pre-Hospital Care Record
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Require authentication
require_login();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('Invalid request method', 'error');
    redirect('../public/records.php');
}

// Verify CSRF token
if (!verify_token($_POST['csrf_token'] ?? '')) {
    set_flash('Security token validation failed', 'error');
    redirect('../public/records.php');
}

try {
    // Validate record ID format and range
    $record_id = isset($_POST['record_id']) ? (int)$_POST['record_id'] : 0;
    
    if ($record_id <= 0 || $record_id > 2147483647) {
        throw new Exception('Invalid record ID');
    }
    
    // Verify ID format (prevent injection attempts)
    if (isset($_POST['record_id']) && !preg_match('/^\d+$/', (string)$_POST['record_id'])) {
        throw new Exception('Invalid record ID format');
    }
    
    // Check if record exists
    $check_sql = "SELECT id, form_number, created_by FROM prehospital_forms WHERE id = ?";
    $check_stmt = db_query($check_sql, [$record_id]);
    $existing_record = $check_stmt->fetch();
    
    if (!$existing_record) {
        throw new Exception('Record not found');
    }
    
    // AUTHORIZATION CHECK: Verify user has permission to update this record
    // Users can only update their own records unless they are admins
    $current_user = get_auth_user();
    if ($existing_record['created_by'] != $current_user['id'] && !is_admin()) {
        log_security_event('unauthorized_access_attempt', "Attempted to update record ID: $record_id without permission", 'warning');
        throw new Exception('Access denied. You do not have permission to update this record.');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Sanitize and validate inputs
    $form_date = sanitize($_POST['form_date'] ?? '');
    if (!validate_date($form_date)) {
        throw new Exception('Invalid form date');
    }
    
    // Basic Information
    $departure_time = !empty($_POST['departure_time']) ? sanitize($_POST['departure_time']) : null;
    $arrival_time = !empty($_POST['arrival_time']) ? sanitize($_POST['arrival_time']) : null;
    $vehicle_used = !empty($_POST['vehicle_used']) ? sanitize($_POST['vehicle_used']) : null;
    $vehicle_details = !empty($_POST['vehicle_details']) ? sanitize($_POST['vehicle_details']) : null;
    $driver_name = !empty($_POST['driver_name']) ? sanitize($_POST['driver_name']) : null;
    $arrival_station_time = !empty($_POST['arrival_station_time']) ? sanitize($_POST['arrival_station_time']) : null;

    // Scene Information
    $arrival_scene_location = !empty($_POST['arrival_scene_location']) ? sanitize($_POST['arrival_scene_location']) : null;
    $arrival_scene_time = !empty($_POST['arrival_scene_time']) ? sanitize($_POST['arrival_scene_time']) : null;
    $departure_scene_location = !empty($_POST['departure_scene_location']) ? sanitize($_POST['departure_scene_location']) : null;
    $departure_scene_time = !empty($_POST['departure_scene_time']) ? sanitize($_POST['departure_scene_time']) : null;

    // Hospital Information
    $arrival_hospital_name = !empty($_POST['arrival_hospital_name']) ? sanitize($_POST['arrival_hospital_name']) : null;
    $arrival_hospital_time = !empty($_POST['arrival_hospital_time']) ? sanitize($_POST['arrival_hospital_time']) : null;
    $departure_hospital_location = !empty($_POST['departure_hospital_location']) ? sanitize($_POST['departure_hospital_location']) : null;
    $departure_hospital_time = !empty($_POST['departure_hospital_time']) ? sanitize($_POST['departure_hospital_time']) : null;

    // Persons Present
    $persons_present = isset($_POST['persons_present']) ? $_POST['persons_present'] : [];
    if (!is_array($persons_present)) {
        $persons_present = [$persons_present];
    }
    $persons_present = array_map('sanitize', $persons_present);
    $persons_present_json = json_encode($persons_present);

    // Patient Information (REQUIRED)
    $patient_name = sanitize($_POST['patient_name'] ?? '');
    $date_of_birth = sanitize($_POST['date_of_birth'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $gender = sanitize($_POST['gender'] ?? '');
    $civil_status = !empty($_POST['civil_status']) ? sanitize($_POST['civil_status']) : null;

    if (empty($patient_name) || empty($date_of_birth) || $age <= 0 || empty($gender)) {
        throw new Exception('Patient information is required');
    }

    if (!validate_date($date_of_birth)) {
        throw new Exception('Invalid date of birth');
    }

    if (!in_array($gender, ['male', 'female'])) {
        throw new Exception('Invalid gender value');
    }

    $address = !empty($_POST['address']) ? sanitize($_POST['address']) : null;
    $zone = !empty($_POST['zone']) ? sanitize($_POST['zone']) : null;
    $occupation = !empty($_POST['occupation']) ? sanitize($_POST['occupation']) : null;
    $place_of_incident = !empty($_POST['place_of_incident']) ? sanitize($_POST['place_of_incident']) : null;
    $zone_landmark = !empty($_POST['zone_landmark']) ? sanitize($_POST['zone_landmark']) : null;
    $incident_time = !empty($_POST['incident_time']) ? sanitize($_POST['incident_time']) : null;

    // Informant Details
    $informant_name = !empty($_POST['informant_name']) ? sanitize($_POST['informant_name']) : null;
    $informant_address = !empty($_POST['informant_address']) ? sanitize($_POST['informant_address']) : null;
    $arrival_type = !empty($_POST['arrival_type']) ? sanitize($_POST['arrival_type']) : null;
    $call_arrival_time = !empty($_POST['call_arrival_time']) ? sanitize($_POST['call_arrival_time']) : null;
    $contact_number = !empty($_POST['contact_number']) ? sanitize($_POST['contact_number']) : null;
    $relationship_victim = !empty($_POST['relationship_victim']) ? sanitize($_POST['relationship_victim']) : null;

    // Personal Belongings
    $personal_belongings = isset($_POST['personal_belongings']) ? $_POST['personal_belongings'] : [];
    if (!is_array($personal_belongings)) {
        $personal_belongings = [$personal_belongings];
    }
    $personal_belongings = array_map('sanitize', $personal_belongings);
    $personal_belongings_json = json_encode($personal_belongings);
    $other_belongings = !empty($_POST['other_belongings']) ? sanitize($_POST['other_belongings']) : null;

    // Emergency Call Types
    $emergency_medical = isset($_POST['emergency_type']) && in_array('medical', $_POST['emergency_type']) ? 1 : 0;
    $emergency_medical_details = !empty($_POST['emergency_medical_details']) ? sanitize($_POST['emergency_medical_details']) : null;
    $emergency_trauma = isset($_POST['emergency_type']) && in_array('trauma', $_POST['emergency_type']) ? 1 : 0;
    $emergency_trauma_details = !empty($_POST['emergency_trauma_details']) ? sanitize($_POST['emergency_trauma_details']) : null;
    $emergency_ob = isset($_POST['emergency_type']) && in_array('ob', $_POST['emergency_type']) ? 1 : 0;
    $emergency_ob_details = !empty($_POST['emergency_ob_details']) ? sanitize($_POST['emergency_ob_details']) : null;
    $emergency_general = isset($_POST['emergency_type']) && in_array('general', $_POST['emergency_type']) ? 1 : 0;
    $emergency_general_details = !empty($_POST['emergency_general_details']) ? sanitize($_POST['emergency_general_details']) : null;

    // Care Management
    $care_management = isset($_POST['care_management']) ? $_POST['care_management'] : [];
    if (!is_array($care_management)) {
        $care_management = [$care_management];
    }
    $care_management = array_map('sanitize', $care_management);
    $care_management_json = json_encode($care_management);
    $oxygen_lpm = !empty($_POST['oxygen_lpm']) ? sanitize($_POST['oxygen_lpm']) : null;
    $other_care = !empty($_POST['other_care']) ? sanitize($_POST['other_care']) : null;

    // Initial Vitals - Handle empty values properly
    $initial_time = !empty($_POST['initial_time']) ? sanitize($_POST['initial_time']) : null;
    $initial_bp = !empty($_POST['initial_bp']) ? sanitize($_POST['initial_bp']) : null;
    $initial_temp = (!empty($_POST['initial_temp']) && $_POST['initial_temp'] !== '') ? (float)$_POST['initial_temp'] : null;
    $initial_pulse = (!empty($_POST['initial_pulse']) && $_POST['initial_pulse'] !== '') ? (int)$_POST['initial_pulse'] : null;
    $initial_resp_rate = (!empty($_POST['initial_resp_rate']) && $_POST['initial_resp_rate'] !== '') ? (int)$_POST['initial_resp_rate'] : null;
    $initial_pain_score = (!empty($_POST['initial_pain_score']) && $_POST['initial_pain_score'] !== '') ? (int)$_POST['initial_pain_score'] : null;
    $initial_spo2 = (!empty($_POST['initial_spo2']) && $_POST['initial_spo2'] !== '') ? (int)$_POST['initial_spo2'] : null;
    $initial_spinal_injury = !empty($_POST['initial_spinal_injury']) ? sanitize($_POST['initial_spinal_injury']) : null;
    $initial_consciousness = !empty($_POST['initial_consciousness']) ? sanitize($_POST['initial_consciousness']) : null;
    $initial_helmet = !empty($_POST['initial_helmet']) ? sanitize($_POST['initial_helmet']) : null;

    // Follow-up Vitals
    $followup_time = !empty($_POST['followup_time']) ? sanitize($_POST['followup_time']) : null;
    $followup_bp = !empty($_POST['followup_bp']) ? sanitize($_POST['followup_bp']) : null;
    $followup_temp = (!empty($_POST['followup_temp']) && $_POST['followup_temp'] !== '') ? (float)$_POST['followup_temp'] : null;
    $followup_pulse = (!empty($_POST['followup_pulse']) && $_POST['followup_pulse'] !== '') ? (int)$_POST['followup_pulse'] : null;
    $followup_resp_rate = (!empty($_POST['followup_resp_rate']) && $_POST['followup_resp_rate'] !== '') ? (int)$_POST['followup_resp_rate'] : null;
    $followup_pain_score = (!empty($_POST['followup_pain_score']) && $_POST['followup_pain_score'] !== '') ? (int)$_POST['followup_pain_score'] : null;
    $followup_spo2 = (!empty($_POST['followup_spo2']) && $_POST['followup_spo2'] !== '') ? (int)$_POST['followup_spo2'] : null;
    $followup_spinal_injury = !empty($_POST['followup_spinal_injury']) ? sanitize($_POST['followup_spinal_injury']) : null;
    $followup_consciousness = !empty($_POST['followup_consciousness']) ? sanitize($_POST['followup_consciousness']) : null;

    // Chief Complaints
    $chief_complaints = isset($_POST['chief_complaints']) ? $_POST['chief_complaints'] : [];
    if (!is_array($chief_complaints)) {
        $chief_complaints = [$chief_complaints];
    }
    $chief_complaints = array_map('sanitize', $chief_complaints);
    $chief_complaints_json = json_encode($chief_complaints);
    $other_complaints = !empty($_POST['other_complaints']) ? sanitize($_POST['other_complaints']) : null;

    // FAST Assessment
    $fast_face_drooping = !empty($_POST['fast_face_drooping']) ? sanitize($_POST['fast_face_drooping']) : null;
    $fast_arm_weakness = !empty($_POST['fast_arm_weakness']) ? sanitize($_POST['fast_arm_weakness']) : null;
    $fast_speech_difficulty = !empty($_POST['fast_speech_difficulty']) ? sanitize($_POST['fast_speech_difficulty']) : null;
    $fast_time_to_call = !empty($_POST['fast_time_to_call']) ? sanitize($_POST['fast_time_to_call']) : null;
    $fast_sample_details = !empty($_POST['fast_sample_details']) ? sanitize($_POST['fast_sample_details']) : null;

    // OB Information
    $ob_baby_status = !empty($_POST['ob_baby_status']) ? sanitize($_POST['ob_baby_status']) : null;
    $ob_delivery_time = !empty($_POST['ob_delivery_time']) ? sanitize($_POST['ob_delivery_time']) : null;
    $ob_placenta = !empty($_POST['ob_placenta']) ? sanitize($_POST['ob_placenta']) : null;
    $ob_lmp = !empty($_POST['ob_lmp']) ? sanitize($_POST['ob_lmp']) : null;
    $ob_aog = !empty($_POST['ob_aog']) ? sanitize($_POST['ob_aog']) : null;
    $ob_edc = !empty($_POST['ob_edc']) ? sanitize($_POST['ob_edc']) : null;

    // Team Information
    $team_leader_notes = !empty($_POST['team_leader_notes']) ? sanitize($_POST['team_leader_notes']) : null;
    $team_leader = !empty($_POST['team_leader']) ? sanitize($_POST['team_leader']) : null;
    $data_recorder = !empty($_POST['data_recorder']) ? sanitize($_POST['data_recorder']) : null;
    $logistic = !empty($_POST['logistic']) ? sanitize($_POST['logistic']) : null;
    $first_aider = !empty($_POST['first_aider']) ? sanitize($_POST['first_aider']) : null;
    $second_aider = !empty($_POST['second_aider']) ? sanitize($_POST['second_aider']) : null;

    // Hospital Endorsement
    $endorsement = !empty($_POST['endorsement']) ? sanitize($_POST['endorsement']) : null;
    $hospital_name = !empty($_POST['hospital_name']) ? sanitize($_POST['hospital_name']) : null;
    $received_by = !empty($_POST['received_by_signature']) ? sanitize($_POST['received_by_signature']) : null;
    $endorsement_datetime = !empty($_POST['endorsement_datetime']) ? sanitize($_POST['endorsement_datetime']) : null;

    // Waiver
    $waiver_patient_signature = !empty($_POST['patient_signature']) ? sanitize($_POST['patient_signature']) : null;
    $waiver_witness_signature = !empty($_POST['witness_signature']) ? sanitize($_POST['witness_signature']) : null;
    
    // Update main form
    $sql = "UPDATE prehospital_forms SET
        form_date = ?,
        departure_time = ?,
        arrival_time = ?,
        vehicle_used = ?,
        vehicle_details = ?,
        driver_name = ?,
        arrival_station_time = ?,
        arrival_scene_location = ?,
        arrival_scene_time = ?,
        departure_scene_location = ?,
        departure_scene_time = ?,
        arrival_hospital_name = ?,
        arrival_hospital_time = ?,
        departure_hospital_location = ?,
        departure_hospital_time = ?,
        persons_present = ?,
        patient_name = ?,
        date_of_birth = ?,
        age = ?,
        gender = ?,
        civil_status = ?,
        address = ?,
        zone = ?,
        occupation = ?,
        place_of_incident = ?,
        zone_landmark = ?,
        incident_time = ?,
        informant_name = ?,
        informant_address = ?,
        arrival_type = ?,
        call_arrival_time = ?,
        contact_number = ?,
        relationship_victim = ?,
        personal_belongings = ?,
        other_belongings = ?,
        emergency_medical = ?,
        emergency_medical_details = ?,
        emergency_trauma = ?,
        emergency_trauma_details = ?,
        emergency_ob = ?,
        emergency_ob_details = ?,
        emergency_general = ?,
        emergency_general_details = ?,
        care_management = ?,
        oxygen_lpm = ?,
        other_care = ?,
        initial_time = ?,
        initial_bp = ?,
        initial_temp = ?,
        initial_pulse = ?,
        initial_resp_rate = ?,
        initial_pain_score = ?,
        initial_spo2 = ?,
        initial_spinal_injury = ?,
        initial_consciousness = ?,
        initial_helmet = ?,
        followup_time = ?,
        followup_bp = ?,
        followup_temp = ?,
        followup_pulse = ?,
        followup_resp_rate = ?,
        followup_pain_score = ?,
        followup_spo2 = ?,
        followup_spinal_injury = ?,
        followup_consciousness = ?,
        chief_complaints = ?,
        other_complaints = ?,
        fast_face_drooping = ?,
        fast_arm_weakness = ?,
        fast_speech_difficulty = ?,
        fast_time_to_call = ?,
        fast_sample_details = ?,
        ob_baby_status = ?,
        ob_delivery_time = ?,
        ob_placenta = ?,
        ob_lmp = ?,
        ob_aog = ?,
        ob_edc = ?,
        team_leader_notes = ?,
        team_leader = ?,
        data_recorder = ?,
        logistic = ?,
        first_aider = ?,
        second_aider = ?,
        endorsement = ?,
        hospital_name = ?,
        received_by = ?,
        endorsement_datetime = ?,
        waiver_patient_signature = ?,
        waiver_witness_signature = ?,
        updated_at = NOW()
        WHERE id = ?";

    $params = [
        $form_date,
        $departure_time,
        $arrival_time,
        $vehicle_used,
        $vehicle_details,
        $driver_name,
        $arrival_station_time,
        $arrival_scene_location,
        $arrival_scene_time,
        $departure_scene_location,
        $departure_scene_time,
        $arrival_hospital_name,
        $arrival_hospital_time,
        $departure_hospital_location,
        $departure_hospital_time,
        $persons_present_json,
        $patient_name,
        $date_of_birth,
        $age,
        $gender,
        $civil_status,
        $address,
        $zone,
        $occupation,
        $place_of_incident,
        $zone_landmark,
        $incident_time,
        $informant_name,
        $informant_address,
        $arrival_type,
        $call_arrival_time,
        $contact_number,
        $relationship_victim,
        $personal_belongings_json,
        $other_belongings,
        $emergency_medical,
        $emergency_medical_details,
        $emergency_trauma,
        $emergency_trauma_details,
        $emergency_ob,
        $emergency_ob_details,
        $emergency_general,
        $emergency_general_details,
        $care_management_json,
        $oxygen_lpm,
        $other_care,
        $initial_time,
        $initial_bp,
        $initial_temp,
        $initial_pulse,
        $initial_resp_rate,
        $initial_pain_score,
        $initial_spo2,
        $initial_spinal_injury,
        $initial_consciousness,
        $initial_helmet,
        $followup_time,
        $followup_bp,
        $followup_temp,
        $followup_pulse,
        $followup_resp_rate,
        $followup_pain_score,
        $followup_spo2,
        $followup_spinal_injury,
        $followup_consciousness,
        $chief_complaints_json,
        $other_complaints,
        $fast_face_drooping,
        $fast_arm_weakness,
        $fast_speech_difficulty,
        $fast_time_to_call,
        $fast_sample_details,
        $ob_baby_status,
        $ob_delivery_time,
        $ob_placenta,
        $ob_lmp,
        $ob_aog,
        $ob_edc,
        $team_leader_notes,
        $team_leader,
        $data_recorder,
        $logistic,
        $first_aider,
        $second_aider,
        $endorsement,
        $hospital_name,
        $received_by,
        $endorsement_datetime,
        $waiver_patient_signature,
        $waiver_witness_signature,
        $record_id
    ];

    $stmt = db_query($sql, $params);
    
    if (!$stmt) {
        throw new Exception('Failed to update record');
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Log activity
    log_activity('form_updated', "Updated form: {$existing_record['form_number']} for patient: $patient_name");
    
    // Success response
    set_flash('Record updated successfully!', 'success');
    redirect('../public/view_record.php?id=' . $record_id);
    
} catch (Exception $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Update Record Error: " . $e->getMessage());
    
    set_flash('Error updating record: ' . $e->getMessage(), 'error');
    redirect('../public/records.php');
}
