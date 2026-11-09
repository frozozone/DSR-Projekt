<?php
//povezava na bazo preko PDO + zagon seje
//to datoteko vkljucimo na vrhu vsake strani

session_start(); //seja rabimo za prijavo uporabnika

//podatki za bazo (isti kot v docker-compose.yml)
//host je "db" ker je to ime servisa v dockerju, ne localhost!
$host   = 'db';
$port   = '5432';
$baza   = 'dsr_baza';
$user   = 'admin';
$geslo  = 'geslo123';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$baza", $user, $geslo);

    //ce pride do napake v SQL naj vrze exception (da vidimo kaj je narobe)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //rezultate vraca kot asociativno tabelo, npr. $vrstica['naziv']
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Napaka pri povezavi na bazo: ' . $e->getMessage());
}

require_once __DIR__ . '/funkcije.php';
