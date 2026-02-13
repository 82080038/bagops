<?php
require_once __DIR__ . '/../support/db.php';

function add_personnel(PDO $pdo, array $data): void
{
    $stmt = $pdo->prepare("INSERT INTO users (urut, name, rank, nrp, position, phone, ket, role) VALUES (:urut, :name, :rank, :nrp, :position, :phone, :ket, :role)");
    $stmt->execute([
        ':urut' => $data['urut'] !== '' ? $data['urut'] : null,
        ':name' => trim($data['name'] ?? ''),
        ':rank' => trim($data['rank'] ?? ''),
        ':nrp' => trim($data['nrp'] ?? ''),
        ':position' => trim($data['position'] ?? ''),
        ':phone' => trim($data['phone'] ?? ''),
        ':ket' => trim($data['ket'] ?? ''),
        ':role' => trim($data['role'] ?? 'user'),
    ]);
}

function list_personnel(PDO $pdo, array $filters = []): array
{
    $sql = "SELECT * FROM users WHERE 1=1";
    $params = [];
    if (!empty($filters['rank'])) {
        $sql .= " AND rank = :rank";
        $params[':rank'] = $filters['rank'];
    }
    if (!empty($filters['position'])) {
        $sql .= " AND position LIKE :position";
        $params[':position'] = '%' . $filters['position'] . '%';
    }
    $sql .= " ORDER BY name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function import_personnel_csv(PDO $pdo, array $file): int
{
    if (empty($file['tmp_name'])) return 0;
    $path = $file['tmp_name'];
    $handle = fopen($path, 'r');
    if (!$handle) return 0;
    // skip header
    fgetcsv($handle);
    $count = 0;
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 7) continue;
        [$no, $urut, $name, $rank, $nrp, $position, $ket] = $row;
        $stmt = $pdo->prepare("INSERT INTO users (urut, name, rank, nrp, position, ket, role) VALUES (:urut, :name, :rank, :nrp, :position, :ket, 'user')");
        $stmt->execute([
            ':urut' => $urut !== '' ? $urut : null,
            ':name' => $name,
            ':rank' => $rank,
            ':nrp' => $nrp,
            ':position' => $position,
            ':ket' => $ket,
        ]);
        $count++;
    }
    fclose($handle);
    return $count;
}

function import_personnel_file(PDO $pdo, array $file): int
{
    if (empty($file['tmp_name'])) return 0;
    $name = $file['name'] ?? '';
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    // Jika CSV langsung pakai import_personnel_csv
    if ($ext === 'csv') {
        return import_personnel_csv($pdo, $file);
    }
    // Jika XLSX, konversi ke CSV via python3 + pandas
    if ($ext === 'xlsx') {
        $tmpCsv = tempnam(sys_get_temp_dir(), 'pers_') . '.csv';
        $cmd = "python3 - <<'PY'\nimport pandas as pd\nimport sys\ntry:\n    df = pd.read_excel(sys.argv[1])\n    df.to_csv(sys.argv[2], index=False)\nexcept Exception as e:\n    sys.stderr.write(str(e))\n    sys.exit(1)\nPY \"{$file['tmp_name']}\" \"$tmpCsv\"";
        exec($cmd, $out, $ret);
        if ($ret !== 0 || !file_exists($tmpCsv)) {
            if (file_exists($tmpCsv)) @unlink($tmpCsv);
            return 0;
        }
        $csvFile = [
            'tmp_name' => $tmpCsv,
            'name' => basename($tmpCsv),
        ];
        $count = import_personnel_csv($pdo, $csvFile);
        @unlink($tmpCsv);
        return $count;
    }
    return 0;
}
