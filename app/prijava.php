<?php
//prijava uporabnika
require_once 'includes/povezava.php';

if (jePrijavljen()) {
    header('Location: index.php');
    exit;
}

$napaka = '';
$uporabnisko_ime = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uporabnisko_ime = trim($_POST['uporabnisko_ime'] ?? '');
    $geslo = $_POST['geslo'] ?? '';

    //poiscemo uporabnika po uporabniskem imenu (pripravljen stavek)
    $stmt = $pdo->prepare("SELECT id_uporabnik, ime, geslo_hash FROM uporabnik WHERE uporabnisko_ime = ?");
    $stmt->execute([$uporabnisko_ime]);
    $uporabnik = $stmt->fetch();

    //password_verify primerja vpisano geslo s hashem iz baze
    if ($uporabnik && password_verify($geslo, $uporabnik['geslo_hash'])) {
        //nov id seje, da nekdo ne more "ukrast" stare seje (session fixation)
        session_regenerate_id(true);

        //v sejo shranimo kdo je prijavljen
        $_SESSION['id_uporabnik'] = $uporabnik['id_uporabnik'];
        $_SESSION['ime'] = $uporabnik['ime'];

        //po prijavi gremo direktno na transakcije
        header('Location: transakcije.php');
        exit;
    } else {
        //ne povemo ali je narobe ime ali geslo, da napadalec ne ugiba
        $napaka = 'Napačno uporabniško ime ali geslo.';
    }
}

$naslov = 'Prijava - Moji stroški';
require_once 'includes/glava.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card p-4">
            <h2 class="mb-3">Prijava</h2>

            <?php if (isset($_GET['registriran'])): ?>
                <div class="alert alert-success">Registracija uspešna, zdaj se lahko prijaviš.</div>
            <?php endif; ?>

            <?php if ($napaka): ?>
                <div class="alert alert-danger"><?= e($napaka) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label" for="uporabnisko_ime">Uporabniško ime</label>
                    <input class="form-control" type="text" id="uporabnisko_ime" name="uporabnisko_ime" value="<?= e($uporabnisko_ime) ?>" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="geslo">Geslo</label>
                    <input class="form-control" type="password" id="geslo" name="geslo" required>
                </div>
                <button type="submit" class="btn btn-zelen w-100">Prijava</button>
            </form>

            <p class="mt-3 mb-0 text-center small">Nimaš računa? <a href="registracija.php">Registriraj se</a></p>
        </div>
    </div>
</div>

<?php require_once 'includes/noga.php'; ?>
