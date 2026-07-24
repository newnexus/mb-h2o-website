<?php
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MB H2O - D.U.K</title>
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'header.php'; ?>

    <main id="faq">
        <section class="hero">
            <div class="hero-title">Dažniausiai Užduodami Klausimai<br>Apie Vandens Gręžinius</div>
            <div class="call-to-action">Klausk Antano +37069859318!</div>
        </section>

        <section class="faq-sections">
            <?php
            include 'generate_faq.php';
            echo generateFaq('faq.json', 'error.log');
            ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script src="scripts.js?v=<?php echo time(); ?>"></script>
</body>
</html>