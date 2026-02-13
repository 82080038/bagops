<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Reminders') ?></title>
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
        <a class="nav-link active" href="/?r=reminders">Reminder</a>
      </div>
    </div>
  </nav>

  <div class="container mb-4">
    <h1 class="h4 mb-3">Reminder (H-3 / H-1)</h1>
    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div class="mb-3">
      <a class="btn btn-outline-primary btn-sm" href="/?r=reminders.run">Proses Reminder Due</a>
    </div>
    <div class="table-responsive bg-white rounded shadow-sm p-3">
      <table class="table table-sm table-striped align-middle mb-0">
        <thead class="table-light">
          <tr><th>Event</th><th>Waktu Kirim</th><th>Pesan</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php foreach ($list as $n): ?>
          <tr>
            <td><?= htmlspecialchars($n['event_title']) ?></td>
            <td><span class="dt" data-date="<?= htmlspecialchars($n['send_at']) ?>"></span></td>
            <td><?= htmlspecialchars($n['message']) ?></td>
            <td><?= htmlspecialchars($n['status']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module">
    import { applyDateFormatting, useToast, initPageToast } from '/shared/app.js';
    const { show: showToast } = useToast();
    initPageToast(<?= json_encode($message ?? '') ?>);
    applyDateFormatting();
  </script>
</body>
</html>
