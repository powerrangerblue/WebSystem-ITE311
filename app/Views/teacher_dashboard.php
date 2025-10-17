<?= $this->extend('template') ?>

<?= $this->section('title') ?>Teacher Dashboard - Student Portal<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Custom Styles -->
    <style>
        .welcome-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 200px);
            padding: 20px;
        }
        .welcome-box {
            background-color: #fff;
            color: #2c3e50;
            padding: 40px 60px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .welcome-text {
            font-size: 1.8em;
            margin: 0;
            font-weight: 300;
        }
    </style>

    <div class="welcome-container">
        <div class="welcome-box">
            <div class="welcome-text">Welcome, Teacher!</div>
        </div>
    </div>
<?= $this->endSection() ?>
