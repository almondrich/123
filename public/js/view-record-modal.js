/**
 * View Record Modal JavaScript
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

    // Update edit button link
    document.getElementById('modalEditBtn').href = `edit_record.php?id=${record.id}`;

    // Status badge
    const statusClass = {
        'completed': 'status-completed',
        'pending': 'status-pending',
        'draft': 'status-draft'
    }[record.status] || 'status-draft';

    // Build HTML content
    const html = `
        <div class="record-section">
            <h5><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Form Number</div>
                    <div class="record-field-value"><strong>${escapeHtml(record.form_number)}</strong></div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Form Date</div>
                    <div class="record-field-value">${formatDate(record.form_date)}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Status</div>
                    <div class="record-field-value">
                        <span class="status-badge ${statusClass}">${capitalizeFirst(record.status)}</span>
                    </div>
                </div>
            </div>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Created By</div>
                    <div class="record-field-value">
                        <i class="bi bi-person-circle me-1"></i>${escapeHtml(record.created_by_name || 'Unknown')}
                    </div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Created At</div>
                    <div class="record-field-value">${formatDateTime(record.created_at)}</div>
                </div>
            </div>
        </div>

        <div class="record-section">
            <h5><i class="bi bi-person me-2"></i>Patient Information</h5>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Patient Name</div>
                    <div class="record-field-value">${escapeHtml(record.patient_name)}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Age</div>
                    <div class="record-field-value">${escapeHtml(record.age)}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Gender</div>
                    <div class="record-field-value">${capitalizeFirst(record.gender)}</div>
                </div>
            </div>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Address</div>
                    <div class="record-field-value">${escapeHtml(record.address || '-')}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Contact Number</div>
                    <div class="record-field-value">${escapeHtml(record.contact_number || '-')}</div>
                </div>
            </div>
        </div>

        <div class="record-section">
            <h5><i class="bi bi-geo-alt me-2"></i>Incident Details</h5>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Place of Incident</div>
                    <div class="record-field-value">${escapeHtml(record.place_of_incident || '-')}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Nature of Call</div>
                    <div class="record-field-value">${escapeHtml(record.nature_of_call || '-')}</div>
                </div>
            </div>
        </div>

        <div class="record-section">
            <h5><i class="bi bi-clock me-2"></i>Timeline</h5>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Call Time</div>
                    <div class="record-field-value">${formatTime(record.call_received_time)}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Dispatch Time</div>
                    <div class="record-field-value">${formatTime(record.dispatch_time)}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Arrival Scene Time</div>
                    <div class="record-field-value">${formatTime(record.arrival_scene_time)}</div>
                </div>
            </div>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Departure Scene Time</div>
                    <div class="record-field-value">${formatTime(record.departure_scene_time)}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Arrival Station Time</div>
                    <div class="record-field-value">${formatTime(record.arrival_station_time)}</div>
                </div>
            </div>
        </div>

        <div class="record-section">
            <h5><i class="bi bi-hospital me-2"></i>Hospital & Transport</h5>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Hospital Name</div>
                    <div class="record-field-value">${escapeHtml(record.arrival_hospital_name || '-')}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Vehicle Used</div>
                    <div class="record-field-value">${capitalizeFirst(record.vehicle_used || '-')}</div>
                </div>
            </div>
        </div>

        <div class="record-section">
            <h5><i class="bi bi-heart-pulse me-2"></i>Vital Signs</h5>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Blood Pressure</div>
                    <div class="record-field-value">${escapeHtml(record.blood_pressure || '-')}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Heart Rate</div>
                    <div class="record-field-value">${escapeHtml(record.heart_rate || '-')}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Respiratory Rate</div>
                    <div class="record-field-value">${escapeHtml(record.respiratory_rate || '-')}</div>
                </div>
            </div>
            <div class="record-row">
                <div class="record-field">
                    <div class="record-field-label">Temperature</div>
                    <div class="record-field-value">${escapeHtml(record.temperature || '-')}</div>
                </div>
                <div class="record-field">
                    <div class="record-field-label">Oxygen Saturation</div>
                    <div class="record-field-value">${escapeHtml(record.oxygen_saturation || '-')}</div>
                </div>
            </div>
        </div>

        ${record.chief_complaint ? `
        <div class="record-section">
            <h5><i class="bi bi-clipboard-pulse me-2"></i>Chief Complaint</h5>
            <div class="record-field">
                <div class="record-field-value">${escapeHtml(record.chief_complaint)}</div>
            </div>
        </div>
        ` : ''}

        ${record.treatment_provided ? `
        <div class="record-section">
            <h5><i class="bi bi-bandaid me-2"></i>Treatment Provided</h5>
            <div class="record-field">
                <div class="record-field-value">${escapeHtml(record.treatment_provided)}</div>
            </div>
        </div>
        ` : ''}

        ${record.notes ? `
        <div class="record-section">
            <h5><i class="bi bi-journal-text me-2"></i>Additional Notes</h5>
            <div class="record-field">
                <div class="record-field-value">${escapeHtml(record.notes)}</div>
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
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>${escapeHtml(message)}
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
 * Helper: Escape HTML
 */
function escapeHtml(text) {
    if (!text) return '-';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Helper: Capitalize first letter
 */
function capitalizeFirst(text) {
    if (!text) return '-';
    return text.charAt(0).toUpperCase() + text.slice(1);
}

/**
 * Helper: Format date
 */
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

/**
 * Helper: Format date time
 */
function formatDateTime(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Helper: Format time
 */
function formatTime(timeStr) {
    if (!timeStr) return '-';
    return timeStr;
}
