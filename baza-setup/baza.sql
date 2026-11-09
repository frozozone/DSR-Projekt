--=============================================
--Aplikacija za spremljanje stroskov - baza (PostgresSQL)
--Ta skripta se pozene sama ko se prvic ustvari docker container
--=============================================

--pgcrypto rabimo samo zato da lahko testnim uporabnikom
--ze tukaj nastavimo bcrypt hash gesla (isto kot password_hash v PHP)
CREATE EXTENSION IF NOT EXISTS pgcrypto;

--najprej pobrisemo stare tabele ce obstajajo (vrstni red je pomemben zaradi tujih kljucev)
DROP TABLE IF EXISTS transakcija CASCADE;
DROP TABLE IF EXISTS kategorija CASCADE;
DROP TABLE IF EXISTS nacin_placila CASCADE;
DROP TABLE IF EXISTS vrsta_transakcije CASCADE;
DROP TABLE IF EXISTS uporabnik CASCADE;


--1. UPORABNIK - registrirani uporabniki
CREATE TABLE uporabnik(
    id_uporabnik SERIAL PRIMARY KEY,
    uporabnisko_ime VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(254) NOT NULL UNIQUE,
    geslo_hash VARCHAR(255) NOT NULL, --tukaj je samo hash, nikoli pravo geslo
    ime VARCHAR(80) NOT NULL,
    priimek VARCHAR(80) NOT NULL,
    ustvarjen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);


--2. VRSTA TRANSAKCIJE - sifrant (prihodek ali odhodek)
CREATE TABLE vrsta_transakcije(
    id_vrsta SERIAL PRIMARY KEY,
    naziv VARCHAR(30) NOT NULL UNIQUE
);


--3. NACIN PLACILA - sifrant (gotovina, kartica, ...)
CREATE TABLE nacin_placila(
    id_nacin_placila SERIAL PRIMARY KEY,
    naziv VARCHAR(80) NOT NULL UNIQUE
);


--4. KATEGORIJA - sifrant kategorij
--id_nadrejena kaze na drugo kategorijo v ISTI tabeli (rekurzivna relacija)
--ce je NULL je to glavna kategorija, drugace je podkategorija
--vsaka kategorija pripada eni vrsti (npr. Hrana -> Odhodek, Placa -> Prihodek)
CREATE TABLE kategorija (
    id_kategorija SERIAL PRIMARY KEY,
    naziv VARCHAR(100) NOT NULL,
    id_vrsta INTEGER NOT NULL,
    id_nadrejena INTEGER NULL,
    FOREIGN KEY (id_vrsta) REFERENCES vrsta_transakcije(id_vrsta),
    FOREIGN KEY (id_nadrejena) REFERENCES kategorija(id_kategorija) ON DELETE RESTRICT, --ne pustimo brisat kategorije ki ima se podkategorije
    UNIQUE (naziv, id_nadrejena) --dve podkategoriji z istim imenom pod isto nadrejeno nimata smisla
);


--5. TRANSAKCIJA - posamezni stroski / prihodki uporabnika
--vrste tukaj NI, ker jo dobimo preko kategorije (tako se podatek ne ponavlja)
CREATE TABLE transakcija(
    id_transakcija SERIAL PRIMARY KEY,
    id_uporabnik INTEGER NOT NULL,
    id_kategorija INTEGER NOT NULL,
    id_nacin_placila INTEGER NOT NULL,
    naziv VARCHAR(100) NOT NULL,--kratek naslov, npr. "Mercator"
    znesek NUMERIC(10, 2) NOT NULL CHECK (znesek > 0), --vedno pozitiven
    datum DATE NOT NULL DEFAULT CURRENT_DATE,
    opis VARCHAR(500),--daljsi opis, ni obvezen
    ustvarjeno TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    --ce se uporabnik izbrise se izbrisejo tudi njegove transakcije
    FOREIGN KEY (id_uporabnik) REFERENCES uporabnik(id_uporabnik) ON DELETE CASCADE,
    FOREIGN KEY (id_kategorija) REFERENCES kategorija(id_kategorija),
    FOREIGN KEY (id_nacin_placila) REFERENCES nacin_placila(id_nacin_placila)
);

--indeks ker bomo skoraj vedno iskali transakcije enega uporabnika
CREATE INDEX idx_transakcija_uporabnik ON transakcija(id_uporabnik);


--========================================================
--TESTNI PODATKI
--========================================================

