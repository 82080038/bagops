<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Event/Operasi') ?></title>
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
        <a class="nav-link active" href="/?r=events">Event</a>
        <a class="nav-link" href="/?r=assignments">Penugasan</a>
        <a class="nav-link" href="/?r=reminders">Reminder</a>
      </div>
    </div>
  </nav>

  <div class="container mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Event / Operasi</h1>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah Event</button>
    </div>
    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div id="alertArea"></div>

    <div class="table-responsive bg-white rounded shadow-sm p-3">
      <table class="table table-sm table-striped align-middle mb-0">
        <thead class="table-light">
          <tr><th>Judul</th><th>Jenis</th><th>Lokasi</th><th>Waktu</th><th>Risiko</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php foreach ($list as $e): ?>
          <tr>
            <td><?= htmlspecialchars($e['title']) ?></td>
            <td><?= htmlspecialchars($e['type']) ?></td>
            <td><?= htmlspecialchars($e['location']) ?></td>
            <td><span class="dt" data-date="<?= htmlspecialchars($e['start_at']) ?>"></span> - <span class="dt" data-date="<?= htmlspecialchars($e['end_at']) ?>"></span></td>
            <td><?= htmlspecialchars($e['risk_level']) ?></td>
            <td class="text-nowrap">
              <button class="btn btn-sm btn-outline-primary btn-edit" data-bs-toggle="modal" data-bs-target="#modalAdd"
                data-id="<?= (int)$e['id'] ?>"
                data-title="<?= htmlspecialchars($e['title']) ?>"
                data-type="<?= htmlspecialchars($e['type']) ?>"
                data-location="<?= htmlspecialchars($e['location']) ?>"
                data-latitude="<?= htmlspecialchars($e['latitude']) ?>"
                data-longitude="<?= htmlspecialchars($e['longitude']) ?>"
                data-start_at="<?= htmlspecialchars($e['start_at']) ?>"
                data-end_at="<?= htmlspecialchars($e['end_at']) ?>"
                data-risk_level="<?= htmlspecialchars($e['risk_level']) ?>"
                data-notes="<?= htmlspecialchars($e['notes']) ?>"
              >Edit</button>
              <form class="d-inline" method="post" action="/?r=events" onsubmit="return confirm('Hapus event ini?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formEvent" method="post" action="/?r=events">
            <input type="hidden" name="action" value="create" id="actionEvent">
            <input type="hidden" name="id" value="">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Judul</label>
                <input class="form-control" name="title" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Jenis</label>
                <input class="form-control" name="type" placeholder="unras/razia/kunjungan/dll">
              </div>
              <div class="col-md-8">
                <label class="form-label">Lokasi</label>
                <input class="form-control" name="location">
              </div>
              <div class="col-md-2">
                <label class="form-label">Lat</label>
                <input class="form-control" name="latitude" type="number" step="0.0000001">
              </div>
              <div class="col-md-2">
                <label class="form-label">Lng</label>
                <input class="form-control" name="longitude" type="number" step="0.0000001">
              </div>
              <div class="col-md-6">
                <label class="form-label">Mulai (YYYY-MM-DD HH:MM)</label>
                <input class="form-control" name="start_at">
              </div>
              <div class="col-md-6">
                <label class="form-label">Selesai (YYYY-MM-DD HH:MM)</label>
                <input class="form-control" name="end_at">
              </div>
              <div class="col-md-4">
                <label class="form-label">Risiko</label>
                <input class="form-control" name="risk_level">
              </div>
              <div class="col-md-8">
                <label class="form-label">Catatan</label>
                <input class="form-control" name="notes">
              </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module">
    import { validate, showErrors, clearErrors, applyDateFormatting, useToast, initPageToast } from '/shared/app.js';

    const { show: showToast } = useToast();
    initPageToast(<?= json_encode($message ?? '') ?>);

    const form = document.getElementById('formEvent');
    form.addEventListener('submit', (e) => {
      clearErrors(form);
      const errors = validate({
        title: { value: form.title.value, label: 'Judul', rules: ['required'] },
      });
      if (Object.keys(errors).length > 0) {
        e.preventDefault();
        showErrors(form, errors);
        return;
      }
      e.preventDefault();
      const fd = new FormData(form);
      fetch(form.action, { method: 'POST', body: fd })
        .then(() => {
          const tbody = document.querySelector('table tbody');
          // Reload untuk sinkron edit/delete
          form.reset();
          document.getElementById('actionEvent').value = 'create';
          form.id.value = '';
          const modal = bootstrap.Modal.getInstance(document.getElementById('modalAdd'));
          modal.hide();
          showToast('Event tersimpan', 'success');
          window.location.reload();
        })
        .catch(() => {
          showToast('Gagal menyimpan', 'danger');
        });
    });
    applyDateFormatting();

    // Prefill edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('actionEvent').value = 'update';
        form.id.value = btn.dataset.id;
        form.title.value = btn.dataset.title;
        form.type.value = btn.dataset.type;
        form.location.value = btn.dataset.location;
        form.latitude.value = btn.dataset.latitude;
        form.longitude.value = btn.dataset.longitude;
        form.start_at.value = btn.dataset.start_at;
        form.end_at.value = btn.dataset.end_at;
        form.risk_level.value = btn.dataset.risk_level;
        form.notes.value = btn.dataset.notes;
      });
    });
  </script>
</body>
</html>
