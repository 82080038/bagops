<?php
require_once __DIR__ . '/../support/db.php';

function add_event(PDO $pdo, array $data): void
{
    $stmt = $pdo->prepare("INSERT INTO events (title, type, location, latitude, longitude, start_at, end_at, risk_level, notes) VALUES (:title, :type, :location, :latitude, :longitude, :start_at, :end_at, :risk_level, :notes)");
    $stmt->execute([
        ':title' => trim($data['title'] ?? ''),
        ':type' => trim($data['type'] ?? ''),
        ':location' => trim($data['location'] ?? ''),
        ':latitude' => $data['latitude'] !== '' ? $data['latitude'] : null,
        ':longitude' => $data['longitude'] !== '' ? $data['longitude'] : null,
        ':start_at' => $data['start_at'] !== '' ? $data['start_at'] : null,
        ':end_at' => $data['end_at'] !== '' ? $data['end_at'] : null,
        ':risk_level' => trim($data['risk_level'] ?? ''),
        ':notes' => trim($data['notes'] ?? ''),
    ]);
}

function list_events(PDO $pdo): array
{
    return $pdo->query("SELECT * FROM events ORDER BY start_at DESC NULLS LAST, id DESC")->fetchAll();
}

function get_event(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function update_event(PDO $pdo, int $id, array $data): void
{
    $stmt = $pdo->prepare("UPDATE events SET title=:title, type=:type, location=:location, latitude=:latitude, longitude=:longitude, start_at=:start_at, end_at=:end_at, risk_level=:risk_level, notes=:notes WHERE id=:id");
    $stmt->execute([
        ':title' => trim($data['title'] ?? ''),
        ':type' => trim($data['type'] ?? ''),
        ':location' => trim($data['location'] ?? ''),
        ':latitude' => $data['latitude'] !== '' ? $data['latitude'] : null,
        ':longitude' => $data['longitude'] !== '' ? $data['longitude'] : null,
        ':start_at' => $data['start_at'] !== '' ? $data['start_at'] : null,
        ':end_at' => $data['end_at'] !== '' ? $data['end_at'] : null,
        ':risk_level' => trim($data['risk_level'] ?? ''),
        ':notes' => trim($data['notes'] ?? ''),
        ':id' => $id,
    ]);
}

function delete_event(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
    $stmt->execute([':id' => $id]);
}
