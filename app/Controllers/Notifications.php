<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    public function get()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Unauthorized']);
        }

        $notificationModel = new NotificationModel();
        $userId = session()->get('user_id');

        $unreadCount = $notificationModel->getUnreadCount($userId);
        $notifications = $notificationModel->getNotificationsForUser($userId);

        return $this->response->setJSON([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    public function mark_as_read($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $notificationModel = new NotificationModel();
        $result = $notificationModel->markAsRead($id);

        return $this->response->setJSON([
            'success' => $result,
            'message' => $result ? 'Notification marked as read' : 'Failed to mark as read'
        ]);
    }
}
