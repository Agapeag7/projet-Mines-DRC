<?php
    interface DatabaseInterface {
        public function connect();
        public function disconnect();
    }

    abstract class BaseModel implements DatabaseInterface {
        protected $connection;

        public function connect() {
            $this->connection = new PDO('mysql:host=localhost;dbname=election_2025', 'root', '');
        }

        public function disconnect() {
            $this->connection = null;
        }
    }

    class Utilisateur extends BaseModel {
        public function __construct() {
            $this->connect();
        }

        public function verifierIdentite($nom_use, $md_passe) {
            $stmt = $this->connection->prepare('SELECT * FROM user_compte u LEFT JOIN blocage b ON u.ID_user = b.id_user WHERE b.id_user IS NULL AND u.nom_user = ?');
            $stmt->execute([$nom_use]);
            while($var_use = $stmt->fetch()){
                $md_passe = sha1($md_passe);
                if($md_passe == $var_use['code_user']){
                    session_start();
                    $_SESSION['id_'] = $var_use['ID_user'];
                    $_SESSION['nom_user'] = $var_use['nom_user'];
                    $_SESSION['pst_n'] = $var_use['post_n_user'];
                    $_SESSION['num_tel'] = $var_use['numero_tel_client'];
                    return true;
                }
                else{
                    return false;
                }
            }
        }

        public function voterCandidat($id_cndt, $id_usr){
            $stmt = $this->connection->prepare("SELECT 1 FROM votes V JOIN 
                candidats C ON V.id_candidats = C.id_candidat 
                WHERE V.id_user =? AND C.id_postes = (
                        SELECT id_postes FROM candidats WHERE id_candidat =?
                    )
                ");
            $stmt->execute([$id_usr, $id_cndt]);
            if ($stmt->fetch()) {
                echo ("<script>
                            alert('Tu as déjà voté pour ce poste.');
                        </script>"
                    );
                    header("Refresh: 2; URL=candidats.php");
            }
            else {
                $stmt = $this->connection->prepare('INSERT INTO votes (id_candidats, id_user) VALUES (?, ?)');
                $stmt->execute([$id_cndt, $id_usr]);
                echo("<script>
                    alert('succès');
                </script>");
            }

            $_SESSION['poste_index']++;
            header("Refresh: 2; URL=candidats.php");
        }

        public function messageConfirmer($id_u){

            $stmt = $this->connection->query("SELECT * FROM candidats c JOIN user_compte u ON c.id_user = u.ID_user JOIN postes p ON p.id_poste = c.id_postes");
            while($var_cndt = $stmt->fetch()){
                if($_SESSION['id_cand'] == $var_cndt['id_candidat']){
                    echo("
                        <div class='about'>
                            <div class='left'>
                                <img src='../images/".$var_cndt['photo_candidat']."'>
                            </div>
                            <div class='right'>
                                <h3>Numéro ".$var_cndt['num_candidat'].", ".$var_cndt['nom_user']." ".$var_cndt['post_n_user']."</h3>
                                <p>Confirmer votre choix Pour ".$var_cndt['nom_poste']."</p>
                                <p>ou Cliquez sur le bouton 'Retour'  !</p>
                                <button><a href='voter_pour.php?id_c=".$var_cndt['id_candidat']."'>Confirmer</a></button>
                                <button><a href='candidats.php'>Retour</a></button>
                            </div>
                        </div>
                    ");
                }
            }
        }
    }
?>