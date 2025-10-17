<?= $this->extend('template') ?>

<?= $this->section('title') ?>Announcements - Student Portal<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Custom Styles -->
    <style>
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .announcement {
            background-color: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .announcement-title {
            color: #007bff;
            font-size: 1.5em;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .announcement-content {
            color: #333;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .announcement-date {
            color: #666;
            font-size: 0.9em;
            font-style: italic;
        }
        .no-announcements {
            text-align: center;
            color: #666;
            font-style: italic;
            background-color: white;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .user-info {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #495057;
        }
    </style>

    <div class="header">
        <h1>📢 Announcements</h1>
        <p>Stay updated with the latest news and announcements</p>
    </div>


    <?php if (isset($error) && $error): ?>
        <div class="error-message">
            <strong>Error:</strong> <?= esc($error) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($announcements)): ?>
        <div class="no-announcements">
            <h3>No announcements available</h3>
            <p>Check back later for updates!</p>
        </div>
    <?php else: ?>
        <?php foreach ($announcements as $announcement): ?>
            <div class="announcement">
                <div class="announcement-title">
                    <?= esc($announcement['title']) ?>
                </div>
                <div class="announcement-content">
                    <?= nl2br(esc($announcement['content'])) ?>
                </div>
                <div class="announcement-date">
                    Posted on: <?= date('F j, Y \a\t g:i A', strtotime($announcement['created_at'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?= $this->endSection() ?>
