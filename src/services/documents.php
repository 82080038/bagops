<?php
require_once __DIR__ . '/../support/db.php';

function add_document(PDO $pdo, array $data, ?array $file): void
{
    $eventId = (int)($data['event_id'] ?? 0);
    if ($eventId <= 0 || !$file || empty($file['tmp_name'])) {
        return;
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
