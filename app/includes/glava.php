<?php
//glava strani - vkljucimo jo na zacetku vsake strani
//pred tem mora bit nastavljen $naslov (naslov strani v zavihku)
if (!isset($naslov)) {
    $naslov = 'Moji stroški';
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($naslov) ?></title>
    <!-- Bootstrap iz CDN-ja (grid in odzivnost) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- nas css -->
    <link href="css/stil.css" rel="stylesheet">
</head>
<body>

<!-- navigacija, na telefonu se skrije pod gumb (navbar-toggler) -->
<nav class="navbar navbar-expand-md navbar-dark navbar-zelena">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">Moji stroški</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#meni">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="meni">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Domov</a></li>
                <?php if (jePrijavljen()): ?>
                    <li class="nav-item"><a class="nav-link" href="transakcije.php">Moje transakcije</a></li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <?php if (jePrijavljen()): ?>
                    <!-- prijavljen uporabnik vidi svoje ime in odjavo -->
                    <li class="nav-item"><span class="navbar-text me-3">Pozdravljen/a, <?= e($_SESSION['ime']) ?></span></li>
                    <li class="nav-item"><a class="nav-link" href="odjava.php">Odjava</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="prijava.php">Prijava</a></li>
                    <li class="nav-item"><a class="nav-link" href="registracija.php">Registracija</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- glavna vsebina strani, zapre se v noga.php -->
<main class="container my-4">
