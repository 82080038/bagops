<?php
/**
 * Documents API Endpoints
 * AJAX handlers for document CRUD operations
 */

session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/DocumentManager.php';

header('Content-Type: application/json');

try {
    $db = (new Database())->getConnection();
    $auth = new Auth($db);
    
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit();
    }
    
    $documentManager = new DocumentManager($db, $auth);
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'upload':
            if (!isset($_FILES['document'])) {
                echo json_encode(['success' => false, 'message' => 'No file uploaded']);
                break;
            }
            
            $metadata = [
                'judul_document' => $_POST['judul_document'],
                'kategori' => $_POST['kategori'] ?? 'umum',
                'deskripsi' => $_POST['deskripsi'] ?? '',
                'access_level' => $_POST['access_level'] ?? 'internal'
            ];
            
            echo json_encode($documentManager->uploadDocument($_FILES['document'], $metadata));
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            $metadata = [
                'judul_document' => $_POST['judul_document'],
                'kategori' => $_POST['kategori'],
                'deskripsi' => $_POST['deskripsi'],
                'access_level' => $_POST['access_level']
            ];
            echo json_encode($documentManager->updateDocument($id, $metadata));
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            echo json_encode($documentManager->deleteDocument($id));
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $document = $documentManager->getDocument($id);
            if ($document) {
                echo json_encode(['success' => true, 'data' => $document]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Document not found']);
            }
            break;
            
        case 'list':
            $filters = [
                'kategori' => $_POST['kategori'] ?? null,
                'access_level' => $_POST['access_level'] ?? null,
                'search' => $_POST['search'] ?? null
            ];
            $documents = $documentManager->getDocuments($filters);
            echo json_encode(['success' => true, 'data' => $documents]);
            break;
            
        case 'download':
            $id = $_GET['id'] ?? 0;
            $result = $documentManager->downloadDocument($id);
            
            if ($result['success']) {
                $document = $result['document'];
                $filepath = $result['filepath'];
                
                // Set headers for download
                header('Content-Type: ' . $document['tipe_file']);
                header('Content-Disposition: attachment; filename="' . $document['nama_file_asli'] . '"');
                header('Content-Length: ' . $document['ukuran_file']);
                header('Cache-Control: private, no-cache, no-store, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                readfile($filepath);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => $result['message']]);
            }
            break;
            
        case 'get_stats':
            echo json_encode(['success' => true, 'data' => $documentManager->getDocumentStats()]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Documents API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
