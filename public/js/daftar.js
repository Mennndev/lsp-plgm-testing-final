// === VALIDASI PASSWORD SAMA (REAL-TIME) ===
const formDaftar = document.getElementById('daftarForm');
const password = document.getElementById('password');
const passwordConf = document.getElementById('password_confirmation');

// === LIHAT / SEMBUNYIKAN PASSWORD ===
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

    input.focus({ preventScroll: true });
    const length = input.value.length;
    try {
      input.setSelectionRange(length, length);
    } catch (error) {
      // Browser tertentu dapat mengabaikan selection range.
    }
  });

  inputGroup.appendChild(toggleButton);
}

addPasswordToggle(password);
addPasswordToggle(passwordConf);

function validatePasswordMatch() {
  const tooltip = document.getElementById('tooltip-password');
  if (!password || !passwordConf || !tooltip) return;

  if (passwordConf.value !== '' && password.value !== passwordConf.value) {
    tooltip.style.display = 'block';
  } else {
    tooltip.style.display = 'none';
  }
}

if (passwordConf) passwordConf.addEventListener('input', validatePasswordMatch);
if (password) password.addEventListener('input', validatePasswordMatch);

// === REGISTRASI TIDAK LAGI MEMINTA TANDA TANGAN ===
// Tanda tangan resmi sekarang diisi pada proses pengajuan APL-01.
function removeLegacyRegistrationSignatureUi() {
  const signatureHeading = Array.from(document.querySelectorAll('h6'))
    .find((heading) => heading.textContent.trim() === 'Tanda Tangan Digital');

  if (signatureHeading) {
    const signatureSection = signatureHeading.closest('.mb-3.pb-3.border-bottom');
    if (signatureSection) signatureSection.remove();
  }

  const signatureInput = document.getElementById('ttd_digital');
  const signatureError = document.getElementById('signature-error');
  const signatureButton = document.getElementById('open-signature-modal');
  const signaturePreview = document.getElementById('signature-preview');
  const signatureModal = document.getElementById('signature-modal-backdrop');

  if (signatureInput) signatureInput.remove();
  if (signatureError) signatureError.remove();
  if (signaturePreview) signaturePreview.remove();
  if (signatureModal) signatureModal.remove();

  if (signatureButton) {
    const buttonWrapper = signatureButton.closest('.d-flex.flex-wrap.align-items-center');
    if (buttonWrapper) {
      buttonWrapper.remove();
    } else {
      signatureButton.remove();
    }
  }

  // Setelah bagian tanda tangan dihapus, Persetujuan menjadi bagian C.
  const approvalHeading = Array.from(document.querySelectorAll('h6'))
    .find((heading) => heading.textContent.trim() === 'Persetujuan');
  if (approvalHeading) {
    const section = approvalHeading.closest('.mb-3.pb-3.border-bottom');
    const circle = section ? section.querySelector('.section-circle') : null;
    if (circle) circle.textContent = 'C';
  }
}

removeLegacyRegistrationSignatureUi();

// === VALIDASI SUBMIT ===
if (formDaftar) {
  formDaftar.addEventListener('submit', function (e) {
    validatePasswordMatch();

    if (password && passwordConf && password.value !== passwordConf.value) {
      e.preventDefault();
      e.stopPropagation();
      return;
    }

    if (!this.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
    }

    this.classList.add('was-validated');
  }, false);
}

window.addEventListener('load', function () {
  const preloader = document.getElementById('preloader');
  if (preloader) {
    preloader.style.display = 'none';
  }
});
