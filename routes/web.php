<?php

use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Homecontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\TentangKamicontroller;
use App\Http\Controllers\VisiMisicontroller;
use App\Http\Controllers\KebijakanMutucontroller;
use App\Http\Controllers\StrukturOrganisasicontroller;
use App\Http\Controllers\Skemacontroller;
use App\Http\Controllers\Admin\ProgramPelatihanController;
use App\Http\Controllers\Admin\AdminBeritaController;
use App\Http\Controllers\Admin\AsesiController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\PengajuanSkemaController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\AsesorController;
use App\Http\Controllers\Admin\JadwalAsesmenController;
use App\Http\Controllers\Asesor\DashboardController;
use App\Http\Controllers\Asesor\PengajuanController;
use App\Http\Controllers\Asesor\PenilaianController;
use App\Http\Controllers\Asesor\ProfilController;
use App\Http\Controllers\Asesor\PengaturanController;
use App\Http\Controllers\Asesor\FormulirController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\SertifikatController;


// Pendaftaran Routes
Route::get('/pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Tentang Kami Route
Route::get('/tentang-kami', [TentangKamicontroller::class, 'index'])->name('tentang-kami');

// Visi Misi Route
Route::get('/visi-misi', [VisiMisicontroller::class, 'index'])->name('visi-misi');

// Kebijakan Mutu Route
Route::get('/kebijakan-mutu', [KebijakanMutucontroller::class, 'index'])->name('kebijakan-mutu');

// Struktur Organisasi Route
Route::get('/struktur-organisasi', [StrukturOrganisasicontroller::class, 'index'])->name('struktur-organisasi');

//skema sertifikasi routes
Route::get('/skema-sertifikasi', [Skemacontroller::class, 'index'])->name('skema.index');
Route::get('/skema/{program:slug}', [SkemaController::class, 'show'])
    ->name('skema.show');

Route::get('/tempat-sertifikasi', function () {
    return view('tempat-sertifikasi');
});

// Webhook for Midtrans - No Auth Required, No CSRF protection
Route::post('/webhook/midtrans', [PembayaranController::class, 'notification'])
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
    ->name('webhook.midtrans');

// password reset routes
Route::get('/password/forgot', [PasswordResetController::class, 'request'])
    ->name('password.request');

Route::post('/password/forgot', [PasswordResetController::class, 'email'])
    ->name('password.email');

Route::get('/password/reset/{token}', [PasswordResetController::class, 'resetForm'])
    ->name('password.reset');

Route::post('/password/reset', [PasswordResetController::class, 'reset'])
    ->name('password.update');

// Home Route
Route::get('/', [Homecontroller::class, 'index'])->name('home');

// Artikel Routes
Route::get('/berita', [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('berita.show');

Route::middleware(['auth'])->group(function () {

    // Dashboard utama user
    Route::get('/dashboard-user', [DashboardUserController::class, 'index'])
        ->name('dashboard.user');

    // FORM EDIT PROFIL USER
    Route::get('/dashboard-user/profile/edit', [ProfileController::class, 'edit'])
        ->name('ProfileUser.edit');

    // UPDATE PROFIL USER
    Route::patch('/dashboard-user/profile', [ProfileController::class, 'update'])
        ->name('ProfileUser.update');

    // UPDATE PASSWORD USER
    Route::patch('/dashboard-user/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('ProfileUser.password.update');

    // HAPUS AKUN
    Route::delete('/dashboard-user/profile', [ProfileController::class, 'destroy'])
        ->name('ProfileUser.destroy');

    //Route Khusus Tanda Tangan
    Route::patch('/dashboard-user/profile/signature', [ProfileController::class, 'updateSignature'])
        ->name('ProfileUser.signature.update');

     Route::get('/pengajuan/pilih-skema', [PengajuanSkemaController::class, 'pilihSkema'])
        ->name('pengajuan.pilih-skema');

    // Route untuk create pengajuan (setelah pilih skema)
    Route::get('/pengajuan/create/{program}', [PengajuanSkemaController::class, 'create'])
        ->name('pengajuan.create');

    Route::post('/pengajuan/store', [PengajuanSkemaController::class, 'store'])
        ->name('pengajuan.store');

    Route::get('/pengajuan/{id}', [PengajuanSkemaController::class, 'show'])
        ->name('pengajuan.show');

    Route::post('/pengajuan/draft', [PengajuanSkemaController::class, 'draft'])
        ->name('pengajuan.draft');

    Route::delete('/pengajuan/{id}', [PengajuanSkemaController::class, 'destroy'])
        ->name('pengajuan.destroy');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatest'])->name('notifications.latest');

    // Pembayaran routes (User)
   // Pembayaran routes (User) - dalam grup auth
Route::get('/pembayaran/{pengajuanId}', [PembayaranController:: class, 'show'])
    ->name('pembayaran.show');
Route::post('/pembayaran/{pengajuanId}/process', [PembayaranController::class, 'process'])
    ->name('pembayaran.process');
Route::get('/pembayaran/{id}/finish', [PembayaranController::class, 'finish'])
    ->name('pembayaran.finish');
Route::get('/pembayaran/{pengajuanId}/check-status', [PembayaranController::class, 'checkStatus'])
    ->name('pembayaran.check-status');
Route::post('/pembayaran/{pengajuanId}/reset', [PembayaranController::class, 'reset'])
    ->name('pembayaran.reset');

// Live Chat Routes
Route::get('/livechat', [ChatController::class, 'index'])->name('chat.index');
Route::post('/livechat/store', [ChatController::class, 'store'])->name('chat.store');
Route::get('/livechat/{id}', [ChatController::class, 'show'])->name('chat.show');
Route::post('/livechat/{id}/send-message', [ChatController::class, 'sendMessage'])->name('chat.send-message');
Route::post('/livechat/{id}/close', [ChatController::class, 'close'])->name('chat.close');
Route::get('/livechat/{id}/get-messages', [ChatController::class, 'getMessages'])->name('chat.get-messages');
Route::get('/livechat/get-chats', [ChatController::class, 'getChats'])->name('chat.get-chats');

});

//Admin Routes
Route::prefix('admin')
    ->middleware('auth')
    ->name('admin.')
    ->group(function () {
// Dashboard Admin Route
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('dashboard');

          Route::post('/pengajuan/{id}/assign-asesor',
        [\App\Http\Controllers\Admin\PengajuanController::class, 'assignAsesor']
        )->name('pengajuan.assign-asesor');


        // CRUD Program Pelatihan
        Route::resource('program-pelatihan', ProgramPelatihanController::class);
        // CRUD Asesi
        Route::resource('asesi', AsesiController::class);
        // CRUD Asesor
        Route::resource('asesor', AsesorController::class);
        Route::resource('berita' , AdminBeritaController::class);

        // Pengajuan Management (Admin Only)
        Route::middleware('admin')->group(function () {
            Route::get('/pengajuan', [\App\Http\Controllers\Admin\PengajuanController::class, 'index'])
                ->name('pengajuan.index');
            Route::get('/pengajuan/{id}', [\App\Http\Controllers\Admin\PengajuanController::class, 'show'])
                ->name('pengajuan.show');
            Route::post('/pengajuan/{id}/approve', [\App\Http\Controllers\Admin\PengajuanController::class, 'approve'])
                ->name('pengajuan.approve');
            Route::post('/pengajuan/{id}/reject', [\App\Http\Controllers\Admin\PengajuanController::class, 'reject'])
                ->name('pengajuan.reject');


            // Jadwal Asesmen Management (Admin)
            Route::get('/jadwal-asesmen', [JadwalAsesmenController::class, 'index'])
                ->name('jadwal-asesmen.index');
            Route::get('/jadwal-asesmen/pengajuan/{pengajuan}/form', [JadwalAsesmenController::class, 'formData'])
                ->name('jadwal-asesmen.form');
            Route::post('/jadwal-asesmen', [JadwalAsesmenController::class, 'upsert'])
                ->name('jadwal-asesmen.upsert');
            Route::patch('/jadwal-asesmen/{jadwalAsesmen}/status', [JadwalAsesmenController::class, 'setStatus'])
                ->name('jadwal-asesmen.set-status');

            // Pembayaran Management (Admin)
            Route::get('/pembayaran', [\App\Http\Controllers\Admin\PembayaranController::class, 'index'])
                ->name('pembayaran.index');
            Route::get('/pembayaran/{id}/detail', [\App\Http\Controllers\Admin\PembayaranController::class, 'show'])
                ->name('pembayaran.show');
            Route::post('/pembayaran/{id}/verify', [\App\Http\Controllers\Admin\PembayaranController::class, 'verify'])
                ->name('pembayaran.verify');
            Route::post('/pembayaran/{id}/reject', [\App\Http\Controllers\Admin\PembayaranController::class, 'reject'])
                ->name('pembayaran.reject');

            // Live Chat Management (Admin)
            Route::get('/livechat', [\App\Http\Controllers\Admin\ChatController::class, 'index'])
                ->name('chat.index');
            Route::get('/livechat/{id}', [\App\Http\Controllers\Admin\ChatController::class, 'show'])
                ->name('chat.show');
            Route::post('/livechat/{id}/send-message', [\App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])
                ->name('chat.send-message');
            Route::post('/livechat/{id}/close', [\App\Http\Controllers\Admin\ChatController::class, 'close'])
                ->name('chat.close');
            Route::post('/livechat/{id}/assign', [\App\Http\Controllers\Admin\ChatController::class, 'assign'])
                ->name('chat.assign');
            Route::post('/livechat/{id}/unassign', [\App\Http\Controllers\Admin\ChatController::class, 'unassign'])
                ->name('chat.unassign');
            Route::get('/livechat/{id}/get-messages', [\App\Http\Controllers\Admin\ChatController::class, 'getMessages'])
                ->name('chat.get-messages');
            Route::get('/livechat/get-chats', [\App\Http\Controllers\Admin\ChatController::class, 'getChats'])
                ->name('chat.get-chats');
            Route::get('/livechat/get-stats', [\App\Http\Controllers\Admin\ChatController::class, 'getStats'])
                ->name('chat.get-stats');

            // Sertifikat Management (Admin)
            Route::get('/pengajuan/{id}/sertifikat/create', [SertifikatController::class, 'create'])
                ->name('sertifikat.create');
            Route::post('/pengajuan/{id}/sertifikat', [SertifikatController::class, 'store'])
                ->name('sertifikat.store');

        });



    });

Route::middleware(['auth', 'role:asesor'])->prefix('asesor')->name('asesor.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pengajuan/{id}', [PengajuanController::class, 'show'])
        ->name('pengajuan.show');
    Route::get('/pengajuan/{id}/penilaian', [PenilaianController::class, 'show'])
        ->name('pengajuan.penilaian');
    Route::post('/pengajuan/{id}', [PenilaianController::class, 'store'])->name('pengajuan.store');

    // Formulir routes
    Route::get('/pengajuan/{id}/formulir', [FormulirController::class, 'index'])
        ->name('formulir.index');
    Route::get('/pengajuan/{id}/formulir/{jenis}', [FormulirController::class, 'show'])
        ->name('formulir.show');
    Route::post('/pengajuan/{id}/formulir/{jenis}', [FormulirController::class, 'store'])
        ->name('formulir.store');
    Route::get('/pengajuan/{id}/formulir/{jenis}/cetak', [FormulirController::class, 'cetak'])
        ->name('formulir.cetak');

    // Profil routes
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');

    // Pengaturan routes
    Route::get('/pengaturan', [PengaturanController::class, 'show'])->name('pengaturan');
    Route::put('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.password');
    Route::put('/pengaturan/preferensi', [PengaturanController::class, 'updatePreferensi'])->name('pengaturan.preferensi');

});

Route::get('/cek-midtrans', function () {
    return config('midtrans');
});








