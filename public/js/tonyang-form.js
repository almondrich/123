// Pre-Hospital Care Form JavaScript
// Form navigation and interaction logic

let currentTab = 0;
let totalTabs = 7;

// Adjust for edit form which has 6 tabs
if (document.getElementById('editForm')) {
    totalTabs = 6;
}

// Body Diagram Variables
let injuries = [];
let injuryCounter = 0;
let selectedInjuryType = 'laceration';

document.addEventListener('DOMContentLoaded', function() {
    updateNavigation();
    updateProgress();
    setupInjuryTypeButtons();
    setupBodyDiagrams();
    initializeAmbulanceList();
    setupVehicleModals();

    // Initialize progress bar for edit form
    if (document.getElementById('editForm')) {
        const progressBar = document.getElementById('progressBar');
        if (progressBar) {
            progressBar.style.width = '16.67%'; // 1/6 tabs
        }
    }
});

// ============================================
// TAB NAVIGATION FUNCTIONS
// ============================================

function navigateTab(direction) {
    const tabs = document.querySelectorAll('.nav-link');
    
    if (direction === 1) {
        tabs[currentTab].classList.add('completed');
    }
    
    currentTab += direction;
    
    if (currentTab >= totalTabs) currentTab = totalTabs - 1;
    if (currentTab < 0) currentTab = 0;
    
    const targetTab = tabs[currentTab];
    const tabTrigger = new bootstrap.Tab(targetTab);
    tabTrigger.show();
    
    updateNavigation();
    updateProgress();
    
    document.querySelector('.form-body').scrollTop = 0;
}

function updateNavigation() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const updateBtn = document.getElementById('updateBtn');

    prevBtn.style.display = currentTab === 0 ? 'none' : 'block';

    if (document.getElementById('editForm')) {
        // Edit form navigation
        if (currentTab === totalTabs - 1) {
            nextBtn.style.display = 'none';
            updateBtn.style.display = 'block';
        } else {
            nextBtn.style.display = 'block';
            updateBtn.style.display = 'none';
        }
        submitBtn.style.display = 'none'; // Hide submit button in edit mode
    } else {
        // Create form navigation
        if (currentTab === totalTabs - 1) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'block';
        } else {
            nextBtn.style.display = 'block';
            submitBtn.style.display = 'none';
        }
        if (updateBtn) updateBtn.style.display = 'none'; // Hide update button in create mode
    }
}

function updateProgress() {
    const progress = ((currentTab + 1) / totalTabs) * 100;
    document.getElementById('progressBar').style.width = progress + '%';
}

// Tab click event listeners
document.querySelectorAll('.nav-link').forEach((tab, index) => {
    tab.addEventListener('click', function() {
        currentTab = index;
        updateNavigation();
        updateProgress();
    });
});

// ============================================
// FORM UTILITY FUNCTIONS
// ============================================

// Auto-calculate age from date of birth
document.getElementById('dateOfBirth')?.addEventListener('change', function() {
    const dob = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - dob.getFullYear();
    const monthDiff = today.getMonth() - dob.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
        age--;
    }
    
    document.getElementById('age').value = age;
});

// ============================================
// FORM SUBMISSION
// ============================================

function submitForm() {
    const formData = new FormData(document.getElementById('preHospitalForm'));
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    
    // Add structured time/location data
    data.arrivalScene = {
        location: document.getElementById('arrSceneLocation').value,
        time: document.getElementById('arrSceneTime').value
    };
    data.departureScene = {
        location: document.getElementById('depSceneLocation').value,
        time: document.getElementById('depSceneTime').value
    };
    data.arrivalHospital = {
        name: document.getElementById('arrHospName').value,
        time: document.getElementById('arrHospTime').value
    };
    data.departureHospital = {
        location: document.getElementById('depHospLocation').value,
        time: document.getElementById('depHospTime').value
    };
    
    // Get selected personal belongings
    const belongingsSelect = document.getElementById('personalBelongings');
    const selectedBelongings = Array.from(belongingsSelect.selectedOptions).map(option => option.value);
    data.personalBelongings = selectedBelongings;
    data.otherBelongingsSpecify = document.getElementById('otherBelongings').value;
    
    // Add injury data
    data.injuries = injuries;
    
    console.log('Form submitted:', data);
    
    // Submit the form
    document.getElementById('preHospitalForm').submit();
}

function printForm() {
    window.print();
}

