<?php 
session_start();
if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit();
}
require "connexion.php";
$id_user = $_SESSION["id_user"];
$req = mysqli_prepare($con, "SELECT * FROM user WHERE idUser = ?");
mysqli_stmt_bind_param($req, "i", $id_user) ;
mysqli_stmt_execute($req);
$result = mysqli_stmt_get_result($req);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJOUTER EMPLOYÉ</title>
</head>
<body>
    <div>
        <form action="ajout_employe_traitement.php" method="post">
            <label for="prenom">Prenom</label>
            <br>
            <input type="text" name="prenom" placeholder="Prénom" required>
            <br>
            <br>
            <label for="nom">Nom</label>
            <br>
            <input type="text" name="nom" placeholder="Nom" required>
            <br>
            <br>
            <label for="naissance">Date de naissance</label>
            <br>
            <input type="date" name="naissance" placeholder="Date de naissance" required>
            <br>
            <br>
            <label for="sexe">Sexe</label>
            <br>
            <select name="sexe" required>
                Sexe
                <option value="">
                    --Quel sexe--
                </option>
                <option value="H">
                    Homme
                </option>
                <option value="F">
                    Femme
                </option>
            </select>
            <br>
            <br>
            <label for="tel">Téléphone</label>
            <br>
            <input type="tel" name="tel" pattern="[0-9]{9}" maxlength="9" placeholder="776543210" required>
            <br>
            <br>
            <label for="adresse">Adresse</label>
            <br>
            <input type="text" name="adresse">
            <br>
            <br>
            <select name="departement" required>
                Département
                <option value="">
                    --Chosir le département--
                </option>
                <option value="ad">
                    Administration
                </option>
                <option value="c">
                    Salle de coupe
                </option>
                <option value="m">
                    Salle de montage
                </option>
                <option value="f">
                    Finition
                </option>
            </select>
            <br>
            <br>
            <label for="code">Code</label>
            <br>
            <select name="code" >
                <option value="30">30</option>
                <option value="35">35</option>
                <option value="40">40</option>
                <option value="45">45</option>
                <option value="ap">apprentie</option>
                <option value="sa">salarié(e)</option>
            </select>
            <br>
            <br>
            <label for="embauche">Date d'embauche</label>
            <br>
            <input type="date" name="embauche" required>
            <br>
            <br>
            <input type="submit" placeholder="Enregistrer" name="enregistrer">
        </form>
    </div>
</body>
</html>
