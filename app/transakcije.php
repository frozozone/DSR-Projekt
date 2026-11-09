<?php
//prikaz transakcij prijavljenega uporabnika
require_once 'includes/povezava.php';

//do te strani lahko pride samo prijavljen uporabnik
zahtevajPrijavo();

//SQL za transakcije ene vrste (Odhodek ali Prihodek)
//vrsto dobimo preko kategorije, ker je transakcija nima direktno
//WHERE id_uporabnik = ? poskrbi da vsak vidi SAMO svoje transakcije
$sql = "SELECT t.naziv, t.znesek, t.datum
        FROM transakcija t
        JOIN kategorija k ON t.id_kategorija = k.id_kategorija
        JOIN vrsta_transakcije v ON k.id_vrsta = v.id_vrsta
        WHERE t.id_uporabnik = ? AND v.naziv = ?
        ORDER BY t.datum DESC, t.id_transakcija DESC";
$stmt = $pdo->prepare($sql);

//najprej stroski (odhodki), najnovejsi najprej
$stmt->execute([$_SESSION['id_uporabnik'], 'Odhodek']);
$stroski = $stmt->fetchAll();

//isti stavek se enkrat, samo za prihodke
$stmt->execute([$_SESSION['id_uporabnik'], 'Prihodek']);
$prihodki = $stmt->fetchAll();

$naslov = 'Moje transakcije - Moji stroški';
require_once 'includes/glava.php';
?>

<h2 class="mb-3">Moje transakcije</h2>

<!-- STROSKI -->
<div class="card p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <h5 class="mb-0">Stroški</h5>
        <!-- iskanje po nazivu, filtrira se sproti v js/iskanje.js -->
        <input type="text" id="iskanje" class="form-control form-control-sm iskanje" placeholder="Išči po nazivu...">
    </div>

    <?php if (empty($stroski)): ?>
        <p class="text-muted mb-0">Nimaš še nobenega stroška.</p>
    <?php else: ?>
        <table class="table table-sm mb-0" id="tabela-stroski">
            <thead>
                <tr><th>Naziv</th><th class="text-end">Vrednost</th><th class="text-end">Datum</th></tr>
            </thead>
            <tbody>
            <?php foreach ($stroski as $s): ?>
                <tr>
                    <td><?= e($s['naziv']) ?></td>
                    <td class="text-end odhodek"><?= evri($s['znesek']) ?></td>
                    <td class="text-end"><?= date('d. m. Y', strtotime($s['datum'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <!-- prikaze se samo ko iskanje ne najde nic -->
        <p class="text-muted mb-0 mt-2 d-none" id="ni-zadetkov">Ni stroškov s tem nazivom.</p>
    <?php endif; ?>
</div>

<!-- PRIHODKI - manjsa tabela, malo drugacen izgled -->
<div class="row">
    <div class="col-md-7 col-lg-6">
        <div class="card p-3 tabela-prihodki">
            <h6 class="mb-2">Prihodki</h6>

            <?php if (empty($prihodki)): ?>
                <p class="text-muted small mb-0">Ni prihodkov.</p>
            <?php else: ?>
                <table class="table table-sm table-borderless small mb-0">
                    <thead>
                        <tr><th>Naziv</th><th class="text-end">Vrednost</th><th class="text-end">Datum</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($prihodki as $p): ?>
                        <tr>
                            <td><?= e($p['naziv']) ?></td>
                            <td class="text-end prihodek"><?= evri($p['znesek']) ?></td>
                            <td class="text-end"><?= date('d. m. Y', strtotime($p['datum'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="js/iskanje.js"></script>

<?php require_once 'includes/noga.php'; ?>
