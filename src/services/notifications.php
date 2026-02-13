<?php
require_once __DIR__ . '/../support/db.php';

function list_notifications(PDO $pdo): array
{
    return $pdo->query("SELECT n.*, e.title AS event_title FROM notifications n JOIN events e ON n.event_id = e.id ORDER BY n.send_at ASC")
               ->fetchAll();
}

function update_notification_status(PDO $pdo, int $id, string $status = 'sent'): void
{
    $stmt = $pdo->prepare("UPDATE notifications SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);
}

function process_due_notifications(PDO $pdo): int
{
    $now = (new DateTime())->format('Y-m-d H:i:s');
    $stmt = $pdo->prepare("SELECT id FROM notifications WHERE status = 'pending' AND send_at <= :now");
    $stmt->execute([':now' => $now]);
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        update_notification_status($pdo, (int)$r['id'], 'sent');
    }
    return count($rows);
}
