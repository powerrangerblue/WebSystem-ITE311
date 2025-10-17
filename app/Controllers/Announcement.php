<?php
namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

class Announcement extends BaseController
{
    public function index()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login to view announcements.');
            return redirect()->to('login');
        }

        try {
            // Use the AnnouncementModel to fetch announcements
            $announcementModel = new AnnouncementModel();
            $announcements = $announcementModel->getAllAnnouncements();

            // Prepare data for view
            $data = [
                'announcements' => $announcements,
                'user_name' => $session->get('user_name'),
                'user_email' => $session->get('user_email'),
                'role' => $session->get('role')
            ];

            return view('announcements', $data);

        } catch (\Throwable $e) {
            // Log the error
            log_message('error', 'Error fetching announcements: ' . $e->getMessage());

            // Return view with error message
            $data = [
                'announcements' => [],
                'error' => 'Unable to load announcements at this time.',
                'user_name' => $session->get('user_name'),
                'user_email' => $session->get('user_email'),
                'role' => $session->get('role')
            ];

            return view('announcements', $data);
        }
    }
}
