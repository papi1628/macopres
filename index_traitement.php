<style>
    :root {
        --primary: #323E82;
        --bg: #ffffffff;
    }
    .page {
        height: 100%;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #F9FAFB;
        font-family: 'Inter', system-ui, sans-serif;
    }
  
    .alert {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 20px;
        background: white;
        height: 110px;
        width: 60%;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);

    }

    .alert span {
        font-size: 14px;
        color: var(--primary);
        font-weight: bold;
        
        text-align: center;
    }
    .step-back {
        border: 1px solid var(--primary);
        background: #f8f9fb;
        padding: 5px 20px 5px 20px;
        border-radius: 5px;
        color: var(--primary);
        font-weight: bold;
    }
    .step-back:hover {
        color: white;
        background: var(--primary);
    }

</style>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "connexion.php";
session_start();
if (isset($_POST["connecter"])) {
    $login = $_POST["login"];
    $password = $_POST["password"];

    $req = mysqli_prepare($con, "SELECT * FROM user u JOIN role r ON u.idRole = r.idRole  WHERE u.login = ?");
    mysqli_stmt_bind_param($req, "s", $login);
    mysqli_stmt_execute($req);
    $result = mysqli_stmt_get_result($req);
    $user = mysqli_fetch_assoc($result);
    if ((mysqli_num_rows($result) == 1) && (password_verify($password,$user["motDePasse"]))) {

        $_SESSION["id_user"] = $user["idUser"];
        if ($user["nomRole"] == "PDG") {
            sleep(2);
            header("location:dashboard_pdg.php");
            exit();
        }
        if ($user["nomRole"] == "Assistant(e)") {
            sleep(2);
            header("location:dashboard_assist.php");
            exit();
            
        }
        
    }else{
        sleep(2);
        header("location:index.php?log=0");
        exit();
    }
}