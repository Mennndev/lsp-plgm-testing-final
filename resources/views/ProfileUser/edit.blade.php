<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - LSP PLGM</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">

    <style>
        .pe-account-simple {
            width: 100%;
            padding: 4px 0 18px;
            border-bottom: 1px solid #e5eaf2;
        }

        .pe-account-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.4fr);
            gap: 18px;
            margin-top: 16px;
        }

        .pe-readonly {
            background: #f6f8fb !important;
        }

        .password-toggle {
            border-color: #dee2e6;
            background: #fff;
        }

        .employment-details {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid #e8edf4;
        }

        .employment-hint {
            font-size: 12px;
            color: #6c757d;
        }

        @media (max-width: 767.98px) {
            .pe-account-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<header class="pe-header">
    <div class="container-fluid pe-header-inner">
        <div>
            <h1 class="pe-title">Profil</h1>
            <p class="pe-subtitle">Kelola informasi akun, data pribadi, pendidikan, pekerjaan, dan keamanan akun Anda.</p>
        </div>

        <div class="pe-header-actions">
            @if (session('status') === 'profile-updated')
                <span class="pe-badge-success">Profil berhasil diperbarui</span>
            @endif

            @if (session('password-updated'))
                <span class="pe-badge-success">Password berhasil diubah</span>
            @endif

            <a href="{{ route('dashboard.user') }}" class="btn btn-outline-gold btn-sm">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>

            <button type="submit" form="profileForm" class="btn btn-blue btn-sm">
                <i class="fa fa-save"></i> Simpan Profil
            </button>
        </div>
    </div>
</header>

<main class="pe-main">
    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-8">
                <form id="profileForm"
                      method="POST"
                      action="{{ route('ProfileUser.update') }}"
                      class="pe-card needs-validation"
                      novalidate>
                    @csrf
                    @method('PATCH')

                    <div class="pe-account-simple">
                        <div class="pe-section-label">Detail Akun</div>
                        <div class="pe-account-grid">
                            <div>
                                <label class="pe-label">Nama Pengguna</label>
                                <input type="text" class="form-control pe-readonly" value="{{ $user->nama ?? '-' }}" disabled>
                            </div>
                            <div>
                                <label class="pe-label">Email</label>
                                <input type="email" class="form-control pe-readonly" value="{{ $user->email }}" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="pe-section">
                        <div class="pe-section-title">Data Pribadi</div>

                        <div class="row pe-row-gap">
                            <div class="col-md-4">
                                <label class="form-label">Nama lengkap <span class="pe-required">*</span></label>
                                <input type="text" name="nama"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $user->nama ?? '') }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tempat lahir <span class="pe-required">*</span></label>
                                <input type="text" name="tempat_lahir"
                                       class="form-control @error('tempat_lahir') is-invalid @enderror"
                                       value="{{ old('tempat_lahir', $pendaftaran->tempat_lahir ?? '') }}" required>
                                @error('tempat_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tanggal lahir <span class="pe-required">*</span></label>
                                <input type="date" name="tanggal_lahir"
                                       class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                       value="{{ old('tanggal_lahir', isset($pendaftaran->tanggal_lahir) ? \Carbon\Carbon::parse($pendaftaran->tanggal_lahir)->format('Y-m-d') : '') }}"
                                       required>
                                @error('tanggal_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label d-block">Jenis kelamin <span class="pe-required">*</span></label>
                                @php $jk = old('jenis_kelamin', $pendaftaran->jenis_kelamin ?? ''); @endphp
                                <div class="pe-radio-group">
                                    <label class="pe-radio">
                                        <input type="radio" name="jenis_kelamin" value="Laki-laki" {{ in_array($jk, ['Laki-laki', 'L']) ? 'checked' : '' }} required>
                                        <span>Laki-laki</span>
                                    </label>
                                    <label class="pe-radio">
                                        <input type="radio" name="jenis_kelamin" value="Perempuan" {{ in_array($jk, ['Perempuan', 'P']) ? 'checked' : '' }}>
                                        <span>Perempuan</span>
                                    </label>
                                </div>
                                @error('jenis_kelamin')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">NIK / No. KTP <span class="pe-required">*</span></label>
                                <input type="text" name="no_ktp" maxlength="16" inputmode="numeric"
                                       class="form-control @error('no_ktp') is-invalid @enderror"
                                       value="{{ old('no_ktp', $pendaftaran->no_ktp ?? '') }}" required>
                                @error('no_ktp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">No. Telp/Handphone <span class="pe-required">*</span></label>
                                <input type="text" name="no_hp"
                                       class="form-control @error('no_hp') is-invalid @enderror"
                                       value="{{ old('no_hp', $user->no_hp ?? '') }}" required>
                                @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Kota/Kabupaten <span class="pe-required">*</span></label>
                                <input type="text" name="kota"
                                       class="form-control @error('kota') is-invalid @enderror"
                                       value="{{ old('kota', $pendaftaran->kota ?? '') }}" required>
                                @error('kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Provinsi <span class="pe-required">*</span></label>
                                <input type="text" name="provinsi"
                                       class="form-control @error('provinsi') is-invalid @enderror"
                                       value="{{ old('provinsi', $pendaftaran->provinsi ?? '') }}" required>
                                @error('provinsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Alamat <span class="pe-required">*</span></label>
                                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2" required>{{ old('alamat', $pendaftaran->alamat ?? '') }}</textarea>
                                @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="pe-section">
                        <div class="pe-section-title">Pendidikan & Pekerjaan</div>

                        <div class="row pe-row-gap">
                            <div class="col-md-4">
                                <label class="form-label">Pendidikan terakhir <span class="pe-required">*</span></label>
                                @php $pendidikan = old('pendidikan', $pendaftaran->pendidikan ?? ''); @endphp
                                <select name="pendidikan" class="form-select @error('pendidikan') is-invalid @enderror" required>
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach([
                                        'SD' => 'SD',
                                        'SMP' => 'SMP',
                                        'SMA/SMK' => 'SMA/SMK',
                                        'D1' => 'Diploma 1 (D1)',
                                        'D2' => 'Diploma 2 (D2)',
                                        'D3' => 'Diploma 3 (D3)',
                                        'S1' => 'Sarjana (S1)',
                                        'S2' => 'Magister (S2)',
                                        'S3' => 'Doktor (S3)'
                                    ] as $value => $label)
                                        <option value="{{ $value }}" {{ $pendidikan === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('pendidikan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Pekerjaan <span class="pe-required">*</span></label>
                                @php $pekerjaan = old('pekerjaan', $pendaftaran->pekerjaan ?? ''); @endphp
                                <select id="pekerjaan" name="pekerjaan" class="form-select @error('pekerjaan') is-invalid @enderror" required>
                                    <option value="">Pilih Pekerjaan</option>
                                    @foreach([
                                        'Belum/Tidak Bekerja',
                                        'Mengurus Rumah Tangga',
                                        'Pelajar/Mahasiswa',
                                        'Pensiunan',
                                        'Pegawai Negeri Sipil (PNS)',
                                        'Tentara Nasional Indonesia (TNI)',
                                        'Kepolisian RI (POLRI)',
                                        'Karyawan Swasta',
                                        'Karyawan BUMN',
                                        'Karyawan BUMD',
                                        'Wiraswasta',
                                        'Lainnya'
                                    ] as $job)
                                        <option value="{{ $job }}" {{ $pekerjaan === $job ? 'selected' : '' }}>{{ $job }}</option>
                                    @endforeach
                                </select>
                                @error('pekerjaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Instansi / Perusahaan <span class="text-muted">(opsional)</span></label>
                                <input id="instansi" type="text" name="instansi"
                                       class="form-control @error('instansi') is-invalid @enderror"
                                       value="{{ old('instansi', $pendaftaran->instansi ?? '') }}"
                                       placeholder="Nama instansi atau perusahaan">
                                @error('instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="employment-details" id="employmentDetails">
                            <div class="row pe-row-gap">
                                <div class="col-md-6">
                                    <label class="form-label">Jabatan <span class="text-muted">(opsional)</span></label>
                                    <input id="jabatan" type="text" name="jabatan"
                                           class="form-control @error('jabatan') is-invalid @enderror"
                                           value="{{ old('jabatan', $pendaftaran->jabatan ?? '') }}"
                                           placeholder="Contoh: Staff Administrasi">
                                    @error('jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email Institusi/Perusahaan <span class="text-muted">(opsional)</span></label>
                                    <input id="email_instansi" type="email" name="email_instansi"
                                           class="form-control @error('email_instansi') is-invalid @enderror"
                                           value="{{ old('email_instansi', $pendaftaran->email_instansi ?? '') }}"
                                           placeholder="nama@perusahaan.com">
                                    @error('email_instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label">Alamat Institusi/Perusahaan <span class="text-muted">(opsional)</span></label>
                                    <textarea id="alamat_instansi" name="alamat_instansi"
                                              class="form-control @error('alamat_instansi') is-invalid @enderror"
                                              rows="2"
                                              placeholder="Alamat institusi atau perusahaan">{{ old('alamat_instansi', $pendaftaran->alamat_instansi ?? '') }}</textarea>
                                    @error('alamat_instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Telepon Institusi/Perusahaan <span class="text-muted">(opsional)</span></label>
                                    <input id="telepon_instansi" type="text" name="telepon_instansi"
                                           class="form-control @error('telepon_instansi') is-invalid @enderror"
                                           value="{{ old('telepon_instansi', $pendaftaran->telepon_instansi ?? '') }}"
                                           placeholder="Contoh: 022xxxxxxx">
                                    @error('telepon_instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="employment-hint mt-2" id="employmentHint">Data institusi dapat dikosongkan jika tidak bekerja.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-blue btn-sm">
                        <i class="fa fa-save"></i> Simpan Profil
                    </button>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="pe-card">
                    <div class="pe-section-title">Keamanan Akun</div>
                    <p class="text-muted small mb-3">Ubah password dengan memasukkan password akun saat ini terlebih dahulu.</p>

                    <form method="POST" action="{{ route('ProfileUser.password.update') }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">Password saat ini <span class="pe-required">*</span></label>
                            <div class="input-group">
                                <input id="current_password" type="password" name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                                <button class="btn password-toggle" type="button" data-toggle-password="current_password" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password baru <span class="pe-required">*</span></label>
                            <div class="input-group">
                                <input id="new_password" type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror" required minlength="8" autocomplete="new-password">
                                <button class="btn password-toggle" type="button" data-toggle-password="new_password" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi password baru <span class="pe-required">*</span></label>
                            <div class="input-group">
                                <input id="new_password_confirmation" type="password" name="password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
                                <button class="btn password-toggle" type="button" data-toggle-password="new_password_confirmation" aria-label="Tampilkan password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-blue btn-sm w-100">
                            <i class="fa fa-lock"></i> Ganti Password
                        </button>

                        <p class="text-muted small mt-2 mb-0">Minimal 8 karakter. Setelah berhasil, gunakan password baru pada login berikutnya.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script>
    (function () {
        'use strict';

        document.querySelectorAll('.needs-validation').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });

        const pekerjaan = document.getElementById('pekerjaan');
        const employmentFields = ['instansi', 'jabatan', 'email_instansi', 'alamat_instansi', 'telepon_instansi']
            .map(id => document.getElementById(id))
            .filter(Boolean);
        const employmentHint = document.getElementById('employmentHint');
        const nonWorkingStatuses = ['Belum/Tidak Bekerja', 'Mengurus Rumah Tangga'];

        function syncEmploymentFields() {
            if (!pekerjaan) return;

            const employmentNotApplicable = nonWorkingStatuses.includes(pekerjaan.value);

            employmentFields.forEach(function (field) {
                field.disabled = employmentNotApplicable;
                if (employmentNotApplicable) field.value = '';
            });

            if (employmentHint) {
                if (pekerjaan.value === 'Belum/Tidak Bekerja') {
                    employmentHint.textContent = 'Data institusi/perusahaan tidak diperlukan karena status pekerjaan Belum/Tidak Bekerja.';
                } else if (pekerjaan.value === 'Mengurus Rumah Tangga') {
                    employmentHint.textContent = 'Data institusi/perusahaan tidak diperlukan untuk status Mengurus Rumah Tangga.';
                } else if (pekerjaan.value === 'Pelajar/Mahasiswa') {
                    employmentHint.textContent = 'Untuk Pelajar/Mahasiswa, data institusi dapat diisi dengan nama sekolah atau perguruan tinggi.';
                } else {
                    employmentHint.textContent = 'Lengkapi data institusi/perusahaan jika sesuai dengan kondisi pekerjaan Anda.';
                }
            }
        }

        if (pekerjaan) {
            pekerjaan.addEventListener('change', syncEmploymentFields);
            syncEmploymentFields();
        }

        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.togglePassword);
                if (!input) return;

                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                const icon = button.querySelector('i');
                if (icon) icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
                button.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });
    })();
</script>
</body>
</html>
