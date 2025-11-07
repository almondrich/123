<!-- View Record Modal -->
<div class="modal fade" id="viewRecordModal" tabindex="-1" aria-labelledby="viewRecordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; border: none;">
                <div>
                    <h5 class="modal-title mb-1" id="viewRecordModalLabel">
                        <i class="bi bi-file-earmark-medical me-2"></i>Pre-Hospital Care Record
                    </h5>
                    <small class="text-white-50" id="modalFormNumber">Loading...</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalRecordContent" style="background-color: #f8f9fa;">
                <!-- Loading spinner -->
                <div class="text-center py-5" id="modalLoading">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted fw-500">Loading record details...</p>
                </div>

                <!-- Record content will be inserted here -->
                <div id="recordData" style="display: none;"></div>
            </div>
            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Close
                </button>
                <a href="#" id="modalPrintBtn" class="btn btn-outline-info" style="display: none;" onclick="printRecord()">
                    <i class="bi bi-printer me-1"></i>Print
                </a>
                <a href="#" id="modalEditBtn" class="btn btn-primary" style="display: none;">
                    <i class="bi bi-pencil me-1"></i>Edit Record
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.modal-xl {
    max-width: 1140px;
}

#viewRecordModal .modal-content {
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}

.record-section {
    background: white;
    border-radius: 10px;
    padding: 24px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
}

.record-section-title {
    color: #2c3e50;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 8px;
}

.record-section-title i {
    color: #3498db;
    font-size: 1.2rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 0.95rem;
    color: #2c3e50;
    font-weight: 500;
}

.info-value.empty {
    color: #adb5bd;
    font-style: italic;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.85rem;
    gap: 6px;
}

.status-badge i {
    font-size: 1rem;
}

.status-completed {
    background-color: #d1e7dd;
    color: #0a3622;
    border: 1px solid #badbcc;
}

.status-pending {
    background-color: #fff3cd;
    color: #664d03;
    border: 1px solid #ffe69c;
}

.status-draft {
    background-color: #cfe2ff;
    color: #084298;
    border: 1px solid #b6d4fe;
}

.text-section {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.text-section .info-value {
    line-height: 1.6;
    white-space: pre-wrap;
}

@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .modal-xl {
        max-width: 96%;
        margin: 10px auto;
    }

    .record-section {
        padding: 16px;
        margin-bottom: 12px;
    }

    .record-section-title {
        font-size: 1rem;
    }
}
</style>
