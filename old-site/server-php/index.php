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
    <title>MB H2O | Vandens Gręžiniai pigia kaina (pigiai) Lietuvoje.</title>
    <meta name="description" content="MB H2O teikia profesionalius vandens gręžinių įrengimo paslaugas pietų ir rytų Lietuvoje.
        Fiksuotos kainos, švarus vanduo namams ir verslui. Susisiekite!">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'header.php'; ?>

    <main id="home">
        <section class="hero">
            <div class="hero-title">Vandens Gręžiniai<br>Kokybiškai. Rimtai</div>
            <a href="tel:+37069859318" class="call-to-action">Klausk Antano +37069859318!</a>
        </section>

        <section class="sections" id="services">
            <?php
            include 'generate_sections.php';
            echo generateSections('sections.json', 'error.log');
            ?>
        </section>
    </main>

    <?php include 'footer.php'; ?>

    <script src="scripts.js?v=<?php echo time(); ?>"></script>
</body>
</html>