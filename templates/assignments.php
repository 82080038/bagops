<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Penugasan Personel') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="/?r=home">BAGOPS</a>
      <div class="navbar-nav">
        <a class="nav-link" href="/?r=personnel">Personel</a>
        <a class="nav-link" href="/?r=events">Event</a>
        <a class="nav-link active" href="/?r=assignments">Penugasan</a>
        <a class="nav-link" href="/?r=reminders">Reminder</a>
      </div>
    </div>
  </nav>

  <div class="container mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Penugasan Personel</h1>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah Penugasan</button>
    </div>
    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div id="alertArea"></div>

    <div class="table-responsive bg-white rounded shadow-sm p-3">
      <table class="table table-sm table-striped align-middle mb-0">
        <thead class="table-light">
          <tr><th>Event</th><th>Personel</th><th>Pangkat</th><th>Jabatan</th><th>Peran</th><th>Jenis</th><th>Sektor</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php foreach ($list as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['event_title']) ?></td>
            <td><?= htmlspecialchars($a['user_name']) ?></td>
            <td><?= htmlspecialchars($a['rank']) ?></td>
            <td><?= htmlspecialchars($a['position']) ?></td>
            <td><?= htmlspecialchars($a['role']) ?></td>
            <td><?= htmlspecialchars($a['assignment_type']) ?></td>
            <td><?= htmlspecialchars($a['sector']) ?></td>
            <td class="text-nowrap">
              <form class="d-inline" method="post" action="/?r=assignments" onsubmit="return confirm('Hapus penugasan ini?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
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
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Penugasan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formAssign" method="post" action="/?r=assignments">
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
              <label class="form-label">Personel</label>
              <select class="form-select" name="user_id" required>
                <option value="">-pilih-</option>
                <?php foreach ($users as $u): ?>
                  <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['rank']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Peran</label>
              <input class="form-control" name="role">
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis Penugasan</label>
              <input class="form-control" name="assignment_type" placeholder="mis: PAWAS/PIKET/PADAL">
            </div>
            <div class="mb-3">
              <label class="form-label">Sektor</label>
              <input class="form-control" name="sector">
            </div>
            <div class="mb-3">
              <label class="form-label">Catatan</label>
              <input class="form-control" name="notes">
            </div>
            <div class="d-flex justify-content-end gap-2">
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
    import { validate, showErrors, clearErrors, useToast, initPageToast } from '/shared/app.js';

    const { show: showToast } = useToast();
    initPageToast(<?= json_encode($message ?? '') ?>);

    const form = document.getElementById('formAssign');
    form.addEventListener('submit', (e) => {
      clearErrors(form);
      const errors = validate({
        event_id: { value: form.event_id.value, label: 'Event', rules: ['required'] },
        user_id: { value: form.user_id.value, label: 'Personel', rules: ['required'] },
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
          form.reset();
          const modal = bootstrap.Modal.getInstance(document.getElementById('modalAdd'));
          modal.hide();
          showToast('Penugasan tersimpan', 'success');
          window.location.reload();
        })
        .catch(() => {
          showToast('Gagal menyimpan', 'danger');
        });
    });
  </script>
</body>
</html>
