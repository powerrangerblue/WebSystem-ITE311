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
    </style>
</head>
<body>

    <!-- Include Header -->
    <?= $this->include('templates/header') ?>

    <!-- Dynamic Content -->
    <div class="container mt-4">
        <?= $this->renderSection('content') ?>
    </div>

</body>
</html>
