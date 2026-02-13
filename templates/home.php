<?php
// Ambil data statistik
$pdo = db_connection();

// Hitung total personel
$totalPersonnel = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Hitung total event/operasi
$totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();

// Hitung total penugasan
$totalAssignments = $pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();

// Ambil event yang akan datang (7 hari ke depan)
$upcomingEvents = $pdo->query(
    "SELECT * FROM events 
     WHERE start_at >= NOW() AND start_at <= DATE_ADD(NOW(), INTERVAL 7 DAY)
     ORDER BY start_at ASC LIMIT 5"
)->fetchAll();

// Ambil penugasan terbaru
$recentAssignments = $pdo->query(
    "SELECT a.*, e.title as event_title, u.name as person_name 
     FROM assignments a
     JOIN events e ON a.event_id = e.id
     JOIN users u ON a.user_id = u.id
     ORDER BY a.created_at DESC LIMIT 5"
)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'BAGOPS - Dashboard') ?></title>
  <!-- Favicon from Bootstrap Icons -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><path d='M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-1.147 1.146a.5.5 0 0 0 .708.708l2-2a.5.5 0 0 0 0-.708l-2-2a.5.5 0 1 0-.708.708L10.293 7.5H4.5z'/></svg>" type="image/svg+xml">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    .stat-card {
      transition: transform 0.2s;
      border-left: 4px solid #0d6efd;
    }
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .upcoming-event {
      border-left: 3px solid #0d6efd;
      margin-bottom: 0.5rem;
    }
    .recent-assignment {
      border-left: 3px solid #198754;
      margin-bottom: 0.5rem;
    }
  </style>
