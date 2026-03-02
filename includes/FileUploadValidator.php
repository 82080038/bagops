<?php
/**
 * Advanced File Upload Validation Utility
 * Provides comprehensive file upload validation and security checks
 */

class FileUploadValidator {
    private $maxFileSize;
    private $allowedTypes;
    private $allowedExtensions;
    private $uploadPath;
    private $errors = [];

    // MIME type mappings
    private $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'txt' => 'text/plain',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed'
    ];

    // Dangerous file signatures (first few bytes)
    private $dangerousSignatures = [
        'php' => ['<?php', '<?=', '<%', '<script'],
        'js' => ['<script', 'javascript:', 'eval(', 'function('],
        'exe' => ['MZ'], // Windows executable
        'bat' => ['@echo', 'cmd.exe'],
        'com' => ['MZ']
    ];

    public function __construct($config = []) {
        $this->maxFileSize = $config['max_size'] ?? 2097152; // 2MB default
        $this->allowedTypes = $config['allowed_types'] ?? ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        $this->allowedExtensions = $config['allowed_extensions'] ?? ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $this->uploadPath = $config['upload_path'] ?? '../storage/uploads/';
    }

    /**
     * Validate uploaded file
     */
    public function validateFile($file) {
        $this->errors = [];

        // Check if file was uploaded
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadError($file['error'] ?? UPLOAD_ERR_NO_FILE);
            return false;
        }

        // Validate file size
        if (!$this->validateFileSize($file['size'])) {
            return false;
        }

        // Validate file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!$this->validateExtension($extension)) {
            return false;
        }

        // Validate MIME type
        if (!$this->validateMimeType($file['tmp_name'], $file['type'], $extension)) {
            return false;
        }

        // Security checks
        if (!$this->performSecurityChecks($file['tmp_name'], $extension)) {
            return false;
        }

        // Additional validation for images
        if ($this->isImage($extension)) {
            if (!$this->validateImage($file['tmp_name'])) {
                return false;
            }
        }

        return empty($this->errors);
    }

    /**
     * Validate file size
     */
    private function validateFileSize($size) {
        if ($size > $this->maxFileSize) {
            $this->errors[] = "Ukuran file terlalu besar. Maksimal " . $this->formatBytes($this->maxFileSize);
            return false;
        }

        if ($size == 0) {
            $this->errors[] = "File kosong atau tidak valid";
            return false;
        }

        return true;
    }

    /**
     * Validate file extension
     */
    private function validateExtension($extension) {
        if (!in_array($extension, $this->allowedExtensions)) {
            $this->errors[] = "Tipe file '{$extension}' tidak diizinkan. Tipe yang diizinkan: " . implode(', ', $this->allowedExtensions);
            return false;
        }
        return true;
    }

    /**
     * Validate MIME type using multiple methods
     */
    private function validateMimeType($filePath, $reportedType, $extension) {
        // Check reported MIME type
        $expectedMime = $this->mimeTypes[$extension] ?? null;
        if ($expectedMime && $reportedType !== $expectedMime) {
            // Some browsers report different MIME types, so we'll be more lenient
            // but still perform additional checks
        }

        // Use finfo for MIME type detection
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $detectedType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            // For images, be more flexible with MIME type detection
            if ($this->isImage($extension)) {
                $validImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($detectedType, $validImageTypes)) {
                    $this->errors[] = "Tipe file gambar tidak valid (detected: {$detectedType})";
                    return false;
                }
            } else {
                // For non-images, be stricter
                if ($expectedMime && $detectedType !== $expectedMime) {
                    $this->errors[] = "MIME type tidak cocok (expected: {$expectedMime}, detected: {$detectedType})";
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Perform security checks on file content
     */
    private function performSecurityChecks($filePath, $extension) {
        // Check for dangerous file signatures
        if (isset($this->dangerousSignatures[$extension])) {
            $fileContent = file_get_contents($filePath, false, null, 0, 1024); // Read first 1KB

            foreach ($this->dangerousSignatures[$extension] as $signature) {
                if (stripos($fileContent, $signature) !== false) {
                    $this->errors[] = "File mengandung konten berbahaya atau tidak diizinkan";
                    return false;
                }
            }
        }

        // Check for null bytes (common in file upload attacks)
        $fileContent = file_get_contents($filePath, false, null, 0, 512);
        if (strpos($fileContent, "\0") !== false) {
            $this->errors[] = "File mengandung karakter tidak valid";
            return false;
        }

        // Additional security check: file name validation
        $filename = basename($filePath);
        if (preg_match('/[<>\"\|\?\*\:]/', $filename)) {
            $this->errors[] = "Nama file mengandung karakter tidak valid";
            return false;
        }

        return true;
    }

    /**
     * Validate image files
     */
    private function validateImage($filePath) {
        $imageInfo = getimagesize($filePath);

        if (!$imageInfo) {
            $this->errors[] = "File gambar tidak valid atau korup";
            return false;
        }

        // Check image dimensions (prevent extremely large images)
        $maxWidth = 5000;
        $maxHeight = 5000;

        if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
            $this->errors[] = "Dimensi gambar terlalu besar (maksimal {$maxWidth}x{$maxHeight} pixels)";
            return false;
        }

        // Check minimum dimensions (prevent 1x1 pixel attacks)
        $minWidth = 10;
        $minHeight = 10;

        if ($imageInfo[0] < $minWidth || $imageInfo[1] < $minHeight) {
            $this->errors[] = "Dimensi gambar terlalu kecil (minimal {$minWidth}x{$minHeight} pixels)";
            return false;
        }

        return true;
    }

    /**
     * Move uploaded file to destination with unique name
     */
    public function moveUploadedFile($file, $customName = null) {
        // Ensure upload directory exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        // Generate unique filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $basename = $customName ?: uniqid('upload_', true);
        $filename = $basename . '.' . $extension;

        // Ensure filename is unique
        $counter = 1;
        while (file_exists($this->uploadPath . $filename)) {
            $filename = $basename . '_' . $counter . '.' . $extension;
            $counter++;
        }

        $destination = $this->uploadPath . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Set proper permissions
            chmod($destination, 0644);
            return $filename;
        }

        $this->errors[] = "Gagal memindahkan file ke direktori tujuan";
        return false;
    }

    /**
     * Get all validation errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get first error message
     */
    public function getError() {
        return !empty($this->errors) ? $this->errors[0] : null;
    }

    /**
     * Check if file extension is for image
     */
    private function isImage($extension) {
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Get upload error message
     */
    private function getUploadError($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return "Ukuran file melebihi batas upload_max_filesize di php.ini";
            case UPLOAD_ERR_FORM_SIZE:
                return "Ukuran file melebihi batas MAX_FILE_SIZE yang ditentukan dalam form HTML";
            case UPLOAD_ERR_PARTIAL:
                return "File hanya terupload sebagian";
            case UPLOAD_ERR_NO_FILE:
                return "Tidak ada file yang diupload";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Folder temporary tidak ditemukan";
            case UPLOAD_ERR_CANT_WRITE:
                return "Gagal menulis file ke disk";
            case UPLOAD_ERR_EXTENSION:
                return "Upload dihentikan oleh ekstensi PHP";
            default:
                return "Terjadi error tidak diketahui saat upload";
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < 3) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Sanitize filename for security
     */
    public static function sanitizeFilename($filename) {
        // Remove any path information
        $filename = basename($filename);

        // Replace dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Ensure filename is not too long
        $maxLength = 255;
        if (strlen($filename) > $maxLength) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            $nameWithoutExt = substr($nameWithoutExt, 0, $maxLength - strlen($extension) - 1);
            $filename = $nameWithoutExt . '.' . $extension;
        }

        return $filename;
    }
}

// Utility function for quick file validation
function validateFileUpload($file, $config = []) {
    $validator = new FileUploadValidator($config);
    return $validator->validateFile($file);
}

// Utility function to handle file upload with validation
function handleFileUpload($fileInput, $config = []) {
    $validator = new FileUploadValidator($config);

    if ($validator->validateFile($fileInput)) {
        $filename = $validator->moveUploadedFile($fileInput);
        if ($filename) {
            return [
                'success' => true,
                'filename' => $filename,
                'original_name' => $fileInput['name'],
                'size' => $fileInput['size'],
                'type' => $fileInput['type']
            ];
        } else {
            return [
                'success' => false,
                'error' => $validator->getError()
            ];
        }
    } else {
        return [
            'success' => false,
            'error' => $validator->getError(),
            'all_errors' => $validator->getErrors()
        ];
    }
}
?>
