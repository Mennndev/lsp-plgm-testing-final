// === VALIDASI PASSWORD SAMA (REAL-TIME) ===
const formDaftar = document.getElementById('daftarForm');
const password = document.getElementById('password');
const passwordConf = document.getElementById('password_confirmation');

// === LIHAT / SEMBUNYIKAN PASSWORD ===
// Gunakan tombol milik aplikasi sendiri agar tidak bergantung pada tombol reveal
// bawaan browser yang dapat hilang ketika fokus berpindah antar input.
function addPasswordToggle(input) {
  if (!input) return;

  const inputGroup = input.closest('.input-group');
  if (!inputGroup || inputGroup.querySelector('[data-password-toggle]')) return;

  const toggleButton = document.createElement('button');
  toggleButton.type = 'button';
  toggleButton.className = 'input-group-text bg-light border-start-0';
  toggleButton.setAttribute('data-password-toggle', 'true');
  toggleButton.setAttribute('aria-label', 'Tampilkan password');
  toggleButton.setAttribute('title', 'Tampilkan password');
  toggleButton.innerHTML = '<i class="bi bi-eye"></i>';

  toggleButton.addEventListener('click', function () {
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';

    const icon = toggleButton.querySelector('i');
    if (icon) {
      icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
    }

    const label = isHidden ? 'Sembunyikan password' : 'Tampilkan password';
    toggleButton.setAttribute('aria-label', label);
    toggleButton.setAttribute('title', label);

    // Kembalikan fokus ke input tanpa mengubah nilai yang sudah diketik.
    input.focus({ preventScroll: true });
    const length = input.value.length;
    try {
      input.setSelectionRange(length, length);
    } catch (error) {
      // Beberapa browser/input type dapat mengabaikan selection range.
    }
  });

  inputGroup.appendChild(toggleButton);
}

addPasswordToggle(password);
addPasswordToggle(passwordConf);

function validatePasswordMatch() {
  const tooltip = document.getElementById('tooltip-password');

  if (password.value !== passwordConf.value) {
    tooltip.style.display = 'block';
  } else {
    tooltip.style.display = 'none';
  }
}

passwordConf.addEventListener('input', validatePasswordMatch);
password.addEventListener('input', validatePasswordMatch);

// === MODAL TANDA TANGAN ===
const signatureModalBackdrop = document.getElementById('signature-modal-backdrop');
const openSignatureModalBtn = document.getElementById('open-signature-modal');
const closeSignatureBtns = document.querySelectorAll('.close-signature-modal, #btn-signature-cancel');
const signatureCanvas = document.getElementById('signature-canvas');
const clearSignatureBtn = document.getElementById('btn-signature-clear');
const saveSignatureBtn = document.getElementById('btn-signature-save');
const uploadSignatureInput = document.getElementById('signature-upload');
const signatureInputHidden = document.getElementById('ttd_digital');
const signatureError = document.getElementById('signature-error');
const signaturePreview = document.getElementById('signature-preview');
const signaturePreviewImg = document.getElementById('signature-preview-img');

let isDrawing = false;
let isSigned = false; // dari canvas
let uploadedSignatureData = ''; // dari upload file
let lastPos = { x: 0, y: 0 };

function openSignatureModal() {
  signatureModalBackdrop.style.display = 'flex';
  resizeCanvas();
}

function closeSignatureModal() {
  signatureModalBackdrop.style.display = 'none';
}

if (openSignatureModalBtn) {
  openSignatureModalBtn.addEventListener('click', function (e) {
    e.preventDefault();
    openSignatureModal();
  });
}

closeSignatureBtns.forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.preventDefault();
    closeSignatureModal();
  });
});

// klik di luar modal menutup modal
signatureModalBackdrop.addEventListener('click', function (e) {
  if (e.target === signatureModalBackdrop) {
    closeSignatureModal();
  }
});

