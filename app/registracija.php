<?php
//registracija novega uporabnika
require_once 'includes/povezava.php';

//ce je ze prijavljen nima kaj delat tukaj
if (jePrijavljen()) {
    header('Location: index.php');
    exit;
}

$napake = [];
//vrednosti hranimo zato da jih ob napaki ni treba se enkrat vpisovat
$podatki = ['uporabnisko_ime' => '', 'email' => '', 'ime' => '', 'priimek' => ''];

//obrazec je bil poslan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //trim odstrani presledke na zacetku in koncu
    foreach ($podatki as $polje => $x) {
        $podatki[$polje] = trim($_POST[$polje] ?? '');
    }
    $geslo  = $_POST['geslo'] ?? '';
    $geslo2 = $_POST['geslo2'] ?? '';

    //preverjanje vnosa
    if ($podatki['uporabnisko_ime'] == '' || $podatki['email'] == '' || $podatki['ime'] == '' || $podatki['priimek'] == '') {
        $napake[] = 'Izpolni vsa polja.';
    }
    if ($podatki['email'] != '' && !filter_var($podatki['email'], FILTER_VALIDATE_EMAIL)) {
        $napake[] = 'E-pošta ni pravilna.';
    }
    if (strlen($geslo) < 6) {
        $napake[] = 'Geslo mora imeti vsaj 6 znakov.';
    }
    if ($geslo !== $geslo2) {
        $napake[] = 'Gesli se ne ujemata.';
    }

    //preverimo ce uporabnisko ime ali email ze obstaja
    //pripravljen stavek (prepare + execute) - zascita pred SQL injection
    if (empty($napake)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM uporabnik WHERE uporabnisko_ime = ? OR email = ?");
        $stmt->execute([$podatki['uporabnisko_ime'], $podatki['email']]);
        if ($stmt->fetchColumn() > 0) {
            $napake[] = 'Uporabniško ime ali e-pošta je že zasedena.';
        }
    }

    //vse ok - shranimo uporabnika
    if (empty($napake)) {
        //password_hash naredi bcrypt hash, v bazo nikoli ne shranimo pravega gesla
        $hash = password_hash($geslo, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO uporabnik (uporabnisko_ime, email, geslo_hash, ime, priimek)
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$podatki['uporabnisko_ime'], $podatki['email'], $hash, $podatki['ime'], $podatki['priimek']]);

        //preusmerimo na prijavo z obvestilom
        header('Location: prijava.php?registriran=1');
        exit;
    }
}

$naslov = 'Registracija - Moji stroški';
require_once 'includes/glava.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card p-4">
            <h2 class="mb-3">Registracija</h2>

            <!-- izpis napak ce so -->
            <?php if (!empty($napake)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($napake as $n): ?>
                        <div><?= e($n) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="ime">Ime</label>
                        <input class="form-control" type="text" id="ime" name="ime" value="<?= e($podatki['ime']) ?>" required>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="priimek">Priimek</label>
                        <input class="form-control" type="text" id="priimek" name="priimek" value="<?= e($podatki['priimek']) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="uporabnisko_ime">Uporabniško ime</label>
                    <input class="form-control" type="text" id="uporabnisko_ime" name="uporabnisko_ime" maxlength="50" value="<?= e($podatki['uporabnisko_ime']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">E-pošta</label>
                    <input class="form-control" type="email" id="email" name="email" value="<?= e($podatki['email']) ?>" required>
                </div>
                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="geslo">Geslo</label>
                        <input class="form-control" type="password" id="geslo" name="geslo" minlength="6" required>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label" for="geslo2">Ponovi geslo</label>
                        <input class="form-control" type="password" id="geslo2" name="geslo2" minlength="6" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-zelen w-100">Registriraj se</button>
            </form>

            <p class="mt-3 mb-0 text-center small">Že imaš račun? <a href="prijava.php">Prijavi se</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/noga.php'; ?>
