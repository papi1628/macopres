<?php
require "connexion.php";
if (isset($_POST["enregistrer"])) {
    //on récupère les données de l'employé.

    $prenom = $_POST["prenom"];
    $nom = $_POST["nom"];
    $naissance = $_POST["naissance"];
    $sexe = $_POST["sexe"];
    $tel = $_POST["tel"];
    $adresse = $_POST["adresse"];
    $departement = $_POST["departement"];
    $code = $_POST["code"];
    $embauche = $_POST["embauche"];

    
    

    //on vérifie si l'employé n'est pas déjà ajouté dans la base.
    $verif = mysqli_prepare($con, "SELECT * FROM `employe` WHERE (`prenom` = ? AND `nom` = ? AND `dateNaissance` = ? AND `sexe` = ? AND `tel` = ? AND `adresse` = ? AND `departement` = ? AND `code` = ? AND `dateEmbauche` = ?) OR `tel` = ?");
    mysqli_stmt_bind_param($verif, "ssssissssi", $prenom, $nom, $naissance, $sexe, $tel, $adresse, $departement, $code, $embauche, $tel);
    mysqli_stmt_execute($verif);
    $result = mysqli_stmt_get_result($verif);
    $employe = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) > 0) {
        $id = $employe["idEmploye"];
        if (isset($_GET["ajout"]) && $_GET["ajout"] == "user") {
            header("location:afficher_un_user.php?id=".$id."&success=3");
            exit();
        } else {
            header("location:afficher_un_employe.php?id=".$id."&success=3");
            exit();
        }
    }

    //on enregistre l'employé et ses données dans la base de données.

    $req = mysqli_prepare($con, "INSERT INTO employe (prenom, nom, dateNaissance, sexe, tel, adresse, departement, code, dateEmbauche) VALUES(?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($req, "ssssissss", $prenom, $nom, $naissance, $sexe, $tel, $adresse, $departement, $code, $embauche);
    mysqli_stmt_execute($req);
    $result_simple = mysqli_stmt_affected_rows($req);

    $id_last_emp = mysqli_insert_id($con);

    //pour tester si l'utilisateur a bien choisi une photo. UPLOAD_ERR_NO_FILE permet de vérifier si un fichier est charger. A noter qu'on le teste avec error.

    $titre_photo = "photo_defaut.jpeg";

    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {

            $photo = $_FILES["photo"];
            $tmp = $photo["tmp_name"];

            if ($photo["error"] != 0) {
                die ("oups, la photo n'est pas enregistrée. <br> <a href='ajout_empploye.php' >Réessayer</a>" );
            }

            $extension = mb_strtolower(pathinfo($photo["name"],PATHINFO_EXTENSION));
            $extension_image = ["jpg", "jpeg", "png"];
 
            if (!in_array($extension, $extension_image) ) {
                die ("oups, la photo n'est pas enregistrée. <br> <a href='ajout_employe.php' >Réessayer</a>" );
            }

            $titre_photo = "emp".$id_last_emp.".".$extension;
            $location = "/Applications/MAMP/htdocs/macopres/photo_profil/";
            move_uploaded_file($tmp, $location);
        
    }


    //on crée une matricule pour l'employé qui vient d'être enregistré
    
    if ($result_simple>0) {
        

        $matricule = "MCPRS25/26-".strtoupper($departement)."".$id_last_emp;

        $req_mat = mysqli_prepare($con, "UPDATE employe SET matricule = ?, photo = ? WHERE idEmploye = ?");
        mysqli_stmt_bind_param($req_mat, "ssi", $matricule, $titre_photo, $id_last_emp);
        mysqli_stmt_execute($req_mat);
        $result_mat = mysqli_stmt_affected_rows($req_mat);

        //on vérifie si l'employé travaille à l'administration, si c'est le cas on l'enregistre aussi comme utilisateur.
        if (($_POST["departement"]) == "ad"){
            $default_password = "m1708";
            $password_hash = password_hash($default_password,PASSWORD_DEFAULT);
            $login = $prenom;
            $id_role = 2;
            $req_user = mysqli_prepare($con, "INSERT INTO user (prenom, nom, tel, login, motDePasse, idRole) VALUES(?,?,?,?,?,?)");
            mysqli_stmt_bind_param($req_user, "ssissi", $prenom, $nom, $tel, $login, $password_hash, $id_role);
            mysqli_stmt_execute($req_user);
            $result_user = mysqli_stmt_affected_rows($req_user);
        }
    }
    
    if ($result_simple>0 && $result_mat>0 && $result_user>0 && isset($_GET["ajout"]) && $_GET["ajout"] == "user") {
        header("location:afficher_un_user.php?id=".$id_last_emp."&success=2");
        exit();

    } elseif ($result_simple>0 && $result_mat>0) {
        header("location:afficher_un_employe.php?id=".$id_last_emp."&success=1");
        exit();
    } else {
        header("location:afficher_un_employe.php?id=".$id_last_emp."&success=0");
        exit();
    }
}

// success=1 -> employé ajouté.
// success=2 -> employé ajouté comme employé et comme utilisateur aussi.
// succes=3 -> employé déjà ajouté.
// success=0 -> echec