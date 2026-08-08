// pengajuan-6tab.js - 6 Tab Pengajuan Skema Handler

(function() {
    'use strict';

    let currentTab = 1;
    const totalTabs = 6;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeTabs();
        initializeAssessmentPurpose();
        initializeSignature();
        initializeAutoSave();
        
        // Restore tab from old input if validation failed
        const savedTab = parseInt(document.getElementById('current_tab').value) || 1;
        if (savedTab > 1) {
            goToTab(savedTab);
        }
    });

    /**
     * Ganti pilihan Tujuan Asesmen lama (PKT/RPL/RCC/Lainnya)
     * menjadi pilihan APL-01: Sertifikasi / Sertifikasi Ulang.
     * Nama field tetap tujuan_asesmen[] agar kompatibel dengan kolom JSON lama.
     */
    function initializeAssessmentPurpose() {
        const oldInputs = document.querySelectorAll('input[name="tujuan_asesmen[]"]');
        if (!oldInputs.length) return;

        const firstInput = oldInputs[0];
        const container = firstInput.closest('.mb-3');
        if (!container) return;

        const previouslySelected = Array.from(oldInputs)
            .find(input => input.checked)?.value || '';

        const selectedValue = ['Sertifikasi', 'Sertifikasi Ulang'].includes(previouslySelected)
            ? previouslySelected
            : '';

        container.innerHTML = `
            <div class="form-check mb-2">
                <input class="form-check-input"
                       type="radio"
                       name="tujuan_asesmen[]"
                       value="Sertifikasi"
                       id="tujuan_sertifikasi"
                       required
                       ${selectedValue === 'Sertifikasi' ? 'checked' : ''}>
                <label class="form-check-label" for="tujuan_sertifikasi">
                    Sertifikasi
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input"
                       type="radio"
                       name="tujuan_asesmen[]"
                       value="Sertifikasi Ulang"
                       id="tujuan_sertifikasi_ulang"
                       required
                       ${selectedValue === 'Sertifikasi Ulang' ? 'checked' : ''}>
                <label class="form-check-label" for="tujuan_sertifikasi_ulang">
                    Sertifikasi Ulang
                </label>
            </div>
        `;
    }

    /**
     * Initialize tab navigation
     */
    function initializeTabs() {
        // Tab click navigation
        document.querySelectorAll('.tab-item').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabNumber = parseInt(this.getAttribute('data-tab'));
                goToTab(tabNumber);
            });
        });

        // Next button
        document.querySelectorAll('.btn-next-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                if (validateCurrentTab()) {
                    goToTab(currentTab + 1);
                }
            });
        });

        // Previous button
        document.querySelectorAll('.btn-prev-tab').forEach(btn => {
            btn.addEventListener('click', function() {
                goToTab(currentTab - 1);
            });
        });

        // Form submission
        const form = document.getElementById('formPengajuan');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateAllTabs()) {
                    e.preventDefault();
                    alert('Silakan lengkapi semua field yang wajib diisi.');
                    return false;
                }
            });
        }
    }

    /**
     * Navigate to specific tab
     */
    function goToTab(tabNumber) {
        if (tabNumber < 1 || tabNumber > totalTabs) return;

        // Update current tab
        currentTab = tabNumber;
        document.getElementById('current_tab').value = tabNumber;

        // Update tab items
        document.querySelectorAll('.tab-item').forEach(tab => {
            const tabNum = parseInt(tab.getAttribute('data-tab'));
            tab.classList.remove('active', 'completed');
            
            if (tabNum === tabNumber) {
                tab.classList.add('active');
            } else if (tabNum < tabNumber) {
                tab.classList.add('completed');
            }
        });

        // Update tab content
        document.querySelectorAll('.tab-content-item').forEach(content => {
            content.classList.remove('active');
        });
        
        const activeContent = document.getElementById(`tab-${tabNumber}`);
        if (activeContent) {
            activeContent.classList.add('active');
        }

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /**
     * Validate current tab
     */
    function validateCurrentTab() {
        const currentContent = document.getElementById(`tab-${currentTab}`);
        if (!currentContent) return true;

        const requiredFields = currentContent.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (field.type === 'checkbox' || field.type === 'radio') {
                // For checkboxes and radios, check if at least one is checked in the group
                const name = field.getAttribute('name');
                if (name) {
                    const checkedFields = currentContent.querySelectorAll(`[name="${name}"]:checked`);
                    if (checkedFields.length === 0 && field.hasAttribute('required')) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                }
            } else if (field.type === 'file') {
                // For file inputs, check if file is selected
                if (field.files.length === 0 && field.hasAttribute('required')) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            } else {
                // For other inputs
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            }
        });

        if (!isValid) {
            alert('Silakan lengkapi semua field yang wajib diisi pada tab ini.');
        }

        return isValid;
    }

    /**
     * Validate all tabs before submission
     */
    function validateAllTabs() {
        const form = document.getElementById('formPengajuan');
        if (!form) return true;

        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (field.type === 'file') {
                if (field.files.length === 0 && field.hasAttribute('required')) {
                    isValid = false;
                }
            } else if (field.type === 'checkbox') {
                // For agreement checkbox
                if (!field.checked && field.hasAttribute('required')) {
                    isValid = false;
                }
            } else if (field.type === 'radio') {
                const name = field.getAttribute('name');
                if (name && !form.querySelector(`[name="${name}"]:checked`)) {
                    isValid = false;
                }
            } else {
                if (!field.value.trim() && field.hasAttribute('required')) {
                    isValid = false;
                }
            }
        });

        return isValid;
    }

    /**
     * Initialize signature pad
     */
    function initializeSignature() {
        const canvas = document.getElementById('signature-canvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Drawing functions
        function startDrawing(e) {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            lastX = (e.clientX || e.touches[0].clientX) - rect.left;
            lastY = (e.clientY || e.touches[0].clientY) - rect.top;
        }

        function draw(e) {
            if (!isDrawing) return;
            
            e.preventDefault();
            const rect = canvas.getBoundingClientRect();
            const x = (e.clientX || e.touches[0].clientX) - rect.left;
            const y = (e.clientY || e.touches[0].clientY) - rect.top;

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.stroke();

            lastX = x;
            lastY = y;
        }

        function stopDrawing() {
            isDrawing = false;
        }

        // Mouse events
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);

        // Touch events
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);

        // Modal controls
        const modalBackdrop = document.getElementById('signature-modal-backdrop');
        const openBtn = document.getElementById('open-signature-modal');
        const closeBtn = document.querySelector('.close-signature-modal');
        const cancelBtn = document.getElementById('btn-signature-cancel');
        const clearBtn = document.getElementById('btn-signature-clear');
        const saveBtn = document.getElementById('btn-signature-save');
        const uploadInput = document.getElementById('signature-upload');

        if (openBtn) {
            openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                modalBackdrop.classList.add('show');
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modalBackdrop.classList.remove('show');
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                modalBackdrop.classList.remove('show');
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                const dataURL = canvas.toDataURL('image/png');
                document.getElementById('ttd_digital').value = dataURL;
                
                const preview = document.getElementById('signature-preview');
                const previewImg = document.getElementById('signature-preview-img');
                previewImg.src = dataURL;
                preview.style.display = 'block';
                
                modalBackdrop.classList.remove('show');
                alert('Tanda tangan berhasil disimpan!');
            });
        }

        if (uploadInput) {
            uploadInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        const img = new Image();
                        img.onload = function() {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        };
                        img.src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Close modal on backdrop click
        modalBackdrop.addEventListener('click', function(e) {
            if (e.target === modalBackdrop) {
                modalBackdrop.classList.remove('show');
            }
        });
    }

    /**
     * Initialize auto-save functionality
     */
    function initializeAutoSave() {
        const form = document.getElementById('formPengajuan');
        if (!form) return;

        let saveTimeout;
        const indicator = document.getElementById('auto-save-indicator');

        // Auto-save on input change (debounced)
        form.addEventListener('input', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(function() {
                saveDraft();
            }, 3000); // Save after 3 seconds of no typing
        });

        function saveDraft() {
            // In a real implementation, this would send an AJAX request
            // For now, we'll just show the indicator
            if (indicator) {
                indicator.classList.add('show');
                setTimeout(function() {
                    indicator.classList.remove('show');
                }, 2000);
            }
        }
    }

    // Make goToTab available globally for external use
    window.goToTab = goToTab;

})();