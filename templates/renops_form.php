<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Form RENOPS') ?></title>
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
    <div class="row g-3">
      <div class="col-12">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">Form RENOPS</div>
          <div class="card-body">
            <form id="formRenops" method="post" action="/?r=renops.submit">
              <div class="row g-3 mb-3">
                <div class="col-md-8">
                  <label class="form-label">Judul / Nama Operasi</label>
                  <input class="form-control" name="event_title" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Jenis (unras/razia/kunjungan/dll)</label>
                  <input class="form-control" name="event_type">
                </div>
                <div class="col-md-8">
                  <label class="form-label">Lokasi</label>
                  <input class="form-control" name="location">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Latitude</label>
                  <input class="form-control" name="latitude" type="number" step="0.0000001">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Longitude</label>
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
                  <label class="form-label">Tingkat Risiko</label>
                  <input class="form-control" name="risk_level">
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label">Nomor Dokumen</label>
                  <input class="form-control" name="doc_no">
                </div>
                <div class="col-md-12">
                  <label class="form-label">Dasar Perintah</label>
                  <textarea class="form-control" name="command_basis" rows="2"></textarea>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Intel Singkat</label>
                  <textarea class="form-control" name="intel_summary" rows="2"></textarea>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Sasaran/Keluaran</label>
                  <textarea class="form-control" name="objectives" rows="2"></textarea>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Kekuatan/Peralatan</label>
                  <textarea class="form-control" name="forces" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Rencana Komunikasi</label>
                  <textarea class="form-control" name="comms_plan" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Rencana Kontinjensi</label>
                  <textarea class="form-control" name="contingency_plan" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Rencana Logistik</label>
                  <textarea class="form-control" name="logistics_plan" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Koordinasi Eksternal</label>
                  <textarea class="form-control" name="coordination" rows="2"></textarea>
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-secondary" href="/?r=home">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan &amp; Buat PDF</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module">
    import { validate, showErrors, clearErrors, useToast, initPageToast } from '/shared/app.js';

    const { show: showToast } = useToast();
    initPageToast("");

    const form = document.getElementById('formRenops');
    form.addEventListener('submit', (e) => {
      clearErrors(form);
      const errors = validate({
        event_title: { value: form.event_title.value, label: 'Judul Operasi', rules: ['required'] },
      });
      if (Object.keys(errors).length > 0) {
        e.preventDefault();
        showErrors(form, errors);
        return;
      }
      // No AJAX for now; allow normal submit
    });
  </script>
</body>
</html>
