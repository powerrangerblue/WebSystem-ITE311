<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    protected $materialModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    public function upload($course_id)
    {
        // Check if user is logged in and has admin or teacher role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = strtolower((string) session()->get('role'));
        if ($role !== 'admin' && $role !== 'teacher') {
            session()->setFlashdata('error', 'Access denied. Only admins and teachers can upload materials.');
            return redirect()->to('dashboard');
        }

        if ($this->request->getMethod() === 'post') {
            // Debug: Log the upload attempt
            log_message('info', 'Material upload attempt for course_id: ' . $course_id);
            
            // Validate CSRF token
            if (!csrf_verify()) {
                log_message('error', 'CSRF token validation failed for material upload');
                session()->setFlashdata('error', 'Security token mismatch. Please try again.');
                return redirect()->back();
            }

            // Get the uploaded file
            $file = $this->request->getFile('file');
            log_message('info', 'File object received: ' . ($file ? 'YES' : 'NO'));
            
            if (!$file) {
                log_message('error', 'No file received in upload request');
                session()->setFlashdata('error', 'No file selected.');
                return redirect()->back();
            }

            // Validate file
            if (!$file->isValid()) {
                $error = $file->getError();
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive.',
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive.',
                    UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.'
                ];
                $errorMessage = isset($errorMessages[$error]) ? $errorMessages[$error] : 'File upload failed. Error code: ' . $error;
                session()->setFlashdata('error', $errorMessage);
                return redirect()->back();
            }

            // Validate file type
            $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt'];
            if (!in_array($file->getExtension(), $allowedTypes)) {
                session()->setFlashdata('error', 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes));
                return redirect()->back();
            }

            // Validate file size (10MB)
            if ($file->getSize() > 10240 * 1024) {
                session()->setFlashdata('error', 'File size exceeds 10MB limit.');
                return redirect()->back();
            }

            // Ensure upload directory exists
            $uploadPath = FCPATH . 'uploads/';
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0755, true)) {
                    session()->setFlashdata('error', 'Failed to create upload directory.');
                    return redirect()->back();
                }
            }

            // Generate unique filename
            $newName = $file->getRandomName();

            // Move file to upload directory
            log_message('info', 'Attempting to move file to: ' . $uploadPath . $newName);
            if ($file->move($uploadPath, $newName)) {
                log_message('info', 'File moved successfully to: ' . $uploadPath . $newName);
                
                // Prepare data for database
                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/' . $newName
                ];
                log_message('info', 'Data to insert: ' . json_encode($data));

                // Save to database using MaterialModel
                $insertResult = $this->materialModel->insertMaterial($data);
                log_message('info', 'Database insert result: ' . ($insertResult ? 'SUCCESS' : 'FAILED'));
                
                if ($insertResult) {
                    log_message('info', 'Material uploaded successfully for course_id: ' . $course_id);
                    session()->setFlashdata('success', 'Material uploaded successfully!');
                    return redirect()->to('course/materials/' . $course_id);
                } else {
                    // Get database errors
                    $errors = $this->materialModel->errors();
                    log_message('error', 'Database insert failed: ' . implode(', ', $errors));
                    
                    // Delete uploaded file if database insert fails
                    if (file_exists($uploadPath . $newName)) {
                        unlink($uploadPath . $newName);
                    }
                    session()->setFlashdata('error', 'Failed to save material to database: ' . implode(', ', $errors));
                }
            } else {
                $errors = $file->getErrors();
                log_message('error', 'File move failed: ' . implode(', ', $errors));
                session()->setFlashdata('error', 'Failed to upload file: ' . implode(', ', $errors));
            }

            return redirect()->back();
        }

        return view('upload_material', ['course_id' => $course_id]);
    }

    public function delete($material_id)
    {
        // Check if user is logged in and has admin or teacher role
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $role = strtolower((string) session()->get('role'));
        if ($role !== 'admin' && $role !== 'teacher') {
            session()->setFlashdata('error', 'Access denied. Only admins and teachers can delete materials.');
            return redirect()->to('dashboard');
        }

        $material = $this->materialModel->find($material_id);
        if ($material) {
            // Delete file
            $fullPath = FCPATH . $material['file_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            $this->materialModel->delete($material_id);
            session()->setFlashdata('success', 'Material deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Material not found.');
        }
        return redirect()->back();
    }

    public function download($material_id)
    {
        $material = $this->materialModel->find($material_id);
        if (!$material) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material not found');
        }

        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        // Check if user is enrolled or is admin/teacher
        $user_id = session()->get('user_id');
        $role = strtolower((string) session()->get('role'));
        
        // Allow admins and teachers to download any material
        if ($role !== 'admin' && $role !== 'teacher') {
            $enrollment = $this->enrollmentModel->where('user_id', $user_id)->where('course_id', $material['course_id'])->first();
            if (!$enrollment) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Access denied');
            }
        }

        // Get full path for download
        $fullPath = FCPATH . $material['file_path'];
        
        if (!file_exists($fullPath)) {
            log_message('error', 'File not found: ' . $fullPath);
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found: ' . $material['file_name']);
        }

        return $this->response->download($fullPath, $material['file_name']);
    }

    // Test method to check database connection
    public function test()
    {
        try {
            $db = \Config\Database::connect();
            $result = $db->query("SELECT COUNT(*) as count FROM materials")->getRow();
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Database connection working',
                'materials_count' => $result->count
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    // Debug method to test upload functionality
    public function debug($course_id)
    {
        if ($this->request->getMethod() === 'post') {
            $file = $this->request->getFile('file');
            
            $response = [
                'method' => $this->request->getMethod(),
                'file_received' => $file ? 'YES' : 'NO',
                'file_valid' => $file ? ($file->isValid() ? 'YES' : 'NO') : 'N/A',
                'file_name' => $file ? $file->getClientName() : 'N/A',
                'file_size' => $file ? $file->getSize() : 'N/A',
                'file_extension' => $file ? $file->getExtension() : 'N/A',
                'upload_path_exists' => is_dir(FCPATH . 'uploads/') ? 'YES' : 'NO',
                'upload_path_writable' => is_writable(FCPATH . 'uploads/') ? 'YES' : 'NO',
                'csrf_valid' => csrf_verify() ? 'YES' : 'NO'
            ];
            
            if ($file && $file->isValid()) {
                $uploadPath = FCPATH . 'uploads/';
                $newName = $file->getRandomName();
                $moved = $file->move($uploadPath, $newName);
                $response['file_moved'] = $moved ? 'YES' : 'NO';
                $response['new_filename'] = $newName;
                
                if ($moved) {
                    // Test database insert
                    $data = [
                        'course_id' => $course_id,
                        'file_name' => $file->getClientName(),
                        'file_path' => 'uploads/' . $newName
                    ];
                    $insertResult = $this->materialModel->insertMaterial($data);
                    $response['database_insert'] = $insertResult ? 'SUCCESS' : 'FAILED';
                    if (!$insertResult) {
                        $response['database_errors'] = $this->materialModel->errors();
                    }
                }
            }
            
            return $this->response->setJSON($response);
        }
        
        return view('upload_material', ['course_id' => $course_id]);
    }
}