function clearForm() {
    if (confirm('Are you sure you want to clear all form data? This action cannot be undone.')) {
        document.getElementById('preHospitalForm').reset();
        clearAllInjuries();
        currentTab = 0;
        navigateTab(0);
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.classList.remove('completed');
        });
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            navigateTab(-1);
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            navigateTab(1);
        }
    }
});

// ============================================
// BODY DIAGRAM FUNCTIONS
// ============================================

function setupInjuryTypeButtons() {
    const buttons = document.querySelectorAll('.injury-type-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            buttons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            selectedInjuryType = this.dataset.type;
        });
    });
}

function setupBodyDiagrams() {
    const frontContainer = document.getElementById('frontContainer');
    const backContainer = document.getElementById('backContainer');

    if (frontContainer) {
        frontContainer.addEventListener('click', function(e) {
            handleBodyClick(e, 'front', frontContainer);
        });
    }

    if (backContainer) {
        backContainer.addEventListener('click', function(e) {
            handleBodyClick(e, 'back', backContainer);
        });
    }
}

function handleBodyClick(e, view, container) {
    const rect = container.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    if (x < 10 || y < 10 || x > rect.width - 10 || y > rect.height - 10) {
        return;
    }

    addInjury(x, y, view, container);
}

function addInjury(x, y, view, container) {
    injuryCounter++;
    const injury = {
        id: injuryCounter,
        type: selectedInjuryType,
        x: x,
        y: y,
        view: view,
        notes: ''
    };

    injuries.push(injury);

    const marker = document.createElement('div');
    marker.className = `injury-marker ${selectedInjuryType}`;
    marker.style.left = x + 'px';
    marker.style.top = y + 'px';
    marker.textContent = injuryCounter;
    marker.dataset.id = injuryCounter;
    marker.title = `Injury #${injuryCounter} - ${selectedInjuryType}`;

    container.appendChild(marker);
    updateInjuryList();
}

