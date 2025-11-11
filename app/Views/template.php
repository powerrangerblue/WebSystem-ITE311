<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My CI Project</title>

    <!-- Bootstrap 5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    

    <!-- Custom Styles -->
    <style>
        body {
            background: #f0f4f9; /* LMS-friendly soft blue-gray */
            font-family: "Poppins", sans-serif;
        }
        .container {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.08);
        }
        .navbar {
            background: linear-gradient(90deg, #1a237e, #283593); /* LMS Blue gradient */
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.3rem;
            color: #fff !important;
        }
        .nav-link {
            font-size: 1rem;
            color: #fff !important;
            margin-right: 10px;
            transition: all 0.2s ease-in-out;
        }
        .nav-link:hover {
            color: #ffeb3b !important; /* yellow hover */
        }
        .nav-link.active {
            font-weight: bold;
            border-bottom: 2px solid #ffeb3b; /* active underline */
        }

        /* Notification dropdown styles */
        .notification-dropdown {
            width: 360px;
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background-color: #ffffff;
            padding: 0;
            border: none;
        }

        .notification-dropdown::-webkit-scrollbar {
            width: 6px;
        }

        .notification-dropdown::-webkit-scrollbar-thumb {
            background-color: #cccccc;
            border-radius: 4px;
        }

        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            font-size: 14px;
            color: #333;
            transition: background-color 0.2s ease;
            word-wrap: break-word;
            white-space: normal;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-time {
            display: block;
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }

        .mark-btn {
            float: right;
            font-size: 12px;
            color: #007bff;
            text-decoration: none;
            margin-left: 10px;
        }

        .mark-btn:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Include Header -->
    <?= $this->include('templates/header') ?>

    <!-- Dynamic Content -->
    <div class="container mt-4">
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Scripts Section -->
    <?= $this->renderSection('scripts') ?>

    <!-- Notification Scripts -->
    <?php if (session('isLoggedIn')): ?>
    <script>
    $(document).ready(function() {
        // Function to fetch and display notifications
        function fetchNotifications() {
            $.get('<?= base_url('notifications') ?>')
                .done(function(response) {
                    if (response.unread_count > 0) {
                        $('#notification-badge').text(response.unread_count).show();
                    } else {
                        $('#notification-badge').hide();
                    }

                    const list = $('#notifications-list');
                    list.empty();

                    if (response.notifications.length === 0) {
                        list.append('<li><a class="dropdown-item" href="#">No notifications</a></li>');
                        return;
                    }

                    response.notifications.forEach(function(notification) {
                        const item = `
                            <li class="notification-item" data-id="${notification.id}">
                                <div>${notification.message}</div>
                                <span class="notification-time">${new Date(notification.created_at).toLocaleString()}</span>
                                ${notification.is_read == 0 ? '<a href="#" class="mark-btn">Mark</a>' : ''}
                            </li>
                        `;
                        list.append(item);
                    });
                });
        }

        // Fetch notifications on page load
        fetchNotifications();

        // Handle mark as read button clicks
        $(document).on('click', '.mark-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const notificationId = $(this).closest('.notification-item').data('id');
            $.post('<?= base_url('notifications/mark_read') ?>/' + notificationId)
                .done(function(response) {
                    if (response.success) {
                        fetchNotifications(); // Refresh the notifications
                    }
                });
        });
    });
    </script>
    <?php endif; ?>

</body>
</html>