// CANVAS DRAW
if (signatureCanvas) {
  const ctx = signatureCanvas.getContext('2d');
  ctx.lineWidth = 2;
  ctx.lineCap = 'round';
  ctx.strokeStyle = '#333';

  function resizeCanvas() {
    // Scale canvas mengikuti lebar container
    const rect = signatureCanvas.getBoundingClientRect();
    const ratio = window.devicePixelRatio || 1;

    signatureCanvas.width = rect.width * ratio;
    signatureCanvas.height = 200 * ratio;
    ctx.scale(ratio, ratio);
    ctx.lineWidth = 2;
  }

  window.addEventListener('resize', () => {
    if (signatureModalBackdrop.style.display === 'flex') {
      resizeCanvas();
    }
  });

  function getCanvasPos(e) {
    const rect = signatureCanvas.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return {
      x: clientX - rect.left,
      y: clientY - rect.top
    };
  }

  function startDrawing(e) {
    e.preventDefault();
    isDrawing = true;
    isSigned = true;
    uploadedSignatureData = ''; // jika mulai menggambar, abaikan upload
    signatureError.style.display = 'none';
    const pos = getCanvasPos(e);
    lastPos = pos;
  }

  function draw(e) {
    if (!isDrawing) return;
    e.preventDefault();
    const pos = getCanvasPos(e);
    ctx.beginPath();
    ctx.moveTo(lastPos.x, lastPos.y);
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
    lastPos = pos;
  }

  function endDrawing(e) {
    if (!isDrawing) return;
    e && e.preventDefault();
    isDrawing = false;
  }

  // Mouse events
  signatureCanvas.addEventListener('mousedown', startDrawing);
  signatureCanvas.addEventListener('mousemove', draw);
  signatureCanvas.addEventListener('mouseup', endDrawing);
  signatureCanvas.addEventListener('mouseleave', endDrawing);

  // Touch events
  signatureCanvas.addEventListener('touchstart', startDrawing, { passive: false });
  signatureCanvas.addEventListener('touchmove', draw, { passive: false });
  signatureCanvas.addEventListener('touchend', endDrawing, { passive: false });

  // Tombol Hapus Kanvas
  if (clearSignatureBtn) {
    clearSignatureBtn.addEventListener('click', function () {
      const rect = signatureCanvas.getBoundingClientRect();
      ctx.clearRect(0, 0, rect.width, rect.height);
      isSigned = false;
      uploadedSignatureData = '';
      if (signatureInputHidden) signatureInputHidden.value = '';
      signatureError.style.display = 'none';
    });
  }
}

// Upload Signature (gambar)
if (uploadSignatureInput) {
  uploadSignatureInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    // validasi tipe file
    const allowedTypes = ['image/jpeg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
      alert('Format tanda tangan harus JPG atau PNG.');
      this.value = '';
      return;
    }

    // validasi ukuran (2MB)
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
      alert('Ukuran file maksimal 2MB.');
      this.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      uploadedSignatureData = e.target.result; // base64
      isSigned = false;
      signatureError.style.display = 'none';
    };
    reader.readAsDataURL(file);
  });
}

// Simpan tanda tangan (canvas / upload)
if (saveSignatureBtn) {
  saveSignatureBtn.addEventListener('click', function () {
    let dataUrl = '';

    if (uploadedSignatureData) {
      // dari upload file
      dataUrl = uploadedSignatureData;
    } else if (isSigned && signatureCanvas) {
      // dari kanvas
      dataUrl = signatureCanvas.toDataURL('image/png');
    }

    if (!dataUrl) {
      signatureError.style.display = 'block';
      return;
    }

    // set ke input hidden
    signatureInputHidden.value = dataUrl;
    signatureError.style.display = 'none';

    // tampilkan preview
    signaturePreviewImg.src = dataUrl;
    signaturePreview.style.display = 'block';

    closeSignatureModal();
  });
}

// validasi saat submit (backup)
formDaftar.addEventListener('submit', function (e) {
  // cek password
  validatePasswordMatch();

  // cek tanda tangan digital
  if (!signatureInputHidden.value) {
    e.preventDefault();
    e.stopPropagation();
    signatureError.style.display = 'block';
  }

  if (!this.checkValidity()) {
    e.preventDefault();
    e.stopPropagation();
  }

  this.classList.add('was-validated');
}, false);

window.addEventListener('load', function () {
  var preloader = document.getElementById('preloader');
  if (preloader) {
    preloader.style.display = 'none';
  }
});
