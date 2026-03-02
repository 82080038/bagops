<?php
/**
 * Profile Page Template
 */

// Get current user info
$currentUser = $_SESSION['user'] ?? ['username' => 'Guest', 'role' => 'guest'];
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-user me-2"></i>Profile</h2>
        <p class="text-muted">Profile pengguna</p>
        <small class="text-muted">Template: profile.php | Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body text-center">
                <img src="https://picsum.photos/seed/<?php echo htmlspecialchars($currentUser['username']); ?>/150/150" 
                     alt="Profile" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h5><?php echo htmlspecialchars($currentUser['nama'] ?? $currentUser['username']); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars(ucfirst($currentUser['role'])); ?></p>
                <span class="badge bg-success">Active</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Akun</h6>
            </div>
            <div class="card-body">
                <form>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($currentUser['username']); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars(ucfirst($currentUser['role'])); ?>" readonly>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($currentUser['email'] ?? '-'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telepon</label>
                            <input type="tel" class="form-control" id="phone" value="<?php echo htmlspecialchars($currentUser['phone'] ?? '-'); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="address" rows="3"><?php echo htmlspecialchars($currentUser['address'] ?? '-'); ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-primary" onclick="updateProfile()">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-warning" onclick="changePassword()">
                            <i class="fas fa-key me-2"></i>Ganti Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terakhir</h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-sign-in-alt text-success me-2"></i>
                            <span>Login ke sistem</span>
                        </div>
                        <small class="text-muted"><?php echo date('d/m/Y H:i'); ?></small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-eye text-info me-2"></i>
                            <span>Mengakses dashboard</span>
                        </div>
                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime('-30 minutes')); ?></small>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-cog text-warning me-2"></i>
                            <span>Mengubah pengaturan</span>
                        </div>
                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime('-1 hour')); ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateProfile() {
    alert("Fitur update profile akan segera tersedia");
}

function changePassword() {
    alert("Fitur ganti password akan segera tersedia");
}

console.log('Profile page loaded');
</script>
