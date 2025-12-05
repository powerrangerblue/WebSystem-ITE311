<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    public function upload($course_id)
    {
        helper(['form']);

        $courseId = (int) $course_id;
        $session = session();

        if ($this->request->getMethod() === 'POST') {
            $validationRules = [
                'material_file' => [
                    'label' => 'Material File',
                    'rules' => 'uploaded[material_file]|max_size[material_file,10240]|ext_in[material_file,pdf,doc,docx,ppt,pptx,xls,xlsx,zip,rar,txt]',
                ],
            ];

            if (!$this->validate($validationRules)) {
                $session->setFlashdata('error', implode(' ', $this->validator->getErrors()));
                return redirect()->back();
            }

            $file = $this->request->getFile('material_file');
            if (!$file || !$file->isValid()) {
                $session->setFlashdata('error', 'Invalid file upload.');
                return redirect()->back();
            }

            $uploadDir = FCPATH . 'uploads/materials';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $storedName = $file->getRandomName();
            if (!$file->move($uploadDir, $storedName)) {
                $session->setFlashdata('error', 'Failed to move uploaded file.');
                return redirect()->back();
            }

            $relativePath = 'uploads/materials/' . $storedName;

            $materialModel = new MaterialModel();
            $insertId = $materialModel->insertMaterial([
                'course_id' => $courseId,
                'file_name' => $file->getClientName(),
                'file_path' => $relativePath,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($insertId) {
                $session->setFlashdata('success', 'Material uploaded successfully.');
            } else {
                $session->setFlashdata('error', 'Failed to save material record.');
            }

            return redirect()->to('/dashboard');
        }

        return view('upload_material', ['course_id' => $courseId]);
    }

    public function delete($material_id)
    {
        $session = session();
        $materialId = (int) $material_id;

        $materialModel = new MaterialModel();
        $material = $materialModel->find($materialId);
        if (!$material) {
            $session->setFlashdata('error', 'Material not found.');
            return redirect()->back();
        }

        $filePath = FCPATH . $material['file_path'];
        if (is_file($filePath)) {
            @unlink($filePath);
        }

        $materialModel->delete($materialId);
        $session->setFlashdata('success', 'Material deleted successfully.');
        return redirect()->back();
    }

    public function download($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $userId = (int) $session->get('user_id');
        $materialId = (int) $material_id;

        $materialModel = new MaterialModel();
        $material = $materialModel->find($materialId);
        if (!$material) {
            return $this->response->setStatusCode(404, 'Material not found');
        }

        $enrollmentModel = new EnrollmentModel();
        $isEnrolled = $enrollmentModel->isAlreadyEnrolled($userId, (int) $material['course_id']);
        if (!$isEnrolled) {
            return $this->response->setStatusCode(403, 'Access denied');
        }

        $absolutePath = FCPATH . $material['file_path'];
        if (!is_file($absolutePath)) {
            return $this->response->setStatusCode(404, 'File missing');
        }

        return $this->response->download($absolutePath, null)->setFileName($material['file_name']);
    }

    public function listByCourse($course_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
        }

        $userId = (int) $session->get('user_id');
        $courseId = (int) $course_id;

        $enrollmentModel = new EnrollmentModel();
        $isEnrolled = $enrollmentModel->isAlreadyEnrolled($userId, $courseId);
        if (!$isEnrolled) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not enrolled']);
        }

        $materialModel = new MaterialModel();
        $materials = $materialModel->getMaterialsByCourse($courseId);

        return $this->response->setJSON([
            'success' => true,
            'materials' => $materials
        ]);
    }
}
