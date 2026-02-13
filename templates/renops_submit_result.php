<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'RENOPS Tersimpan') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
    <div id="toast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body" id="toastBody"></div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="/?r=home">BAGOPS</a>
      <div class="navbar-nav">
        <a class="nav-link" href="/?r=personnel">Personel</a>
        <a class="nav-link" href="/?r=events">Event</a>
        <a class="nav-link" href="/?r=assignments">Penugasan</a>
        <a class="nav-link" href="/?r=reminders">Reminder</a>
        <a class="nav-link active" href="/?r=renops.form">RENOPS</a>
      </div>
    </div>
  </nav>

  <div class="container mb-4">
    <h1 class="h4 mb-3">RENOPS Tersimpan</h1>
    <?php if (!empty($result['error'])): ?>
      <div class="alert alert-danger">Gagal: <?= htmlspecialchars($result['error']) ?></div>
    <?php else: ?>
      <div class="alert alert-success">
        <div>Event ID: <strong><?= htmlspecialchars($result['event_id'] ?? '') ?></strong></div>
        <div>PDF: <?= !empty($result['pdf_path']) ? '<a href="/' . htmlspecialchars($result['pdf_path']) . '" target="_blank">Unduh</a>' : 'belum tersedia' ?></div>
        <div><?= htmlspecialchars($result['message'] ?? '') ?></div>
      </div>
    <?php endif; ?>
    <a class="btn btn-secondary" href="/?r=renops.form">Kembali ke Form</a>
    <a class="btn btn-outline-primary" href="/?r=home">Beranda</a>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module">
    import { useToast, initPageToast } from '/shared/app.js';
    const { show: showToast } = useToast();
    initPageToast('');
  </script>
</body>
</html>
