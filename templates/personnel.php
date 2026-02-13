<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Personel') ?></title>
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
        <a class="nav-link active" href="/?r=personnel">Personel</a>
        <a class="nav-link" href="/?r=events">Event</a>
        <a class="nav-link" href="/?r=assignments">Penugasan</a>
        <a class="nav-link" href="/?r=reminders">Reminder</a>
      </div>
    </div>
  </nav>

  <div class="container mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Personel</h1>
      <div class="d-flex gap-2">
        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalImport">Import CSV/XLSX</button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah Personel</button>
      </div>
    </div>
    <?php if (!empty($message)): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <div id="alertArea"></div>

    <form class="row g-2 align-items-end mb-3" method="get" action="/?r=personnel">
      <input type="hidden" name="r" value="personnel">
      <div class="col-md-3">
        <label class="form-label">Filter Pangkat</label>
        <input class="form-control" name="rank" value="<?= htmlspecialchars($filters['rank'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Filter Jabatan</label>
        <input class="form-control" name="position" value="<?= htmlspecialchars($filters['position'] ?? '') ?>">
      </div>
      <div class="col-md-3">
        <button class="btn btn-outline-primary" type="submit">Terapkan Filter</button>
      </div>
    </form>

    <div class="table-responsive bg-white rounded shadow-sm p-3">
      <table class="table table-sm table-striped align-middle mb-0">
        <thead class="table-light">
          <tr><th>Nama</th><th>Pangkat</th><th>NRP</th><th>Jabatan</th><th>Telepon</th><th>Role</th></tr>
        </thead>
        <tbody>
        <?php foreach ($list as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['rank']) ?></td>
            <td><?= htmlspecialchars($p['nrp']) ?></td>
            <td><?= htmlspecialchars($p['position']) ?></td>
            <td><?= htmlspecialchars($p['phone']) ?></td>
            <td><?= htmlspecialchars($p['role']) ?></td>
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
          <h5 class="modal-title">Tambah Personel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formPersonel" method="post" action="/?r=personnel">
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input class="form-control" name="name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Pangkat</label>
              <input class="form-control" name="rank">
            </div>
            <div class="mb-3">
              <label class="form-label">NRP</label>
              <input class="form-control" name="nrp">
            </div>
            <div class="mb-3">
              <label class="form-label">Jabatan</label>
              <input class="form-control" name="position">
            </div>
            <div class="mb-3">
              <label class="form-label">Telepon</label>
              <input class="form-control" name="phone">
            </div>
            <div class="mb-3">
              <label class="form-label">Role</label>
              <input class="form-control" name="role" value="user">
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
    import { validate, showErrors, clearErrors, normalizePhone, isValidPhone, useToast, initPageToast } from '/shared/app.js';

    const { show: showToast } = useToast();
    initPageToast(<?= json_encode($message ?? '') ?>);

    const form = document.getElementById('formPersonel');
    form.addEventListener('submit', (e) => {
      clearErrors(form);
      const phone = form.phone.value;
      const normalizedPhone = normalizePhone(phone);
      if (phone) form.phone.value = normalizedPhone;
      const errors = validate({
        name: { value: form.name.value, label: 'Nama', rules: ['required'] },
        phone: phone ? { value: normalizedPhone, label: 'Telepon', rules: [[(v)=>isValidPhone(v), '']] } : { value: '', rules: [] },
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
          // Tambah row baru secara lokal
          const tbody = document.querySelector('table tbody');
          const tr = document.createElement('tr');
          tr.innerHTML = `<td>${form.name.value}</td><td>${form.rank.value}</td><td>${form.nrp.value}</td><td>${form.position.value}</td><td>${form.phone.value}</td><td>${form.role.value}</td>`;
          tbody.prepend(tr);
          form.reset();
          const modal = bootstrap.Modal.getInstance(document.getElementById('modalAdd'));
          modal.hide();
          showToast('Data personel tersimpan', 'success');
        })
        .catch(() => {
          showToast('Gagal menyimpan', 'danger');
        });
    });

    const formImport = document.getElementById('formImport');
    formImport.addEventListener('submit', (e) => {
      e.preventDefault();
      if (!confirm('Import akan mengosongkan data personel dan penugasan, lanjutkan?')) return;
      const fd = new FormData(formImport);
      fetch(formImport.action, { method: 'POST', body: fd })
        .then(() => {
          formImport.reset();
          const modal = bootstrap.Modal.getInstance(document.getElementById('modalImport'));
          modal.hide();
          showToast('Import berhasil, data diperbarui', 'success');
          // tidak refresh tabel lokal, user dapat refresh halaman
        })
        .catch(() => showToast('Import gagal', 'danger'));
    });
  </script>
</body>
</html>