--vsi testni uporabniki imajo geslo: geslo123
--crypt + gen_salt('bf') naredi bcrypt hash ki ga PHP password_verify() razume
INSERT INTO uporabnik (uporabnisko_ime, email, geslo_hash, ime, priimek) VALUES
('ana',   'ana@example.com',   crypt('geslo123', gen_salt('bf', 10)), 'Ana',   'Novak'),
('marko', 'marko@example.com', crypt('geslo123', gen_salt('bf', 10)), 'Marko', 'Kovač'),
('nina',  'nina@example.com',  crypt('geslo123', gen_salt('bf', 10)), 'Nina',  'Horvat');


INSERT INTO vrsta_transakcije (naziv) VALUES
('Prihodek'),   --id 1
('Odhodek');    --id 2


INSERT INTO nacin_placila (naziv) VALUES
('Gotovina'),          --id 1
('Plačilna kartica'),  --id 2
('Bančno nakazilo');   --id 3


--glavne kategorije (id_nadrejena = NULL)
INSERT INTO kategorija (naziv, id_vrsta, id_nadrejena) VALUES
('Plača',      1, NULL),  --id 1
('Štipendija', 1, NULL),  --id 2
('Ostali prihodki', 1, NULL), --id 3
('Hrana',      2, NULL),  --id 4
('Prevoz',     2, NULL),  --id 5
('Stanovanje', 2, NULL),  --id 6
('Zabava',     2, NULL),  --id 7
('Zdravje',    2, NULL);  --id 8

--podkategorije (id_nadrejena kaze na glavno kategorijo zgoraj)
INSERT INTO kategorija (naziv, id_vrsta, id_nadrejena) VALUES
('Trgovina',      2, 4),  --id 9   Hrana > Trgovina
('Restavracije',  2, 4),  --id 10  Hrana > Restavracije
('Gorivo',        2, 5),  --id 11  Prevoz > Gorivo
('Javni prevoz',  2, 5),  --id 12  Prevoz > Javni prevoz
('Najemnina',     2, 6),  --id 13  Stanovanje > Najemnina
('Elektrika',     2, 6),  --id 14  Stanovanje > Elektrika
('Internet',      2, 6),  --id 15  Stanovanje > Internet
('Kino',          2, 7),  --id 16  Zabava > Kino
('Lekarna',       2, 8);  --id 17  Zdravje > Lekarna


--transakcije (vecina za ano, da je kaj videt v tabeli)
INSERT INTO transakcija (id_uporabnik, id_kategorija, id_nacin_placila, naziv, znesek, datum, opis) VALUES
--ana (id 1)
(1, 2,  3, 'Štipendija oktober', 420.00, '2026-10-01', 'Zoisova štipendija'),
(1, 1,  3, 'Študentsko delo',    380.50, '2026-10-05', 'Delo v skladišču'),
(1, 13, 3, 'Najemnina oktober',  250.00, '2026-10-03', 'Soba v Mariboru'),
(1, 9,  2, 'Mercator',            34.20, '2026-10-04', 'Tedenski nakup'),
(1, 10, 2, 'Pizzerija',           18.90, '2026-10-06', 'Večerja s prijatelji'),
(1, 12, 2, 'Mesečna vozovnica',   20.00, '2026-10-07', NULL),
(1, 9,  1, 'Lidl',                21.75, '2026-10-11', 'Sadje in zelenjava'),
(1, 16, 2, 'Kino Cineplexx',       8.50, '2026-10-12', 'Film ob sobotah'),
(1, 14, 3, 'Elektrika',           42.30, '2026-10-15', 'Račun za september'),
(1, 15, 3, 'Internet',            24.99, '2026-10-15', NULL),
(1, 17, 1, 'Lekarna',             11.40, '2026-10-18', 'Tablete za glavobol'),
(1, 9,  2, 'Spar',                27.60, '2026-10-19', NULL),
(1, 3,  1, 'Rojstni dan',         50.00, '2026-10-20', 'Darilo od babice'),
(1, 10, 1, 'Študentski bon',       3.20, '2026-10-22', 'Kosilo na boni'),
(1, 11, 2, 'Petrol',              45.00, '2026-10-25', 'Gorivo za vikend domov'),
(1, 13, 3, 'Najemnina november', 250.00, '2026-11-03', 'Soba v Mariboru'),
(1, 9,  2, 'Hofer',               19.85, '2026-11-04', NULL),
--marko (id 2)
(2, 1,  3, 'Plača',             1650.00, '2026-10-10', 'Plača za september'),
(2, 13, 3, 'Najemnina',          480.00, '2026-10-03', 'Stanovanje Ljubljana'),
(2, 11, 2, 'OMV',                 62.00, '2026-10-08', NULL),
(2, 9,  2, 'Mercator',            55.40, '2026-10-09', NULL),
--nina (id 3)
(3, 2,  3, 'Štipendija',         150.00, '2026-10-01', 'Kadrovska štipendija'),
(3, 7,  1, 'Koncert',             35.00, '2026-10-14', NULL);
