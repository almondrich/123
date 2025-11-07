<!-- View Record Modal -->
<div class="modal fade" id="viewRecordModal" tabindex="-1" aria-labelledby="viewRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2c3e50; color: white;">
                <h5 class="modal-title" id="viewRecordModalLabel">
                    <i class="bi bi-file-earmark-medical me-2"></i>Pre-Hospital Care Record
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalRecordContent">
                <!-- Loading spinner -->
                <div class="text-center py-5" id="modalLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading record...</p>
                </div>

                <!-- Record content will be inserted here -->
                <div id="recordData" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
                <a href="#" id="modalEditBtn" class="btn btn-warning" style="display: none;">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <a href="#" id="modalPrintBtn" class="btn btn-info" style="display: none;" onclick="printRecord()">
                    <i class="bi bi-printer me-1"></i>Print
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.modal-xl {
    max-width: 1200px;
}

.record-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 4px solid #0d6efd;
}

.record-section h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #dee2e6;
}

.record-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 10px;
}

.record-field {
    background: white;
    padding: 12px;
    border-radius: 6px;
    border: 1px solid #e0e0e0;
}

.record-field-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.85rem;
    margin-bottom: 5px;
    text-transform: uppercase;
}

.record-field-value {
    color: #212529;
    font-size: 0.95rem;
    word-break: break-word;
}

.status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
}

.status-completed {
    background-color: #d1e7dd;
    color: #0f5132;
}

.status-pending {
    background-color: #fff3cd;
    color: #664d03;
}

.status-draft {
    background-color: #cfe2ff;
    color: #084298;
}

@media (max-width: 768px) {
    .record-row {
        grid-template-columns: 1fr;
    }

    .modal-xl {
        max-width: 95%;
    }
}
</style>
