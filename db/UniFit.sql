USE ffurno;

DROP TABLE IF EXISTS ESchede;
DROP TABLE IF EXISTS Schede;
DROP TABLE IF EXISTS Utenti;
DROP TABLE IF EXISTS Esercizi;
CREATE TABLE Utenti (
    username VARCHAR(20) NOT NULL UNIQUE,
    nome VARCHAR(63) NOT NULL,
    cognome VARCHAR(63) NOT NULL,
    email VARCHAR(63) NOT NULL UNIQUE,
    istruttore BOOLEAN DEFAULT FALSE NOT NULL,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (username)
);
CREATE TABLE Esercizi (
    nomeesercizio VARCHAR(60) NOT NULL UNIQUE,
    immagine VARCHAR(60) NOT NULL UNIQUE,
    PRIMARY KEY (nomeesercizio)
);
CREATE TABLE Schede (
    ids INT AUTO_INCREMENT NOT NULL UNIQUE,
    atleta VARCHAR(20) NOT NULL,
    istruttore VARCHAR(20) NOT NULL,
    inizio DATE NOT NULL,
    fine DATE NOT NULL,
    PRIMARY KEY (ids),
    FOREIGN KEY (atleta) REFERENCES Utenti(username) ON DELETE CASCADE,
    FOREIGN KEY (istruttore) REFERENCES Utenti(username) ON DELETE CASCADE,
    CONSTRAINT date_overlap_check 
        CHECK (inizio < fine)
);

-- Esercizi - schede. Ogni scheda può avere più esercizi
CREATE TABLE ESchede (
    ide INT AUTO_INCREMENT NOT NULL UNIQUE,
    ids INT NOT NULL,
    esercizio VARCHAR(60) NOT NULL,
    ripetizioni VARCHAR(40) NOT NULL,
    recupero VARCHAR(40) NOT NULL,
    note TEXT,
    PRIMARY KEY (ide),
    FOREIGN KEY (ids) REFERENCES Schede(ids) ON DELETE CASCADE,
    FOREIGN KEY (esercizio) REFERENCES Esercizi(nomeesercizio)
);

