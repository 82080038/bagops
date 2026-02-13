<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'BAGOPS App') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="/?r=home">BAGOPS</a>
      <div class="navbar-nav">
        <a class="nav-link" href="/?r=personnel">Personel</a>
        <a class="nav-link" href="/?r=events">Event</a>
        <a class="nav-link" href="/?r=assignments">Penugasan</a>
        <a class="nav-link" href="/?r=reminders">Reminder</a>
        <a class="nav-link" href="/?r=renops.form">RENOPS</a>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="mb-4">
      <h1 class="h4 mb-1"><?= htmlspecialchars($title ?? 'BAGOPS App') ?></h1>
      <p class="text-muted mb-0"><?= htmlspecialchars($message ?? '') ?></p>
    </div>

    <div class="row g-3">
      <div class="col-md-3 col-sm-6">
        <a class="text-decoration-none" href="/?r=personnel">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title">Personel</h5>
              <p class="card-text text-muted">Kelola data personel</p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-3 col-sm-6">
        <a class="text-decoration-none" href="/?r=events">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title">Event</h5>
              <p class="card-text text-muted">Daftar operasi/kegiatan</p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-3 col-sm-6">
        <a class="text-decoration-none" href="/?r=assignments">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title">Penugasan</h5>
              <p class="card-text text-muted">Bagi personel per peran/sektor</p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-3 col-sm-6">
        <a class="text-decoration-none" href="/?r=renops.form">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title">RENOPS</h5>
              <p class="card-text text-muted">Susun RENOPS & PDF</p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-3 col-sm-6">
        <a class="text-decoration-none" href="/?r=reminders">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title">Reminder</h5>
              <p class="card-text text-muted">Lihat pengingat H-3/H-1</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
