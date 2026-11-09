<?php
//domaca stran
require_once 'includes/povezava.php';

$naslov = 'Domov - Moji stroški';

//preberemo kategorije skupaj z nadrejeno kategorijo in vrsto
//LEFT JOIN na isto tabelo (kategorija n) zato da dobimo ime nadrejene kategorije
$sql = "SELECT k.naziv, n.naziv AS nadrejena, v.naziv AS vrsta
        FROM kategorija k
        LEFT JOIN kategorija n ON k.id_nadrejena = n.id_kategorija
        JOIN vrsta_transakcije v ON k.id_vrsta = v.id_vrsta
        ORDER BY v.naziv, COALESCE(n.naziv, k.naziv), k.id_nadrejena NULLS FIRST";
$kategorije = $pdo->query($sql)->fetchAll();

//se nacini placila
$nacini = $pdo->query("SELECT naziv FROM nacin_placila ORDER BY id_nacin_placila")->fetchAll();

require_once 'includes/glava.php';
?>

<!-- pozdravni del -->
<div class="p-4 mb-4 bg-white border rounded">
    <h1>Moji stroški</h1>
    <p class="lead">Preprosta aplikacija za spremljanje osebnih stroškov in prihodkov.
        Vpiši kam gre tvoj denar in imej pregled nad porabo.</p>

    <?php if (jePrijavljen()): ?>
        <a href="transakcije.php" class="btn btn-zelen">Pojdi na moje transakcije</a>
    <?php else: ?>
        <a href="registracija.php" class="btn btn-zelen">Registriraj se</a>
        <a href="prijava.php" class="btn btn-outline-zelen">Prijava</a>
    <?php endif; ?>
</div>

<!-- kratek opis, na telefonu so kartice ena pod drugo (col-md-4) -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100 p-3">
            <h5>Vpisuj stroške</h5>
            <p class="mb-0">Dodaj vsak strošek ali prihodek z zneskom, datumom in kategorijo.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-3">
            <h5>Kategorije</h5>
            <p class="mb-0">Stroški so razvrščeni v kategorije in podkategorije (npr. Hrana &gt; Trgovina).</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-3">
            <h5>Samo tvoji podatki</h5>
            <p class="mb-0">Vsak uporabnik vidi samo svoje transakcije.</p>
        </div>
    </div>
</div>

<!-- podatki iz baze - dokaz da PDO povezava dela -->
<div class="row g-3">
    <div class="col-md-8">
        <div class="card p-3">
            <h5>Kategorije</h5>
            <table class="table table-sm mb-0">
                <thead>
                    <tr><th>Kategorija</th><th>Nadrejena</th><th>Vrsta</th></tr>
                </thead>
                <tbody>
                <?php foreach ($kategorije as $k): ?>
                    <tr>
                        <td><?= e($k['naziv']) ?></td>
                        <td><?= $k['nadrejena'] ? e($k['nadrejena']) : '<span class="text-muted">-</span>' ?></td>
                        <td class="<?= $k['vrsta'] == 'Prihodek' ? 'prihodek' : 'odhodek' ?>"><?= e($k['vrsta']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <h5>Načini plačila</h5>
            <ul class="mb-0">
                <?php foreach ($nacini as $n): ?>
                    <li><?= e($n['naziv']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once 'includes/noga.php'; ?>
