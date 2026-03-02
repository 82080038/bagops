<?php
/**
 * Help Page Template
 */

// Get help topics based on user role
$userRole = $GLOBALS['user_role'] ?? 'user';

$helpTopics = [
    'dashboard' => [
        'title' => 'Dashboard',
        'description' => 'Panduan penggunaan dashboard',
        'icon' => 'tachometer-alt',
        'content' => 'Dashboard menampilkan statistik real-time personel, operasi, dan laporan.'
    ],
    'personel' => [
        'title' => 'Data Personel',
        'description' => 'Manajemen data personel kepolisian',
        'icon' => 'users',
        'content' => 'Halaman personel digunakan untuk mengelola data personel kepolisian.'
    ],
    'operations' => [
        'title' => 'Data Operasi',
        'description' => 'Manajemen operasi kepolisian',
        'icon' => 'cogs',
        'content' => 'Halaman operasi digunakan untuk mengelola data operasi kepolisian.'
    ],
    'reports' => [
        'title' => 'Laporan',
        'description' => 'Sistem pelaporan operasional',
        'icon' => 'file-alt',
        'content' => 'Halaman laporan digunakan untuk membuat dan mengelola laporan operasional.'
    ],
    'settings' => [
        'title' => 'Pengaturan',
        'description' => 'Pengaturan sistem',
        'icon' => 'cog',
        'content' => 'Halaman pengaturan digunakan untuk mengkonfigurasi sistem.'
    ]
];

// Filter topics based on role
if ($userRole === 'user') {
    unset($helpTopics['operations'], $helpTopics['settings']);
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-question-circle me-2"></i>Bantuan</h2>
        <p class="text-muted">Panduan dan bantuan sistem BAGOPS POLRES SAMOSIR</p>
        <small class="text-muted">Template: help.php | Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pencarian Bantuan</h6>
            </div>
            <div class="card-body">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari bantuan..." id="searchHelp">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="accordion" id="helpAccordion">
            <?php foreach ($helpTopics as $key => $topic): ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?php echo ucfirst($key); ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#collapse<?php echo ucfirst($key); ?>" aria-expanded="false">
                        <i class="fas fa-<?php echo $topic['icon']; ?> me-2"></i>
                        <?php echo htmlspecialchars($topic['title']); ?>
                    </button>
                </h2>
                <div id="collapse<?php echo ucfirst($key); ?>" class="accordion-collapse collapse" 
                     aria-labelledby="heading<?php echo ucfirst($key); ?>" data-bs-parent="#helpAccordion">
                    <div class="accordion-body">
                        <h6><?php echo htmlspecialchars($topic['description']); ?></h6>
                        <p><?php echo htmlspecialchars($topic['content']); ?></p>
                        
                        <div class="mt-3">
                            <h6>Langkah-langkah:</h6>
                            <ol>
                                <li>Buka halaman <?php echo strtolower($topic['title']); ?></li>
                                <li>Perhatikan informasi yang tersedia</li>
                                <li>Gunakan tombol aksi sesuai kebutuhan</li>
                                <li>Simpan perubahan jika diperlukan</li>
                            </ol>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Tips:</h6>
                            <ul>
                                <li>Gunakan fitur pencarian untuk menemukan data</li>
                                <li>Perhatikan indikator status untuk informasi terkini</li>
                                <li>Hubungi admin jika mengalami masalah</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Kontak Support</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="fas fa-phone me-2"></i>Telepon</h6>
                    <p class="mb-0">(0623) 1234567</p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="fas fa-envelope me-2"></i>Email</h6>
                    <p class="mb-0">support@bagops-samosir.polri.go.id</p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="fas fa-clock me-2"></i>Jam Operasional</h6>
                    <p class="mb-0">Senin - Jumat: 08:00 - 16:00</p>
                    <p class="mb-0">Sabtu: 08:00 - 12:00</p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="fas fa-map-marker-alt me-2"></i>Lokasi</h6>
                    <p class="mb-0">Kantor POLRES SAMOSIR</p>
                    <p class="mb-0">Jl. Polres No. 1, Samosir</p>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="openSupportChat()">
                        <i class="fas fa-comments me-2"></i>Chat Support
                    </button>
                    <button class="btn btn-info" onclick="sendSupportEmail()">
                        <i class="fas fa-envelope me-2"></i>Kirim Email
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">FAQ</h6>
            </div>
            <div class="card-body">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#faq1">
                                Bagaimana cara login?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Masukkan username dan password yang telah diberikan oleh admin, kemudian klik tombol Login.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#faq2">
                                Lupa password?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Hubungi admin sistem untuk reset password melalui email atau telepon.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openSupportChat() {
    alert("Fitur chat support akan segera tersedia");
}

function sendSupportEmail() {
    window.location.href = "mailto:support@bagops-samosir.polri.go.id?subject=Bantuan%20Sistem%20BAGOPS";
}

console.log('Help page loaded');
</script>