</head>
<body class="bg-light">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="/?r=home">
        <i class="bi bi-shield-check me-2"></i>BAGOPS
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link active" href="/?r=home">
              <i class="bi bi-house-door me-1"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/?r=personnel">
              <i class="bi bi-people me-1"></i> Personel
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/?r=events">
              <i class="bi bi-calendar-event me-1"></i> Event
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/?r=assignments">
              <i class="bi bi-clipboard-check me-1"></i> Penugasan
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/?r=reminders">
              <i class="bi bi-bell me-1"></i> Reminder
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/?r=renops.form">
              <i class="bi bi-file-earmark-text me-1"></i> RENOPS
            </a>
          </li>
        </ul>
        <div class="d-flex">
          <span class="navbar-text text-light">
            <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y, H:i') ?>
          </span>
        </div>
      </div>
    </div>
  </nav>

  <div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Ringkasan aktivitas dan statistik terkini</p>
      </div>
      <div>
        <a href="/?r=renops.form" class="btn btn-primary">
          <i class="bi bi-plus-circle me-1"></i> Buat RENOPS Baru
        </a>
      </div>
    </div>

    <?php if (!empty($message)): ?>
      <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Statistik Utama -->
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="card stat-card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-uppercase text-muted small fw-bold">Total Personel</h6>
                <h2 class="mb-0"><?= number_format($totalPersonnel) ?></h2>
              </div>
              <div class="bg-primary bg-opacity-10 p-3 rounded">
                <i class="bi bi-people fs-2 text-primary"></i>
              </div>
            </div>
            <div class="mt-3">
              <a href="/?r=personnel" class="btn btn-sm btn-outline-primary">
                Kelola Personel <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card stat-card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-uppercase text-muted small fw-bold">Total Event/Operasi</h6>
                <h2 class="mb-0"><?= number_format($totalEvents) ?></h2>
              </div>
              <div class="bg-success bg-opacity-10 p-3 rounded">
                <i class="bi bi-calendar-event fs-2 text-success"></i>
              </div>
            </div>
            <div class="mt-3">
              <a href="/?r=events" class="btn btn-sm btn-outline-success">
                Lihat Semua Event <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card stat-card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="text-uppercase text-muted small fw-bold">Total Penugasan</h6>
                <h2 class="mb-0"><?= number_format($totalAssignments) ?></h2>
              </div>
              <div class="bg-warning bg-opacity-10 p-3 rounded">
                <i class="bi bi-clipboard-check fs-2 text-warning"></i>
              </div>
            </div>
            <div class="mt-3">
              <a href="/?r=assignments" class="btn btn-sm btn-outline-warning">
                Kelola Penugasan <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <!-- Event yang Akan Datang -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Event Mendatang</h5>
            <a href="/?r=events" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
          </div>
          <div class="card-body">
            <?php if (count($upcomingEvents) > 0): ?>
              <?php foreach ($upcomingEvents as $event): ?>
                <div class="card mb-2 shadow-sm upcoming-event">
                  <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <h6 class="mb-0"><?= htmlspecialchars($event['title']) ?></h6>
                        <small class="text-muted">
                          <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($event['location']) ?>
                        </small>
                      </div>
                      <div class="text-end">
                        <div class="badge bg-<?= $event['risk_level'] === 'Tinggi' ? 'danger' : ($event['risk_level'] === 'Sedang' ? 'warning' : 'info') ?>">
                          <?= htmlspecialchars($event['risk_level']) ?>
                        </div>
                        <div class="small text-muted">
                          <?= date('d M Y', strtotime($event['start_at'])) ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-4">
                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                <p class="text-muted mt-2 mb-0">Tidak ada event dalam 7 hari ke depan</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Penugasan Terbaru -->
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clipboard-check me-2"></i>Penugasan Terbaru</h5>
            <a href="/?r=assignments" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
          </div>
          <div class="card-body">
            <?php if (count($recentAssignments) > 0): ?>
              <?php foreach ($recentAssignments as $assignment): ?>
                <div class="card mb-2 shadow-sm recent-assignment">
                  <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <h6 class="mb-0"><?= htmlspecialchars($assignment['person_name']) ?></h6>
                        <small class="text-muted">
                          <?= htmlspecialchars($assignment['role']) ?>
                        </small>
                      </div>
                      <div class="text-end">
                        <div class="small text-muted">
                          <?= date('d M Y', strtotime($assignment['assigned_at'])) ?>
                        </div>
                        <small class="text-primary">
                          <?= htmlspecialchars($assignment['event_title']) ?>
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="text-center py-4">
                <i class="bi bi-clipboard-x fs-1 text-muted"></i>
                <p class="text-muted mt-2 mb-0">Belum ada penugasan</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card mb-4">
      <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</h5>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3 col-6">
            <a href="/?r=personnel&action=add" class="text-decoration-none">
              <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center">
                  <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-person-plus fs-3 text-primary"></i>
                  </div>
                  <p class="mt-2 mb-0 small">Tambah Personel</p>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-3 col-6">
            <a href="/?r=events&action=add" class="text-decoration-none">
              <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center">
                  <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-calendar-plus fs-3 text-success"></i>
                  </div>
                  <p class="mt-2 mb-0 small">Buat Event Baru</p>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-3 col-6">
            <a href="/?r=assignments&action=add" class="text-decoration-none">
              <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center">
                  <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-clipboard2-plus fs-3 text-warning"></i>
                  </div>
                  <p class="mt-2 mb-0 small">Buat Penugasan</p>
                </div>
              </div>
            </a>
          </div>
          <div class="col-md-3 col-6">
            <a href="/?r=renops.form" class="text-decoration-none">
              <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body text-center">
                  <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-file-earmark-text fs-3 text-info"></i>
                  </div>
                  <p class="mt-2 mb-0 small">Buat RENOPS</p>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="bg-light py-4 mt-5">
    <div class="container">
      <div class="text-center text-muted small">
        <p class="mb-0">© <?= date('Y') ?> BAGOPS - Aplikasi Manajemen Operasi</p>
        <p class="mb-0">Versi 1.0.0</p>
      </div>
    </div>
  </footer>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Update waktu secara real-time
    function updateClock() {
      const now = new Date();
      const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
      };
      document.getElementById('current-time').textContent = now.toLocaleDateString('id-ID', options);
    }
    
    // Update waktu setiap detik
    setInterval(updateClock, 1000);
    updateClock(); // Panggil sekali saat halaman dimuat
  </script>
</body>
</html>
