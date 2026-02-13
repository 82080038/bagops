<?php
// Router sederhana berbasis query parameter ?r=path
// Contoh: /?r=renops.form

$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['app']['timezone'] ?? 'Asia/Jakarta');

require_once __DIR__ . '/support/db.php';
require_once __DIR__ . '/support/view.php';
require_once __DIR__ . '/support/response.php';
require_once __DIR__ . '/services/renops.php';
require_once __DIR__ . '/services/personnel.php';
require_once __DIR__ . '/services/events.php';
require_once __DIR__ . '/services/assignments.php';
require_once __DIR__ . '/services/notifications.php';
require_once __DIR__ . '/services/documents.php';

$r = $_GET['r'] ?? 'home';

$routes = [
    'home' => function () {
        return view('home', [
            'title' => 'BAGOPS App',
            'message' => 'Selamat datang di aplikasi BAGOPS (draft)'
        ]);
    },
    'renops.form' => function () {
        return view('renops_form', [
            'title' => 'Form RENOPS',
        ]);
    },
    'renops.submit' => function () {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return 'Method Not Allowed';
        }
        $pdo = db_connection();
        // Kumpulkan data dasar
        $event = [
            'title' => trim($_POST['event_title'] ?? ''),
            'type' => trim($_POST['event_type'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'latitude' => $_POST['latitude'] ?? null,
            'longitude' => $_POST['longitude'] ?? null,
            'start_at' => $_POST['start_at'] ?? null,
            'end_at' => $_POST['end_at'] ?? null,
            'risk_level' => trim($_POST['risk_level'] ?? ''),
            'notes' => null,
        ];

        if ($event['title'] === '') {
            return 'Judul operasi wajib diisi';
        }

        $renops = [
            'doc_no' => trim($_POST['doc_no'] ?? ''),
            'command_basis' => trim($_POST['command_basis'] ?? ''),
            'intel_summary' => trim($_POST['intel_summary'] ?? ''),
            'objectives' => trim($_POST['objectives'] ?? ''),
            'forces' => trim($_POST['forces'] ?? ''),
            'comms_plan' => trim($_POST['comms_plan'] ?? ''),
            'contingency_plan' => trim($_POST['contingency_plan'] ?? ''),
            'logistics_plan' => trim($_POST['logistics_plan'] ?? ''),
            'coordination' => trim($_POST['coordination'] ?? ''),
            'attachments' => null,
        ];

        $result = save_renops($pdo, $event, $renops);

        return view('renops_submit_result', [
            'title' => 'RENOPS Tersimpan',
            'result' => $result,
        ]);
    },
    'personnel' => function () {
        $pdo = db_connection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            add_personnel($pdo, $_POST);
            header('Location: /?r=personnel&msg=' . urlencode('Data personel tersimpan'));
            exit;
        }
        $filters = [
            'rank' => $_GET['rank'] ?? '',
            'position' => $_GET['position'] ?? '',
        ];
        $list = list_personnel($pdo, $filters);
        $msg = $_GET['msg'] ?? '';
        return view('personnel', [
            'title' => 'Personel',
            'list' => $list,
            'message' => $msg,
            'filters' => $filters,
        ]);
    },
    'personnel.import' => function () {
        $pdo = db_connection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            // kosongkan assignments dan users
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
            $pdo->exec('TRUNCATE TABLE assignments');
            $pdo->exec('TRUNCATE TABLE users');
            $count = import_personnel_file($pdo, $_FILES['file']);
            $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
            header('Location: /?r=personnel&msg=' . urlencode('Import selesai: ' . $count . ' baris'));
            exit;
        }
        header('Location: /?r=personnel&msg=' . urlencode('File tidak valid'));
        exit;
    },
    'events' => function () {
        $pdo = db_connection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            add_event($pdo, $_POST);
            header('Location: /?r=events&msg=' . urlencode('Event tersimpan'));
            exit;
        }
        $list = list_events($pdo);
        $msg = $_GET['msg'] ?? '';
        return view('events', ['title' => 'Event/Operasi', 'list' => $list, 'message' => $msg]);
    },
    'assignments' => function () {
        $pdo = db_connection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            add_assignment($pdo, $_POST);
            header('Location: /?r=assignments&msg=' . urlencode('Penugasan tersimpan'));
            exit;
        }
        $events = list_events($pdo);
        $users = list_personnel($pdo);
        $list = list_assignments($pdo);
        return view('assignments', [
            'title' => 'Penugasan Personel',
            'events' => $events,
            'users' => $users,
            'list' => $list,
            'message' => $_GET['msg'] ?? '',
        ]);
    },
    'reminders' => function () {
        $pdo = db_connection();
        $list = list_notifications($pdo);
        $msg = $_GET['msg'] ?? '';
        return view('reminders', ['title' => 'Reminder (H-3/H-1)', 'list' => $list, 'message' => $msg]);
    },
    'reminders.run' => function () {
        $pdo = db_connection();
        $count = process_due_notifications($pdo);
        header('Location: /?r=reminders&msg=' . urlencode("Reminder diproses: $count ter-update"));
        exit;
    },
    'documents' => function () {
        $pdo = db_connection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            add_document($pdo, $_POST, $_FILES['file'] ?? null);
            header('Location: /?r=documents&msg=' . urlencode('Dokumen diunggah'));
            exit;
        }
        $events = list_events($pdo);
        $docs = list_documents($pdo);
        return view('documents', ['title' => 'Lampiran Dokumen', 'events' => $events, 'docs' => $docs, 'message' => $_GET['msg'] ?? '']);
    },
];

if (isset($routes[$r])) {
    echo $routes[$r]();
    exit;
}

http_response_code(404);
echo '404 Not Found';
