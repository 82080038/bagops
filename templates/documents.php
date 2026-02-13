<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Lampiran Dokumen') ?></title>
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
        <a class="nav-link" href="/?r=renops.form">RENOPS</a>
        <a class="nav-link active" href="/?r=documents">Dokumen</a>
      </div>
    </div>
  </nav>

  <div class="container mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Lampiran Dokumen</h1>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUpload">Unggah Dokumen</button>
    </div>
    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div id="alertArea"></div>

    <div class="table-responsive bg-white rounded shadow-sm p-3">
      <table class="table table-sm table-striped align-middle mb-0">
        <thead class="table-light">
          <tr><th>Event</th><th>Nama Asli</th><th>Tipe</th><th>File</th><th>Waktu</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php foreach ($docs as $d): ?>
          <tr>
            <td><?= htmlspecialchars($d['event_title']) ?></td>
            <td><?= htmlspecialchars($d['original_name']) ?></td>
            <td><?= htmlspecialchars($d['type']) ?></td>
            <td><a href="/<?= htmlspecialchars($d['path']) ?>" target="_blank">Unduh</a></td>
            <td><?= htmlspecialchars($d['uploaded_at']) ?></td>
            <td class="text-nowrap">
              <form class="d-inline" method="post" action="/?r=documents" onsubmit="return confirm('Hapus dokumen ini?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal fade" id="modalUpload" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Unggah Dokumen</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post" action="/?r=documents" enctype="multipart/form-data" id="formUpload">
            <div class="mb-3">
              <label class="form-label">Event</label>
              <select class="form-select" name="event_id" required>
                <option value="">-pilih-</option>
                <?php foreach ($events as $e): ?>
                  <option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['title']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Tipe</label>
              <input class="form-control" name="type" placeholder="lampiran/renops/foto/dll">
            </div>
            <div class="mb-3">
              <label class="form-label">File</label>
              <input class="form-control" type="file" name="file" required>
              <div class="form-text">Maksimum 2MB. Tipe yang diizinkan: pdf, jpg, png, txt.</div>
            </div>
            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Unggah</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module">
    import { useToast, initPageToast } from '/shared/app.js';
    const { show: showToast } = useToast();
    initPageToast(<?= json_encode($message ?? '') ?>);

    const form = document.querySelector('#modalUpload form');
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const file = form.file.files[0];
      if (file && file.size > 2 * 1024 * 1024) {
        showToast('File terlalu besar, maksimum 2MB', 'danger');
        return;
      }
      const fd = new FormData(form);
      fetch(form.action, { method: 'POST', body: fd })
        .then(() => {
          form.reset();
          const modal = bootstrap.Modal.getInstance(document.getElementById('modalUpload'));
          modal.hide();
          showToast('Dokumen diunggah', 'success');
          window.location.reload();
        })
        .catch(() => {
          showToast('Gagal unggah', 'danger');
        });
    });
  </script>
</body>
</html>
