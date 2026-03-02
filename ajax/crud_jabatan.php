<?php
// CRUD operations for master jabatan

require_once '../config/database.php';

$db = (new Database())->getConnection();

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Action not specified'];

switch ($action) {
    case 'create':
        $jabatan = $_POST['jabatan'] ?? '';
        $kantorType = $_POST['kantor_type'] ?? '';
        $unsur = $_POST['unsur'] ?? '';
        
        if (!$jabatan || !$kantorType || !$unsur) {
            $response['message'] = 'Semua field harus diisi';
            break;
        }
        
        $stmt = $db->prepare("INSERT INTO master_jabatan (jabatan, kantor_type, unsur) VALUES (?, ?, ?)");
        if ($stmt->execute([$jabatan, $kantorType, $unsur])) {
            $response['success'] = true;
            $response['message'] = 'Jabatan berhasil ditambahkan';
        } else {
            $response['message'] = 'Gagal menambahkan jabatan';
        }
        break;
        
    case 'read':
        $stmt = $db->prepare("SELECT mj.*, mkt.type_name FROM master_jabatan mj LEFT JOIN master_kantor_type mkt ON mj.kantor_type = mkt.type_code ORDER BY mj.kantor_type, mj.unsur, mj.jabatan");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['success'] = true;
        $response['data'] = $data;
        break;
        
    case 'update':
        $id = $_POST['id'] ?? 0;
        $jabatan = $_POST['jabatan'] ?? '';
        $kantorType = $_POST['kantor_type'] ?? '';
        $unsur = $_POST['unsur'] ?? '';
        
        if (!$id || !$jabatan || !$kantorType || !$unsur) {
            $response['message'] = 'Semua field harus diisi';
            break;
        }
        
        $stmt = $db->prepare("UPDATE master_jabatan SET jabatan = ?, kantor_type = ?, unsur = ? WHERE id = ?");
        if ($stmt->execute([$jabatan, $kantorType, $unsur, $id])) {
            $response['success'] = true;
            $response['message'] = 'Jabatan berhasil diupdate';
        } else {
            $response['message'] = 'Gagal update jabatan';
        }
        break;
        
    case 'delete':
        $id = $_POST['id'] ?? 0;
        
        if (!$id) {
            $response['message'] = 'ID tidak valid';
            break;
        }
        
        // Check if jabatan is used in kantor_positions
        $checkStmt = $db->prepare("SELECT COUNT(*) FROM kantor_positions WHERE master_jabatan_id = ?");
        $checkStmt->execute([$id]);
        $count = $checkStmt->fetchColumn();
        
        if ($count > 0) {
            $response['message'] = 'Jabatan tidak dapat dihapus karena sudah digunakan';
            break;
        }
        
        $stmt = $db->prepare("DELETE FROM master_jabatan WHERE id = ?");
        if ($stmt->execute([$id])) {
            $response['success'] = true;
            $response['message'] = 'Jabatan berhasil dihapus';
        } else {
            $response['message'] = 'Gagal hapus jabatan';
        }
        break;
        
    case 'toggle_active':
        $id = $_POST['id'] ?? 0;
        
        if (!$id) {
            $response['message'] = 'ID tidak valid';
            break;
        }
        
        $stmt = $db->prepare("UPDATE master_jabatan SET is_active = NOT is_active WHERE id = ?");
        if ($stmt->execute([$id])) {
            $response['success'] = true;
            $response['message'] = 'Status jabatan berhasil diubah';
        } else {
            $response['message'] = 'Gagal ubah status jabatan';
        }
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
