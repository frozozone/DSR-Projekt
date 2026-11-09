<?php
//pomozne funkcije ki jih rabimo na vec straneh

//varen izpis besedila v HTML (zascita pred XSS)
//npr. ce nekdo v opis napise <script> se to izpise kot navaden tekst
function e($besedilo)
{
    return htmlspecialchars($besedilo ?? '', ENT_QUOTES, 'UTF-8');
}

//preveri ce je uporabnik prijavljen (v seji imamo njegov id)
function jePrijavljen()
{
    return isset($_SESSION['id_uporabnik']);
}

//to klicemo na straneh kjer mora bit uporabnik prijavljen
//ce ni prijavljen ga preusmerimo na prijavo
function zahtevajPrijavo()
{
    if (!jePrijavljen()) {
        header('Location: prijava.php');
        exit;
    }
}

//znesek lepo izpise v evrih, npr. 1234.5 -> 1.234,50 €
function evri($znesek)
{
    return number_format((float)$znesek, 2, ',', '.') . ' €';
}
