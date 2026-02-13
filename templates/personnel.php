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
          <tr><th>Nama</th><th>Pangkat</th><th>NRP</th><th>Jabatan</th><th>Telepon</th><th>Role</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php foreach ($list as $p): ?>
          <tr data-id="<?= (int)$p['id'] ?>">
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['rank']) ?></td>
            <td><?= htmlspecialchars($p['nrp']) ?></td>
            <td><?= htmlspecialchars($p['position']) ?></td>
            <td><?= htmlspecialchars($p['phone']) ?></td>
            <td><?= htmlspecialchars($p['role']) ?></td>
            <td class="text-nowrap">
              <button class="btn btn-sm btn-outline-primary btn-edit" data-bs-toggle="modal" data-bs-target="#modalAdd"
                data-id="<?= (int)$p['id'] ?>"
                data-name="<?= htmlspecialchars($p['name']) ?>"
                data-rank="<?= htmlspecialchars($p['rank']) ?>"
                data-nrp="<?= htmlspecialchars($p['nrp']) ?>"
                data-position="<?= htmlspecialchars($p['position']) ?>"
                data-phone="<?= htmlspecialchars($p['phone']) ?>"
                data-role="<?= htmlspecialchars($p['role']) ?>"
                data-urut="<?= htmlspecialchars($p['urut']) ?>"
                data-ket="<?= htmlspecialchars($p['ket']) ?>"
              >Edit</button>
              <form class="d-inline" method="post" action="/?r=personnel" onsubmit="return confirm('Hapus data ini?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
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
          <h5 class="modal-title">Tambah Personel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formPersonel" method="post" action="/?r=personnel">
            <input type="hidden" name="action" value="create" id="actionField">
            <input type="hidden" name="id" value="">
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
            <div class="mb-3">
              <label class="form-label">Urut</label>
              <input class="form-control" name="urut">
            </div>
            <div class="mb-3">
              <label class="form-label">Keterangan</label>
              <textarea class="form-control" name="ket" rows="2"></textarea>
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
          form.reset();
          document.getElementById('actionField').value = 'create';
          form.id.value = '';
          const modal = bootstrap.Modal.getInstance(document.getElementById('modalAdd'));
          modal.hide();
          showToast('Data personel tersimpan', 'success');
          window.location.reload();
        })
        .catch(() => {
          showToast('Gagal menyimpan', 'danger');
        });
    });

    // Prefill edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('actionField').value = 'update';
        form.id.value = btn.dataset.id;
        form.name.value = btn.dataset.name;
        form.rank.value = btn.dataset.rank;
        form.nrp.value = btn.dataset.nrp;
        form.position.value = btn.dataset.position;
        form.phone.value = btn.dataset.phone;
        form.role.value = btn.dataset.role;
        form.urut.value = btn.dataset.urut;
        form.ket.value = btn.dataset.ket;
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
