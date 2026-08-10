// pengajuan-6tab.js - 6 Tab Pengajuan Skema Handler

(function() {
    'use strict';

    let currentTab = 1;
    const totalTabs = 6;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeTabs();
        initializeAssessmentPurpose();
        initializeUnitAssessment();
        initializeSignature();
        initializeAutoSave();
        
        // Restore tab from old input if validation failed
        const currentTabInput = document.getElementById('current_tab');
        const savedTab = parseInt(currentTabInput ? currentTabInput.value : '1') || 1;
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
     * APL-02 disederhanakan menjadi asesmen mandiri per Unit Kompetensi.
     * Elemen Kompetensi dan KUK tidak digunakan pada alur pengajuan ini.
     */
    function initializeUnitAssessment() {
        const tab = document.getElementById('tab-5');
        if (!tab) return;

        const title = tab.querySelector('.card-header h5');
        if (title) {
            title.innerHTML = '<i class="bi bi-clipboard-check"></i> Tab 5: Asesmen Mandiri (APL-02)';
        }

        const info = tab.querySelector('.card-body > .alert-info');
        if (info) {
            info.innerHTML = `
                <i class="bi bi-info-circle"></i>
                <strong>Petunjuk:</strong><br>
                &bull; Pilih <strong>K (Kompeten)</strong> jika Anda merasa sudah menguasai Unit Kompetensi tersebut.<br>
                &bull; Pilih <strong>BK (Belum Kompeten)</strong> jika Anda merasa belum menguasainya.<br>
                &bull; Bukti kompetensi dapat dilampirkan untuk mendukung asesmen mandiri.<br>
                <small class="text-muted">K/BK pada bagian ini adalah penilaian mandiri Asesi, bukan keputusan akhir Asesor.</small>
            `;
        }

        const cards = tab.querySelectorAll('.unit-assessment-card');
        cards.forEach((card, index) => {
            const oldTitle = card.querySelector('.unit-header h6');
            const oldCode = card.querySelector('.unit-header p');

            if (!oldTitle || !oldCode) return;

            const unitTitle = oldTitle.textContent.trim();
            const unitCode = oldCode.textContent.replace(/^Kode Unit:\s*/i, '').trim();
            const safeCode = escapeHtml(unitCode);
            const safeTitle = escapeHtml(unitTitle);

            card.innerHTML = `
                <div class="unit-header mb-3">
                    <h6 class="fw-bold mb-2">
                        <i class="bi bi-check2-square text-primary"></i>
                        ${safeTitle}
                    </h6>
                    <p class="small text-muted mb-0">Kode Unit: ${safeCode}</p>
                </div>

                <input type="hidden"
                       name="unit_assessment[${index}][kode_unit]"
                       value="${safeCode}">

                <div class="row align-items-end g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Penilaian Mandiri <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 border rounded p-3 bg-light">
                            <div class="form-check mb-0">
                                <input class="form-check-input"
                                       type="radio"
                                       name="unit_assessment[${index}][status]"
                                       id="unit_${index}_k"
                                       value="K"
                                       required>
                                <label class="form-check-label" for="unit_${index}_k">
                                    <strong>K</strong> - Kompeten
                                </label>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input"
                                       type="radio"
                                       name="unit_assessment[${index}][status]"
                                       id="unit_${index}_bk"
                                       value="BK"
                                       required>
                                <label class="form-check-label" for="unit_${index}_bk">
                                    <strong>BK</strong> - Belum Kompeten
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Bukti Kompetensi <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="file"
                               class="form-control"
                               name="unit_evidence[${index}]"
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small class="text-muted">Maksimal 2MB. Format: PDF, JPG, PNG, DOC, DOCX.</small>
                    </div>
                </div>
            `;
        });
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
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
        const currentTabInput = document.getElementById('current_tab');
        if (currentTabInput) currentTabInput.value = tabNumber;

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
                if (field.files.length === 0 && field.hasAttribute('required')) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            } else {
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

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
        canvas.addEventListener('touchstart', startDrawing);
        canvas.addEventListener('touchmove', draw);
        canvas.addEventListener('touchend', stopDrawing);

        const modalBackdrop = document.getElementById('signature-modal-backdrop');
        const openBtn = document.getElementById('open-signature-modal');
        const closeBtn = document.querySelector('.close-signature-modal');
        const cancelBtn = document.getElementById('btn-signature-cancel');
        const clearBtn = document.getElementById('btn-signature-clear');
        const saveBtn = document.getElementById('btn-signature-save');
        const uploadInput = document.getElementById('signature-upload');

        if (openBtn && modalBackdrop) {
            openBtn.addEventListener('click', function(e) {
                e.preventDefault();
                modalBackdrop.classList.add('show');
            });
        }

        if (closeBtn && modalBackdrop) {
            closeBtn.addEventListener('click', function() {
                modalBackdrop.classList.remove('show');
            });
        }

        if (cancelBtn && modalBackdrop) {
            cancelBtn.addEventListener('click', function() {
                modalBackdrop.classList.remove('show');
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            });
        }

        if (saveBtn && modalBackdrop) {
            saveBtn.addEventListener('click', function() {
                const dataURL = canvas.toDataURL('image/png');
                const signatureInput = document.getElementById('ttd_digital');
                if (signatureInput) signatureInput.value = dataURL;
                
                const preview = document.getElementById('signature-preview');
                const previewImg = document.getElementById('signature-preview-img');
                if (previewImg) previewImg.src = dataURL;
                if (preview) preview.style.display = 'block';
                
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

        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', function(e) {
                if (e.target === modalBackdrop) {
                    modalBackdrop.classList.remove('show');
                }
            });
        }
    }

    /**
     * Initialize auto-save functionality
     */
    function initializeAutoSave() {
        const form = document.getElementById('formPengajuan');
        if (!form) return;

        let saveTimeout;
        const indicator = document.getElementById('auto-save-indicator');

        form.addEventListener('input', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(function() {
                if (indicator) {
                    indicator.classList.add('show');
                    setTimeout(function() {
                        indicator.classList.remove('show');
                    }, 2000);
                }
            }, 3000);
        });
    }

    window.goToTab = goToTab;

})();
