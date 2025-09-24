<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My CI Project</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f5f7fa;
            font-family: "Poppins", sans-serif;
        }
        .navbar {
            background: #e3f2fd; /* soft light blue */
            box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.2rem;
            color: #333 !important;
        }
        .nav-link {
            font-size: 1rem;
            color: #333 !important;
        }
        .nav-link.active {
            font-weight: bold;
            color: #1976d2 !important; /* highlight active link */
        }
        .container {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 3px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<?= $this->include('templates/header') ?>

<div class="container mt-4">
    <!-- Dynamic content will load here -->
    <?= $this->renderSection('content') ?>
</div>

</body>
</html>
