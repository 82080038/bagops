<?php
require_once __DIR__ . '/../support/db.php';

function add_document(PDO $pdo, array $data, ?array $file): void
{
    $eventId = (int)($data['event_id'] ?? 0);
    if ($eventId <= 0 || !$file || empty($file['tmp_name'])) {
        return;
    }
    // Batas ukuran 2MB
    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        throw new RuntimeException('File terlalu besar, maksimum 2MB');
    }
    // Validasi tipe sederhana
    $allowed = ['application/pdf','image/jpeg','image/png','text/plain'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if ($mime && !in_array($mime, $allowed, true)) {
        throw new RuntimeException('Tipe file tidak didukung');
    }
    $orig = $file['name'] ?? 'lampiran';
    $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
    $targetDir = __DIR__ . '/../../storage/lampiran/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }
    $targetPath = $targetDir . $safeName;
    move_uploaded_file($file['tmp_name'], $targetPath);
    $stmt = $pdo->prepare("INSERT INTO documents (event_id, type, path, original_name) VALUES (:event_id, :type, :path, :original_name)");
    $stmt->execute([
        ':event_id' => $eventId,
        ':type' => $data['type'] ?? 'lampiran',
        ':path' => 'storage/lampiran/' . $safeName,
        ':original_name' => $orig,
    ]);
}

function list_documents(PDO $pdo): array
{
    $sql = "SELECT d.*, e.title AS event_title FROM documents d JOIN events e ON d.event_id = e.id ORDER BY d.id DESC";
    return $pdo->query($sql)->fetchAll();
}

function delete_document(PDO $pdo, int $id): void
{
    // Hapus file fisik jika ada
    $stmt = $pdo->prepare("SELECT path FROM documents WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['path'])) {
        $fullPath = __DIR__ . '/../../' . $row['path'];
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
    $del = $pdo->prepare("DELETE FROM documents WHERE id = :id");
    $del->execute([':id' => $id]);
}
