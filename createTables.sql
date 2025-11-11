CREATE TABLE utente(
    idUtente INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(20) NOT NULL,
    cognome VARCHAR(20) NOT NULL,
    email VARCHAR(40) NOT NULL,
    username VARCHAR(20) NOT NULL,
    descrizione VARCHAR(50),
    PASSWORD VARCHAR(20) NOT NULL,
    idImmagine INT
); CREATE TABLE admin(
    idAdmin INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(20) NOT NULL,
    cognome VARCHAR(20) NOT NULL,
    email VARCHAR(40) NOT NULL,
    PASSWORD VARCHAR(20) NOT NULL
); CREATE TABLE segnalazione(
    idSegnalazione INT PRIMARY KEY AUTO_INCREMENT,
    testo VARCHAR(100) NOT NULL,
    stato ENUM('aperta', 'approvata', 'rifiutata') NOT NULL,
    idAdmin INT NOT NULL,
    FOREIGN KEY(idAdmin) REFERENCES admin(idAdmin) ON DELETE SET NULL
); CREATE TABLE prodotto(
    idProdotto INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(40) NOT NULL,
    descrizione VARCHAR(200) NOT NULL,
    prezzo FLOAT NOT NULL,
    stato ENUM(
        'attesa',
        'esposto',
        'asta',
        'venduto',
        'rifiutato'
    ) NOT NULL,
    ragioneRifiuto VARCHAR(200),
    fineAsta DATE,
    idUtente INT NOT NULL,
    idAdmin INT,
    FOREIGN KEY(idUtente) REFERENCES utente(idUtente) ON DELETE CASCADE,
    FOREIGN KEY(idAdmin) REFERENCES admin(idAdmin) ON DELETE SET NULL
); CREATE TABLE messaggio(
    idMessaggio INT PRIMARY KEY AUTO_INCREMENT,
    testo VARCHAR(500),
    progressivo INT NOT NULL
); CREATE TABLE immagini(
    idImmagine INT PRIMARY KEY AUTO_INCREMENT,
    immagine BLOB NOT NULL,
    idProdotto INT,
    idMessaggio INT,
    FOREIGN KEY(idMessaggio) REFERENCES messaggio(idMessaggio) ON DELETE CASCADE,
    FOREIGN KEY(idProdotto) REFERENCES prodotto(idProdotto) ON DELETE CASCADE
); CREATE TABLE chat(
    idChat INT PRIMARY KEY AUTO_INCREMENT,
    idUtente1 INT NOT NULL,
    idUtente2 INT NOT NULL,
    idProdotto INT NOT NULL,
    FOREIGN KEY(idUtente1) REFERENCES utente(idUtente) ON DELETE CASCADE,
    FOREIGN KEY(idUtente2) REFERENCES utente(idUtente) ON DELETE CASCADE,
    FOREIGN KEY(idProdotto) REFERENCES prodotto(idProdotto) ON DELETE CASCADE
); CREATE TABLE offertaAsta(
     idProdotto INT,
        idUtente INT,
        progressivo INT,
    PRIMARY KEY(
        idProdotto,
        idUtente,
        progressivo
    ),
    valore INT NOT NULL,
    FOREIGN KEY(idProdotto) REFERENCES prodotto(idProdotto) ON DELETE CASCADE,
    FOREIGN KEY(idUtente) REFERENCES utente(idUtente) ON DELETE CASCADE
); CREATE TABLE offertaChat(
    idOffertaChat INT PRIMARY KEY AUTO_INCREMENT,
    valore INT NOT NULL,
    progressivo INT NOT NULL,
    idChat INT NOT NULL,
    FOREIGN KEY(idChat) REFERENCES chat(idChat) ON DELETE CASCADE
); ALTER TABLE
    utente ADD CONSTRAINT idImmagine FOREIGN KEY(idImmagine) REFERENCES immagini(idImmagine) ON DELETE SET NULL;


CREATE table faq( idFaq INT PRIMARY KEY AUTO_INCREMENT, titolo VARCHar (100), descrizione varchar (500) ); 
ALTER TABLE segnalazione ADD tipoSegnalazione ENUM('discriminazione','volgaritá','molestia','truffa','spam','altro'); 