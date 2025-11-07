/**
 * View Record Modal JavaScript - Simplified for actual form fields
 * Handles fetching and displaying record data in a modal
 */

let currentRecordId = null;

/**
 * Open view record modal
 */
function viewRecordModal(recordId) {
    currentRecordId = recordId;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('viewRecordModal'));
    modal.show();

    // Show loading state
    document.getElementById('modalLoading').style.display = 'block';
    document.getElementById('recordData').style.display = 'none';
    document.getElementById('modalEditBtn').style.display = 'none';
    document.getElementById('modalPrintBtn').style.display = 'none';
    document.getElementById('modalFormNumber').textContent = 'Loading...';

    // Fetch record data
    fetch(`api/get_record.php?id=${recordId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRecordData(data.record);
            } else {
                showError(data.message || 'Error loading record');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Failed to load record data');
        });
}

/**
 * Display record data in modal
 */
function displayRecordData(record) {
    // Hide loading
    document.getElementById('modalLoading').style.display = 'none';
    document.getElementById('recordData').style.display = 'block';
    document.getElementById('modalEditBtn').style.display = 'inline-block';
    document.getElementById('modalPrintBtn').style.display = 'inline-block';

    // Update form number in header
    document.getElementById('modalFormNumber').textContent = `Form #${record.form_number || 'N/A'}`;

    // Update edit button link
    document.getElementById('modalEditBtn').href = `edit_record.php?id=${record.id}`;

    // Status badge
    const statusIcons = {
        'completed': 'bi-check-circle-fill',
        'pending': 'bi-clock-fill',
        'draft': 'bi-file-earmark-fill'
    };
    const statusClass = {
        'completed': 'status-completed',
        'pending': 'status-pending',
        'draft': 'status-draft'
    }[record.status] || 'status-draft';
    const statusIcon = statusIcons[record.status] || 'bi-file-earmark-fill';

    // Build HTML content with cleaner structure - showing only actual form fields
    const html = `
        <!-- Basic Information -->
        <div class="record-section">
            <h6 class="record-section-title">
                <i class="bi bi-info-circle"></i>
                Basic Information
            </h6>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Form Number</span>
                    <span class="info-value">${safe(record.form_number)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Form Date</span>
                    <span class="info-value">${formatDate(record.form_date)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        <span class="status-badge ${statusClass}">
                            <i class="bi ${statusIcon}"></i>
                            ${capitalize(record.status)}
                        </span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Departure Time</span>
                    <span class="info-value">${formatTime(record.departure_time)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Arrival Time</span>
                    <span class="info-value">${formatTime(record.arrival_time)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Vehicle Used</span>
                    <span class="info-value">${capitalize(record.vehicle_used)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Driver Name</span>
                    <span class="info-value">${safe(record.driver_name)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Created By</span>
                    <span class="info-value">
                        <i class="bi bi-person-circle me-1" style="color: #3498db;"></i>${safe(record.created_by_name)}
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Created At</span>
                    <span class="info-value">${formatDateTime(record.created_at)}</span>
                </div>
            </div>
        </div>

        <!-- Patient Information -->
        <div class="record-section">
            <h6 class="record-section-title">
                <i class="bi bi-person"></i>
                Patient Information
            </h6>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Patient Name</span>
                    <span class="info-value">${safe(record.patient_name)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date of Birth</span>
                    <span class="info-value">${formatDate(record.date_of_birth)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Age</span>
                    <span class="info-value">${safe(record.age)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Gender</span>
                    <span class="info-value">${capitalize(record.gender)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Civil Status</span>
                    <span class="info-value">${capitalize(record.civil_status)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Occupation</span>
                    <span class="info-value">${safe(record.occupation)}</span>
                </div>
                <div class="info-item" style="grid-column: span 2;">
                    <span class="info-label">Address</span>
                    <span class="info-value">${safe(record.address)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Zone</span>
                    <span class="info-value">${safe(record.zone)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Contact Number</span>
                    <span class="info-value">${safe(record.contact_number)}</span>
                </div>
            </div>
        </div>

        <!-- Location Information -->
        <div class="record-section">
            <h6 class="record-section-title">
                <i class="bi bi-geo-alt"></i>
                Location & Timeline
            </h6>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Arrival Scene Location</span>
                    <span class="info-value">${safe(record.arrival_scene_location)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Arrival Scene Time</span>
                    <span class="info-value">${formatTime(record.arrival_scene_time)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Departure Scene Location</span>
                    <span class="info-value">${safe(record.departure_scene_location)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Departure Scene Time</span>
                    <span class="info-value">${formatTime(record.departure_scene_time)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Hospital Name</span>
                    <span class="info-value">${safe(record.arrival_hospital_name)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Hospital Arrival Time</span>
                    <span class="info-value">${formatTime(record.arrival_hospital_time)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Hospital Departure Time</span>
                    <span class="info-value">${formatTime(record.departure_hospital_time)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Station Arrival Time</span>
                    <span class="info-value">${formatTime(record.arrival_station_time)}</span>
                </div>
            </div>
        </div>

        <!-- Incident Information -->
        <div class="record-section">
            <h6 class="record-section-title">
                <i class="bi bi-exclamation-triangle"></i>
                Incident Details
            </h6>
            <div class="info-grid">
                <div class="info-item" style="grid-column: span 2;">
                    <span class="info-label">Place of Incident</span>
                    <span class="info-value">${safe(record.place_of_incident)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Zone/Landmark</span>
                    <span class="info-value">${safe(record.zone_landmark)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Incident Time</span>
                    <span class="info-value">${formatTime(record.incident_time)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Informant Name</span>
                    <span class="info-value">${safe(record.informant_name)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Informant Address</span>
                    <span class="info-value">${safe(record.informant_address)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Arrival Type</span>
                    <span class="info-value">${capitalize(record.arrival_type)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Call Arrival Time</span>
                    <span class="info-value">${formatTime(record.call_arrival_time)}</span>
                </div>
            </div>
        </div>

        <!-- Initial Vital Signs -->
        <div class="record-section">
            <h6 class="record-section-title">
                <i class="bi bi-heart-pulse"></i>
                Initial Vital Signs
            </h6>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Time</span>
                    <span class="info-value">${formatTime(record.initial_time)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Blood Pressure</span>
                    <span class="info-value">${safe(record.initial_bp)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Temperature (°C)</span>
                    <span class="info-value">${safe(record.initial_temp)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pulse (bpm)</span>
                    <span class="info-value">${safe(record.initial_pulse)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Respiratory Rate</span>
                    <span class="info-value">${safe(record.initial_resp_rate)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pain Score (0-10)</span>
                    <span class="info-value">${safe(record.initial_pain_score)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">SPO2 (%)</span>
                    <span class="info-value">${safe(record.initial_spo2)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Spinal Injury</span>
                    <span class="info-value">${capitalize(record.initial_spinal_injury)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Consciousness</span>
                    <span class="info-value">${capitalize(record.initial_consciousness)}</span>
                </div>
            </div>
        </div>

        ${record.followup_time ? `
        <!-- Follow-up Vital Signs -->
        <div class="record-section">
            <h6 class="record-section-title">
                <i class="bi bi-arrow-repeat"></i>
                Follow-up Vital Signs
            </h6>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Time</span>
                    <span class="info-value">${formatTime(record.followup_time)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Blood Pressure</span>
                    <span class="info-value">${safe(record.followup_bp)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Temperature (°C)</span>
                    <span class="info-value">${safe(record.followup_temp)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pulse (bpm)</span>
                    <span class="info-value">${safe(record.followup_pulse)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Respiratory Rate</span>
                    <span class="info-value">${safe(record.followup_resp_rate)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pain Score (0-10)</span>
                    <span class="info-value">${safe(record.followup_pain_score)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">SPO2 (%)</span>
                    <span class="info-value">${safe(record.followup_spo2)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Consciousness</span>
                    <span class="info-value">${capitalize(record.followup_consciousness)}</span>
                </div>
            </div>
        </div>
        ` : ''}

        <!-- Team Information -->
        <div class="record-section">
            <h6 class="record-section-title">
                <i class="bi bi-people"></i>
                Team Information
            </h6>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Team Leader</span>
                    <span class="info-value">${safe(record.team_leader)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data Recorder</span>
                    <span class="info-value">${safe(record.data_recorder)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Logistic</span>
                    <span class="info-value">${safe(record.logistic)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">First Aider</span>
                    <span class="info-value">${safe(record.first_aider)}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Second Aider</span>
                    <span class="info-value">${safe(record.second_aider)}</span>
                </div>
            </div>
        </div>

        ${record.team_leader_notes ? `
        <!-- Team Leader Notes -->
        <div class="record-section">
            <h6 class="record-section-title">
                <i class="bi bi-journal-text"></i>
                Team Leader Notes
            </h6>
            <div class="text-section">
                <div class="info-value">${safe(record.team_leader_notes)}</div>
            </div>
        </div>
        ` : ''}
    `;

    document.getElementById('recordData').innerHTML = html;
}

/**
 * Show error message in modal
 */
function showError(message) {
    document.getElementById('modalLoading').style.display = 'none';
    document.getElementById('recordData').style.display = 'block';
    document.getElementById('recordData').innerHTML = `
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 1.5rem;"></i>
            <div>${safe(message)}</div>
        </div>
    `;
}

/**
 * Print record
 */
function printRecord() {
    if (currentRecordId) {
        window.open(`view_record.php?id=${currentRecordId}`, '_blank');
    }
}

/**
 * Helper: Safe output (returns empty string or value)
 */
function safe(value) {
    if (value === null || value === undefined || value === '') {
        return '<span class="info-value empty">Not provided</span>';
    }
    return escapeHtml(String(value));
}

/**
 * Helper: Escape HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Helper: Capitalize first letter
 */
function capitalize(text) {
    if (!text) return safe(text);
    return escapeHtml(text.charAt(0).toUpperCase() + text.slice(1));
}

/**
 * Helper: Format date (e.g., Jan 15, 2025)
 */
function formatDate(dateStr) {
    if (!dateStr) return safe(dateStr);
    try {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch (e) {
        return safe(dateStr);
    }
}

/**
 * Helper: Format date time (e.g., Jan 15, 2025, 10:30 AM)
 */
function formatDateTime(dateStr) {
    if (!dateStr) return safe(dateStr);
    try {
        const date = new Date(dateStr);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return safe(dateStr);
    }
}

/**
 * Helper: Format time (e.g., 10:30 AM or just display as is)
 */
function formatTime(timeStr) {
    if (!timeStr) return safe(timeStr);
    // If it's already in a good format, return it
    return escapeHtml(timeStr);
}
