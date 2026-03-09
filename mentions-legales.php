<?php
session_start();
?>
<?php include('components/header.php') ?>
    <style>
        body { background-color: #f8f9fa; }
        .legal-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-top: 30px;
            margin-bottom: 50px;
        }
        h2 { color: #198754; font-size: 1.5rem; margin-top: 25px; border-bottom: 2px solid #e6f4ea; padding-bottom: 10px; }
        p, li { color: #555; line-height: 1.6; }
    </style>
<body class="d-flex flex-column min-vh-100">

<?php include('components/nav.php') ?>
<?php include("components/mentions_legales.html"); ?>


<?php include("components/footer.html"); ?>

</body>