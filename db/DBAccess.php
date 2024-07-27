<?php
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    require_once "funzioni.php";
    class DBAccess { 

        private const HOST_DB = "localhost"; //localhost
        private const DATABASE_NAME = "ffurno"; 
        private const USERNAME = "ffurno"; 
        private const PASSWORD = "Uo6wushiekicai0N";

        private const ERRORE ="Errore in DBAccess.php";
        private $connection;

        /**
         * Apre la connessione al database
         * @return bool
         */
        public function openDBConnection(){
            try {
                $this->connection = new mysqli(self::HOST_DB, self::USERNAME, self::PASSWORD, self::DATABASE_NAME);
                $this->connection->set_charset("utf8mb4");
                $this->connection->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

            } catch(mysqli_sql_exception) {

                throw new Exception(self::ERRORE);
            }
            return true;
        }
        
        /**
         * Chiude la connessione al database
         */
        public function closeDBConnection(){
            if($this->connection) 
                $this->connection->close();
        }


        /**
         * Esegue una query in modo sicuro
         * @param string $query query con segnaposto ?
         * @param mixed $params parametro/array da inserire
         * @return mysqli_stmt statement della query eseguito
         */
        function executeQuery($query, &$params) {
            $stmt = $this->connection->prepare($query);
            pulisciInput($params);
            if(!is_array($params))
                $params = array($params);
            $types = "";
            foreach($params as $param) {
                if(is_int($param))
                    $types .= "i";
                elseif(is_float($param))
                    $types .= "d";
                else $types .= "s";
            }
            if($params)
                $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt;
        }

        /**
         * @param string $query query con segnaposto ?
         * @param mixed $params parametro/array da inserire
         * @return array array associativo con il risultato.
         */
        function selectQuery($query, &$params) {
            try {
                $stmt = $this->executeQuery($query, $params);
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $result->close();
                $stmt->close();
                return $data;
            } catch(mysqli_sql_exception $err) {
                throw new Exception(self::ERRORE);
            }
        }

        /**
         * @param string $query query con segnaposto ?
         * @param mixed $params parametro/array da inserire
         * @return int numero di righe aggiornate
         */
        function updateQuery($query, &$params) {
            try {
                $stmt = $this->executeQuery($query, $params);
                $affected_rows = $stmt->affected_rows;
                $stmt->close();
                return $affected_rows;
            } catch(mysqli_sql_exception $err) {
                throw new Exception(self::ERRORE);
            }
        }

        /**
         * @param string $query query con segnaposto ?
         * @param mixed $params parametro/array da inserire
         * @return int id ultima riga inserita
         */
        function insertQuery($query, &$params) {
            try {
                $stmt = $this->executeQuery($query, $params);
                $last_insert_id = $stmt->insert_id;
                $stmt->close();
                return $last_insert_id;
            } catch(mysqli_sql_exception $err) {
                throw new Exception(self::ERRORE);
            }
        }
        
        /**
         * Gestisce il login di un utente
         * @param string $username username dell'utente
         * @param string $password password dell'utente
         * @return array|bool risultato della query
         */
        public function login($username, $password) {
            $query =    "SELECT username, password, nome, cognome, email, istruttore
                        FROM Utenti
                        WHERE username = ?";
            
            $result =  $this->selectQuery($query, $username);
            $result = $this->selectQuery($query, $username);
            if (!$result) {
                return false;
            }
            if (!empty($result[0]['password']) && password_verify($password, $result[0]['password'])) {// Restituisci i dati dell'utente se la password è corretta
                return $result[0]; 
            } else { // Password non corretta
                return false; 
            }
        }
        /**
         * Inserisce un nuovo atleta nel database
         * @param string $username 
         * @param string $nome
         * @param string $cognome
         * @param string $email
         * @param string $password
         * @return int
         */
        public function signup($username, $nome, $cognome, $email, $password) {
            $query =    "INSERT INTO Utenti (username, nome, cognome, email, password) VALUES
                        (?, ?, ?, ?, ?);";
            $enc_pass = password_hash($password, PASSWORD_DEFAULT);
            $params = [$username, $nome, $cognome, $email, $enc_pass];
            return $this->insertQuery($query, $params);
        }

        /**
         * Seleziona tutti gli atleti iscritti
         * @return array risultato della query
         */
        public function getListaAtleti(){
            $query =    "SELECT username, nome, cognome, email, password 
                        FROM Utenti 
                        WHERE istruttore = FALSE";
            $params = [];
            return $this->selectQuery($query, $params);
        }

        /**
         * Seleziona un atleta tramite username
         * @param string $username username dell'atleta
         * @return array risultato della query
         */
        public function getAtletaByUsername($username) {
            $query =    "SELECT username, nome, cognome, email, password 
                        FROM Utenti 
                        WHERE istruttore = FALSE AND username = ?";

            return $this->selectQuery($query, $username)[0];
        }

        /**
         * Seleziona tutte le schede scadute di un atleta
         * @param string $username username dell'atleta
         * @return array risultato della query
         */
        public function getSchedeScaduteAtleta($username) {
            $query =    "SELECT DISTINCT ids, inizio, fine
                        FROM Utenti
                        JOIN Schede ON (Utenti.username = Schede.atleta)
                        WHERE (fine < CURDATE() OR inizio > CURDATE()) AND Utenti.username = ?

                        ORDER BY inizio DESC";
            return $this->selectQuery($query, $username);
        }

        /**
         * Inserisce l'intestazione di una scheda di allenamento
         * @param string $atleta username dell'atleta
         * @param string $istruttore username dell'istruttore
         * @param string $inizio data di inizio
         * @param string $fine data di fine
         * @return int id ultima riga inserita
         */
        public function createIntestazioneScheda($atleta, $istruttore, $inizio, $fine) {
            $query =    "INSERT INTO Schede (atleta, istruttore, inizio, fine) VALUES
                        (?, ?, ?, ?);";
            $params =[$atleta, $istruttore, $inizio, $fine];
            return $this->insertQuery($query, $params);
        }

        /**
         * Inserisce un esercizio in una scheda di allenamento
         * @param string $ids id della scheda
         * @param string $esercizio nome dell'esercizio
         * @param string $ripetizioni numero ripetizioni
         * @param string $recupero recupero tra le serie
         * @param string $note note sull'esercizio
         * @return int id ultima riga inserita
         */
        public function createEsercizioScheda($ids, $esercizio, $ripetizioni, $recupero, $note = null) {
            $query =    "INSERT INTO ESchede (ids, esercizio, ripetizioni, recupero, note) VALUES
                        (?, ?, ?, ?, ?)";
            $params = [$ids, $esercizio, $ripetizioni, $recupero, $note];
            return $this->insertQuery($query, $params);
        }
        /**
         * Seleziona le schede in corso di validità dell'atleta
         * @param string $username username dell'atleta
         * @return array risultato della query
         */
        public function getSchedeCorrentiAtleta($username) {
            $query =    "SELECT DISTINCT ids, inizio, fine
                        FROM Utenti
                        JOIN Schede ON (Utenti.username = Schede.atleta)
                        WHERE Utenti.username = ? AND CURDATE() >= inizio AND CURDATE() <= fine
                        ORDER BY inizio";
            return $this->selectQuery($query, $username);
        }

        /**
         * Seleziona l'username dell'atleta di una scheda
         * @param string $ids id della scheda
         * @return array risultato della query
         */
        public function getUsernameAtletaByScheda($ids) {
            $query =     "SELECT DISTINCT atleta
                        FROM Schede
                        WHERE ids = ?";

            return $this->selectQuery($query, $ids)[0]['atleta'];

        }
        
        /**
         * Seleziona l'intestazione di una scheda (Istruttore, inizio e fine)
         * @param string $ids id della scheda
         * @return array risultato della query
         */
        public function getIntestazioneScheda($ids) {
            $query =    "SELECT DISTINCT Utenti.nome, Utenti.cognome, inizio, fine 
                        FROM Schede
                        JOIN Utenti ON (Schede.istruttore = Utenti.username)
                        WHERE ids = ? AND Utenti.istruttore = TRUE";

            return $this->selectQuery($query, $ids)[0];

        }

        /**
         * Seleziona gli esercizi di una scheda
         * @param int $ids id della scheda
         * @return array risultato della query
         */
        public function getEserciziScheda($ids) {
            $query =    "SELECT DISTINCT ide, esercizio, immagine, ripetizioni, recupero, note
                        FROM Schede

                        JOIN ESchede ON (Schede.ids = ESchede.ids)

                        JOIN Esercizi ON (Esercizi.nomeesercizio = ESchede.esercizio)
                        WHERE Schede.ids = ?
                        ORDER BY ide ASC";
            return $this->selectQuery($query, $ids);
        }

        /**
         * Restituisce il numero degli esercizi presenti in una scheda
         * @param int $ids id della scheda
         * @return int
         */
        public function countEserciziScheda($ids): int{
            $query =    "SELECT COUNT(ide) AS 'numero'
                        FROM `ESchede`
                        WHERE ids = ?";
            return $this->selectQuery($query, $ids)[0]['numero'];
        }

        /**
         * Seleziona tutti gli esercizi presenti
         * @return array
         */
        public function getEsercizi() {
            $query =    "SELECT *
                        FROM Esercizi
                        ORDER BY nomeesercizio";
            $param = [];
            return $this->selectQuery($query, $param);
        }

        /**
         * Elimina un esercizio da una scheda
         * @param int $ide id esercizio
         * @param int $ids id scheda
         * @return int
         */
        public function deleteEsercizioFromScheda($ide, $ids) {
            $query =    "DELETE 
                        FROM ESchede
                        WHERE ESchede.ide = ? AND ESchede.ids = ?";
            $params = [$ide, $ids];
            return $this->updateQuery($query, $params);
        }
        /**
         * Rimuove una scheda e tutti gli esercizi collegati
         * @param int $ids id scheda
         * @return int 
         */
        public function deleteScheda($ids) {
            $query =   "DELETE 
                        FROM Schede 
                        WHERE ids = ?";
            return $this->updateQuery($query, $ids);
        }

        /**
         * Aggiorna la tipologia di un esercizio
         * @param int $ids id scheda
         * @param int $ide id esercizio da aggiornare
         * @param string $esercizio nome esercizio da inserire
         * @return int
         */
        public function updateTipologiaEsercizio($ids, $ide, $esercizio) {
            $query =    "UPDATE ESchede
                        SET esercizio = ?
                        WHERE ids = ? AND ide = ?";
            $params = [$esercizio, $ids, $ide];
            return $this->updateQuery($query, $params);
        }

        /**
         * Aggiorna le ripetizioni di un esercizio
         * @param int $ids id scheda
         * @param int $ide id esercizio da aggiornare
         * @param string $ripetizioni ripetizioni esercizio da inserire
         * @return int
         */
        public function updateRipetizioniEsercizio($ids, $ide, $ripetizioni) {
            $query =    "UPDATE ESchede
                        SET ripetizioni = ?
                        WHERE ids = ? AND ide = ?";
            $params = [$ripetizioni, $ids, $ide];
            return $this->updateQuery($query, $params);
        }

        /**
         * Aggiorna il recupero di un esercizio
         * @param int $ids id scheda
         * @param int $ide id esercizio da aggiornare
         * @param string $recupero recupero esercizio da inserire
         * @return int
         */
        public function updateRecuperoEsercizio($ids, $ide, $recupero) {
            $query =    "UPDATE ESchede
                        SET recupero = ?
                        WHERE ids = ? AND ide = ?";
            $params = [$recupero, $ids, $ide];
            return $this->updateQuery($query, $params);
        }

        /**
         * Aggiorna le note di un esercizio
         * @param int $ids id scheda
         * @param int $ide id esercizio da aggiornare
         * @param string $note note esercizio da inserire
         * @return int
         */
        public function updateNoteEsercizio($ids, $ide, $note) {
            $query =    "UPDATE ESchede
                        SET note = ?
                        WHERE ids = ? AND ide = ?";
            $params = [$note, $ids, $ide];
            return $this->updateQuery($query, $params);
        }

        /**
         * Aggiorna la data di inizio di una scheda
         * @param int $ids id scheda
         * @param string $inizio nuova data di inizio 
         * @return int
         */
        public function updateInizioScheda($ids, $inizio) {
            $query =    "UPDATE Schede
                        SET inizio = ?
                        WHERE ids = ?";
            $params = [$inizio, $ids];
            return $this->updateQuery($query, $params);
        }

        /**
         * Aggiorna la data di fine di una scheda
         * @param int $ids id scheda
         * @param string $inizio nuova data di fine 
         * @return int
         */
        public function updateFineScheda($ids, $inizio) {
            $query =    "UPDATE Schede
                        SET fine = ?
                        WHERE ids = ?";
            $params = [$inizio, $ids];
            return $this->updateQuery($query, $params);
        }

        /**
         * Elimina un atleta dal database con tutte le schede correlate
         * @param string $username username dell'atleta
         * @return int
         */
        public function deleteAtleta($username) {
            $query =    "DELETE
                        FROM Utenti
                        WHERE username = ? AND ISTRUTTORE = FALSE";
            return $this->updateQuery($query, $username);
        }


        /**
         * Aggiorna l'email di un utente
         * @param string $username utente
         * @param string $mail nuova email
         * @return int
         */
        public function updateMailUser($username, $email) {
            $query =    "UPDATE Utenti
                        SET email = ?
                        WHERE username = ?";
            $params = [$email, $username];
            return $this->updateQuery($query, $params);
        }

    }
?>