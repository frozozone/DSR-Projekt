//sprotno iskanje stroskov po nazivu (brez ponovnega nalaganja strani)

const iskanje = document.getElementById('iskanje');
const tabela = document.getElementById('tabela-stroski');
const niZadetkov = document.getElementById('ni-zadetkov');

//ce uporabnik nima stroskov tabele ni, zato preverimo da obstaja
if (iskanje && tabela) {
    //'input' se sprozi ob vsaki vpisani ali izbrisani crki
    iskanje.addEventListener('input', function () {
        //vse damo v male crke, da "MERC" najde tudi "Mercator"
        const iskano = iskanje.value.toLowerCase();
        const vrstice = tabela.querySelectorAll('tbody tr');
        let najdenih = 0;

        vrstice.forEach(function (vrstica) {
            //prvi stolpec je naziv
            const naziv = vrstica.cells[0].textContent.toLowerCase();

            //ce naziv vsebuje iskani niz vrstico pokazemo, drugace skrijemo
            if (naziv.includes(iskano)) {
                vrstica.style.display = '';
                najdenih++;
            } else {
                vrstica.style.display = 'none';
            }
        });

        //ce ni nobenega zadetka izpisemo obvestilo
        niZadetkov.classList.toggle('d-none', najdenih > 0);
    });
}
