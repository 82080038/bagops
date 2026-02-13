<?php
require_once __DIR__ . '/../support/db.php';
require_once __DIR__ . '/pdf.php';
require_once __DIR__ . '/assignments.php';

function save_renops(PDO $pdo, array $event, array $renops): array
{
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO events (title, type, location, latitude, longitude, start_at, end_at, risk_level, notes) VALUES (:title, :type, :location, :latitude, :longitude, :start_at, :end_at, :risk_level, :notes)");
        $stmt->execute([
            ':title' => $event['title'],
            ':type' => $event['type'],
            ':location' => $event['location'],
            ':latitude' => $event['latitude'] !== '' ? $event['latitude'] : null,
            ':longitude' => $event['longitude'] !== '' ? $event['longitude'] : null,
            ':start_at' => $event['start_at'] !== '' ? $event['start_at'] : null,
            ':end_at' => $event['end_at'] !== '' ? $event['end_at'] : null,
            ':risk_level' => $event['risk_level'],
            ':notes' => $event['notes'],
        ]);
        $eventId = (int)$pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO renops (event_id, doc_no, command_basis, intel_summary, objectives, forces, comms_plan, contingency_plan, logistics_plan, coordination, attachments) VALUES (:event_id, :doc_no, :command_basis, :intel_summary, :objectives, :forces, :comms_plan, :contingency_plan, :logistics_plan, :coordination, :attachments)");
        $stmt->execute([
            ':event_id' => $eventId,
            ':doc_no' => $renops['doc_no'],
            ':command_basis' => $renops['command_basis'],
            ':intel_summary' => $renops['intel_summary'],
            ':objectives' => $renops['objectives'],
            ':forces' => $renops['forces'],
            ':comms_plan' => $renops['comms_plan'],
            ':contingency_plan' => $renops['contingency_plan'],
            ':logistics_plan' => $renops['logistics_plan'],
            ':coordination' => $renops['coordination'],
            ':attachments' => $renops['attachments'],
        ]);

        // Jadwalkan pengingat H-3 dan H-1 jika ada start_at
        if (!empty($event['start_at'])) {
            $start = new DateTime($event['start_at']);
            $h3 = clone $start; $h3->modify('-3 days');
            $h1 = clone $start; $h1->modify('-1 day');
            $insNotif = $pdo->prepare("INSERT INTO notifications (event_id, send_at, channel, message, status) VALUES (:event_id, :send_at, 'in-app', :message, 'pending')");
            $msg = 'Reminder operasi: ' . $event['title'];
            $insNotif->execute([':event_id' => $eventId, ':send_at' => $h3->format('Y-m-d H:i:s'), ':message' => $msg . ' (H-3)']);
            $insNotif->execute([':event_id' => $eventId, ':send_at' => $h1->format('Y-m-d H:i:s'), ':message' => $msg . ' (H-1)']);
        }

        // Ambil assignments untuk event ini
        $assignments = list_assignments_by_event($pdo, $eventId);

        // Generate PDF dengan assignments
        $pdfPath = generate_renops_pdf(
            array_merge($event, ['id' => $eventId]),
            $renops,
            $assignments
        );

        $pdo->commit();

        return [
            'event_id' => $eventId,
            'pdf_path' => $pdfPath,
            'message' => 'RENOPS tersimpan dan PDF berhasil dibuat.'
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['error' => $e->getMessage()];
    }
}