function updateInjuryList() {
    const container = document.getElementById('injuryListContainer');
    const countElement = document.getElementById('injuryCount');
    
    if (!container || !countElement) return;
    
    countElement.textContent = injuries.length;

    if (injuries.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">📍</div>
                <p>No injuries marked yet.<br>Click on body to add.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = injuries.map(injury => `
        <div class="injury-item" data-injury-id="${injury.id}">
            <button class="delete-btn" onclick="deleteInjury(${injury.id})" title="Delete injury">×</button>
            <div class="injury-item-header">
                <span class="injury-number">Injury #${injury.id}</span>
                <span class="injury-type-badge ${injury.type}">${injury.type.toUpperCase()}</span>
            </div>
            <div style="font-size: 0.8rem; color: #666; margin-bottom: 0.5rem;">
                <strong>Location:</strong> ${injury.view === 'front' ? 'Front' : 'Back'}
            </div>
            <textarea class="injury-notes" placeholder="Notes about this injury..." 
                      onchange="updateInjuryNotes(${injury.id}, this.value)">${injury.notes}</textarea>
        </div>
    `).join('');
}

function updateInjuryNotes(id, notes) {
    const injury = injuries.find(i => i.id === id);
    if (injury) {
        injury.notes = notes;
    }
}

function deleteInjury(id) {
    if (confirm('Delete this injury marker?')) {
        injuries = injuries.filter(i => i.id !== id);

        const marker = document.querySelector(`.injury-marker[data-id="${id}"]`);
        if (marker) {
            marker.remove();
        }

        updateInjuryList();
    }
}

function clearAllInjuries() {
    if (injuries.length === 0) {
        return;
    }
    
    if (confirm(`Clear all ${injuries.length} injury markers?`)) {
        injuries = [];
        injuryCounter = 0;
        document.querySelectorAll('.injury-marker').forEach(m => m.remove());
        updateInjuryList();
    }
}

function exportInjuryData() {
    if (injuries.length === 0) {
        alert('No injuries to export! Please mark some injuries first.');
        return;
    }

    const data = {
        formTitle: 'Pre-Hospital Care - Injury Assessment',
        timestamp: new Date().toISOString(),
        totalInjuries: injuries.length,
        injuries: injuries.map(i => ({
            injuryNumber: i.id,
            type: i.type,
            view: i.view,
            coordinates: { x: Math.round(i.x), y: Math.round(i.y) },
            notes: i.notes || 'No notes provided'
        }))
    };

    const dataStr = JSON.stringify(data, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `injury-assessment-${Date.now()}.json`;
    link.click();

    alert(`✅ Successfully exported ${injuries.length} injuries!`);
}

// ============================================
// VEHICLE MODAL FUNCTIONS
// ============================================

function initializeAmbulanceList() {
    const ambulanceList = document.getElementById('ambulanceList');
    if (!ambulanceList) return;
    
    ambulanceList.innerHTML = '';
    
    // Generate ambulance options V1 to V12
    for (let i = 1; i <= 12; i++) {
        const ambulanceId = `V${i}`;
        const plateNumber = generatePlateNumber();
        
        const ambulanceOption = document.createElement('div');
        ambulanceOption.className = 'vehicle-option';
        ambulanceOption.dataset.id = ambulanceId;
        ambulanceOption.dataset.plate = plateNumber;
        
        ambulanceOption.innerHTML = `
            <div class="vehicle-name">${ambulanceId}</div>
            <div class="vehicle-details">Plate Number: ${plateNumber}</div>
        `;
        
        ambulanceOption.addEventListener('click', function() {
            // Remove selected class from all options
            document.querySelectorAll('#ambulanceList .vehicle-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            this.classList.add('selected');
        });
        
        ambulanceList.appendChild(ambulanceOption);
    }
}

function generatePlateNumber() {
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const numbers = '0123456789';
    
    let plate = '';
    // First 3 letters
    for (let i = 0; i < 3; i++) {
        plate += letters.charAt(Math.floor(Math.random() * letters.length));
    }
    // Then 4 numbers
    for (let i = 0; i < 4; i++) {
        plate += numbers.charAt(Math.floor(Math.random() * numbers.length));
    }
    
    return plate;
}

function setupVehicleModals() {
    // Ambulance selection
    const ambulanceRadio = document.getElementById('ambulance');
    if (ambulanceRadio) {
        ambulanceRadio.addEventListener('click', function() {
            const ambulanceModal = new bootstrap.Modal(document.getElementById('ambulanceModal'));
            ambulanceModal.show();
        });
    }
    
    // Fire truck selection
    const fireTruckRadio = document.getElementById('fireTruck');
    if (fireTruckRadio) {
        fireTruckRadio.addEventListener('click', function() {
            const fireTruckModal = new bootstrap.Modal(document.getElementById('fireTruckModal'));
            fireTruckModal.show();
        });
    }
    
    // Fire truck selection
    document.querySelectorAll('#fireTruckModal .vehicle-option').forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            document.querySelectorAll('#fireTruckModal .vehicle-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            this.classList.add('selected');
        });
    });
    
    // Confirm ambulance selection
    const confirmAmbulanceBtn = document.getElementById('confirmAmbulance');
    if (confirmAmbulanceBtn) {
        confirmAmbulanceBtn.addEventListener('click', function() {
            const selectedAmbulance = document.querySelector('#ambulanceList .vehicle-option.selected');
            
            if (selectedAmbulance) {
                const ambulanceId = selectedAmbulance.dataset.id;
                const plateNumber = selectedAmbulance.dataset.plate;
                
                // Store vehicle details in hidden field
                document.getElementById('vehicleDetails').value = JSON.stringify({
                    type: 'ambulance',
                    id: ambulanceId,
                    plate: plateNumber
                });
                
                alert(`Ambulance ${ambulanceId} with plate ${plateNumber} selected.`);
                
                // Close modal
                const ambulanceModal = bootstrap.Modal.getInstance(document.getElementById('ambulanceModal'));
                ambulanceModal.hide();
            } else {
                alert('Please select an ambulance.');
            }
        });
    }
    
    // Confirm fire truck selection
    const confirmFireTruckBtn = document.getElementById('confirmFireTruck');
    if (confirmFireTruckBtn) {
        confirmFireTruckBtn.addEventListener('click', function() {
            const selectedFireTruck = document.querySelector('#fireTruckModal .vehicle-option.selected');
            
            if (selectedFireTruck) {
                const fireTruckType = selectedFireTruck.dataset.type;
                const fireTruckName = selectedFireTruck.querySelector('.vehicle-name').textContent;
                
                // Store vehicle details in hidden field
                document.getElementById('vehicleDetails').value = JSON.stringify({
                    type: 'firetruck',
                    subtype: fireTruckType,
                    name: fireTruckName
                });
                
                alert(`${fireTruckName} selected.`);
                
                // Close modal
                const fireTruckModal = bootstrap.Modal.getInstance(document.getElementById('fireTruckModal'));
                fireTruckModal.hide();
            } else {
                alert('Please select a fire truck type.');
            }
        });
    }
}
