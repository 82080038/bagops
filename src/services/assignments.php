<?php
require_once __DIR__ . '/../support/db.php';

function add_assignment(PDO $pdo, array $data): void
{
    $stmt = $pdo->prepare("INSERT INTO assignments (event_id, user_id, role, sector, notes, assignment_type) VALUES (:event_id, :user_id, :role, :sector, :notes, :assignment_type)");
    $stmt->execute([
        ':event_id' => (int)($data['event_id'] ?? 0),
        ':user_id' => (int)($data['user_id'] ?? 0),
        ':role' => trim($data['role'] ?? ''),
        ':sector' => trim($data['sector'] ?? ''),
        ':notes' => trim($data['notes'] ?? ''),
        ':assignment_type' => trim($data['assignment_type'] ?? ''),
    ]);
}

function list_assignments(PDO $pdo): array
{
    $sql = "SELECT a.*, e.title AS event_title, u.name AS user_name, u.rank, u.position
            FROM assignments a
            JOIN events e ON a.event_id = e.id
            JOIN users u ON a.user_id = u.id
            ORDER BY e.start_at DESC NULLS LAST, a.id DESC";
    return $pdo->query($sql)->fetchAll();
}

function list_assignments_by_event(PDO $pdo, int $eventId): array
{
    $stmt = $pdo->prepare("SELECT a.*, u.name, u.rank, u.position FROM assignments a JOIN users u ON a.user_id = u.id WHERE a.event_id = :event_id ORDER BY a.id ASC");
    $stmt->execute([':event_id' => $eventId]);
    return $stmt->fetchAll();
}
