<?php
//odjava - pobrisemo sejo in gremo na domaco stran
session_start();

$_SESSION = [];     //izpraznimo podatke v seji
session_destroy();  //unicimo sejo

header('Location: index.php');
exit;
