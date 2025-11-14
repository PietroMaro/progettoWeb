<form action="/vendi" method="POST" enctype="multipart/form-data">

    <h1>Unisell</h1>

    <ul>
        <li>
            <label for="astaSwitch">
                Vuoi che il prodotto sia venduto con un'asta?
            </label>
            <input type="checkbox" role="switch" id="astaSwitch">
            <small>In questo caso il prezzo inserito sopra sarà considerato la base d'asta</small>
        </li>

        <li>
            <label for="nomeProdotto">Nome prodotto</label>
            <input type="text" id="nomeProdotto" placeholder="Inserisci il nome del prodotto">
        </li>

        <li>
            <label for="descrizioneProdotto">Descrizione prodotto</label>
            <textarea id="descrizioneProdotto" rows="3"
                placeholder="Inserisci una breve descrizione (max 50 parole)"></textarea>
        </li>

        <li>
            <label for="prezzoProdotto">Inserisci prezzo</label>
            <input type="number" id="prezzoProdotto" placeholder="Inserisci prezzo" min="0" step="0.01">
        </li>

        <li>
            <section>
                <label>Inserisci immagini</label>
                <div id="image-preview-wrapper">
                    <span></span><span></span><span></span><span></span>
                </div>
                <label for="fileUpload" id="fileUploadLabel">
                    Aggiungi immagini
                </label>
                <input type="file" id="fileUpload" multiple>
            </section>
        </li>

        <li>
            <label for="dataFine">Inserisci la data e ora di fine dell'asta</label>
            <input type="datetime-local" id="dataFine">
        </li>

        <li>

            <button type="button">Modifica</button>
            <button type="submit">Vendi</button>

        </li>
    </ul>

</form>