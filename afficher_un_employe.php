<?php 
session_start();
require "connexion.php";

if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION["id_user"];
$idEmploye = $_GET["id"];



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Macopres</title>
    <link rel="icon" type="image/png" href="logo.jpeg">
    <style>
        :root {
        --primary: #323E82;
        --primary_trans: #e6e8f3ff;
        --bg: #ffffffff;
        }
        
        body {
            margin: 0;
            background: var(--primary_trans);
            font-family: system-ui, -apple-system, sans-serif;
        }

        .page {
            display: flex;
            min-height: 100vh;
            background: var(--primary_trans);

        }
        
        .menu {
            width: 18%;
            min-width: 220px;
            max-width: 280px;
            background: var(--primary);
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        .company {
            display: flex;
            gap: 1rem;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .logo {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            overflow: hidden;
        }

        

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu-item-group {
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.3);
            padding-top: 1.5rem;
        }

        .menu-item,
        .menu-item-employes {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.7rem;
            color: white;
            text-decoration: none;
            font-size: 1rem;
            margin-bottom: 0.8rem;
            border-radius: 0.4rem;
        }

        .menu-item-employes {
            background: #7882bc;
        }

        .menu-item:hover {
            background: #7882bc;
        }

        .main {
            flex: 1;
            padding: 1.5rem;
        }


        .top {
            background: var(--primary);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            border-radius: 0.5rem;
        }

        .top {
            background: var(--primary);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            border-radius: 0.5rem;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user h2 {
            color: white;
        }

        .user-pp {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            overflow: hidden;
        }

        .user-pp img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .deconnexion a {
            background: white;
            color: var(--primary);
            border: 1px solid white;
            border-radius: 0.4rem;
            padding: 0.45rem 0.9rem;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .deconnexion a:hover {
            background: transparent;
            color: white;
            border: 1px solid white;
        }
        

        .middle {
            margin-top: 2rem;

        }


        .superposition {
            position: relative;
            width: 100%;
            margin-top: 6.3rem;
        }

        .fiche_employe {
            position: fixed;
            z-index: 2000;
            top: 26%;
            right: 5%;
            width: 45%;
            min-width: 320px;
            max-width: 600px;
            height: 68vh;
            background: white;
            padding: 1rem;
            border-radius: 0.6rem;
            border: 1px solid var(--primary);
            box-shadow: 0 0 3rem rgba(0,0,0,0.15);
            overflow-y: auto;
        }

        .liste {
        }

        table{
            width:100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 700px;
        }

        th, td {
            padding: 0.8rem;
            border-bottom: 1px solid var(--primary);
            text-align: center;
        }

        th {
            background: var(--primary);
            color: white;
        }

        tr:hover td {
            background: var(--primary_trans);
        }

        .actif {
            background: green;
            color: white;
            padding: 0.3rem 0.3rem;
            border-radius: 0.3rem;
        }

        .bloque {
            background: red;
            color: white;
            padding: 0.3rem 0.3rem;
            border-radius: 0.3rem;
        }

        .update, .delete {
            padding: 0.4rem;
            border-radius: 0.3rem;
            color: white;
            text-decoration: none;
        }

        .update { 
            background: var(--primary); 
        }
        .delete { 
            background: red; 
        }

        .employe_photo {
            width: 1.55rem;
            height: 1.55rem;
            overflow: hidden;
            border-radius: 50%;
        }

        .employe_photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .employe {
            display: flex;
            gap: 3rem;
            align-items: center;
            margin-bottom: 1.875rem;
        }

        .image_employe {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            align-items: center;
        }

    .photo_employe {
        width: 9rem;
        height: 9rem;
        border: 1px solid var(--primary);
        border-radius: 50%;
        overflow: hidden;
    }

    .photo_employe img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .matricule_employe {
        border-radius: 0.4rem;
        color: white;
        font-weight: bold;
        padding: 0.4rem 0.8rem;
        background: var(--primary);
        text-align: center;
        font-size: 0.9rem;
    }


    .info_employe {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.8rem;
        padding-left: 2rem;
        border-left: 0.15rem solid lightgray;
    }

    .info_employe div {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        flex-wrap: wrap;
        width: 100%;
    }

    .info_employe svg {
        color: var(--primary);
    }

    

    .modifier {
        background: var(--primary);
        border-radius: 0.4rem;
        color: white;
        padding: 0.5rem;
        text-align: center;
        text-decoration: none;
        font-size: 0.9rem;
        margin-top: 1rem;
        transition: 0.3s;
    }

    .modifier:hover {
        border: 1px solid var(--primary);
        background: white;
        color: var(--primary);
    }


    .cancel {
        color: var(--primary);
        position: absolute;
        top: 1rem;
        left: 1rem;
        cursor: pointer;
        text-decoration: none;
    }


    .dansLaBase,
    .ajoutReussi,
    .ajoutUserReussi {
        position: fixed;
        z-index: 3000;
        top: 26%;
        left: 35%;
        transform: translateX(-50%);
        width: 10%;
        min-width: 200px;
        max-width: 500px;
        padding: 1.2rem;
        text-align: center;
        border-radius: 0.6rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        animation: hide 8s forwards;
        box-shadow: 0 0 2rem rgba(0,0,0,0.2);
        animation: hide 5s forwards;
    }

    @keyframes hide {
        0% { opacity: 1; }
        90% { opacity: 1; }
        100% { opacity: 0; }
    }

    .dansLaBase {
        background-color: #ed7a7a;
    }

    .ajoutReussi,
    .ajoutUserReussi {
        background-color: lightgreen;
    }

    .dansLaBase span,
    .ajoutReussi span,
    .ajoutUserReussi span {
        font-size: 1rem;
    }

    .cancel_erreur,
    .cancel_ajout,
    .cancel_ajout_user {
        color: black;
        cursor: pointer;
        text-decoration: none;
    }


    table {
        width: 100%;
    }

    th, td {
        padding: 0.8rem;
    }


    @media (max-width: 1200px) {
        .fiche_employe {
            width: 50%;
        }
    }

    @media (max-width: 992px) {
        .fiche_employe {
            width: 80%;
            right: 10%;
        }

        .info_employe {
            padding-left: 1rem;
        }
    }

    @media (max-width: 768px) {

        .fiche_employe {
            width: 95%;
            right: 2.5%;
            top: 3%;
            height: 90vh;
        }

        .employe {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 1.5rem;
        }

        .info_employe {
            border-left: none;
            padding-left: 0;
            width: 100%;
        }

        .photo_employe {
            width: 7rem;
            height: 7rem;
        }

        .dansLaBase,
        .ajoutReussi,
        .ajoutUserReussi {
            width: 90%;
        }
    }
        
    </style>
</head>
<body>
    <div class="page">
        <div class="menu">
            <div class="company">
                <div class="logo">
                    <img src="logo.jpeg" alt="macopres">
                </div>
                <div class="slogan">
                    <h3>
                        Élargir<br>sans limite.
                    </h3>
                </div>
            </div>
            <div class="menu-item-group">
                
                <a class="menu-item" href="dashboard_pdg.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-building-fill" viewBox="0 0 16 16">
                        <path d="M3 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h3v-3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V16h3a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm1 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"/>
                    </svg>
                    <div>Tableau de Bord</div>  
                </a>
                <a href="gestion_utilisateurs.php" class="menu-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                    </svg>
                    <div>Gestion des utilisateurs</div>
                </a>
                <a class="menu-item" href="gestion_role.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-person-badge" viewBox="0 0 16 16">
                        <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                        <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z"/>
                    </svg>
                    <div>Gestion des rôles</div>
                </a>
                <a class="menu-item-employes" href="gestion_employe.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
                        <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5m1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0M1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5"/>
                    </svg>
                    <div>Gestion des employés</div>
                </a>

                <a class="menu-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                    </svg>
                    <div>Gestion des pointages</div>
                </a>
            </div>
        </div>
        <?php
        $req = mysqli_prepare($con, "SELECT * FROM user WHERE idUser = ? ");
        mysqli_stmt_bind_param($req, "i", $id_user);
        mysqli_stmt_execute($req);
        $result = mysqli_stmt_get_result($req);
        $user = mysqli_fetch_assoc($result);
        ?>
        <div class="main">
            <div class="top">
                <div class="user">
                    <div class="user-pp">
                        <img src="photo_profil/<?php echo $user["photoDeProfil"] ?>" alt="">
                    </div>
                    <h2>
                        Bienvenue, M. <?php echo strtoupper($user["nom"]) ?>
                    </h2>
                </div>
                <div class="deconnexion">
                    <a href="deconnexion.php" name="deconnecter"> Se déconnecter</a>
                </div>
            </div>
            <div class="middle">
                

                <?php
                $req1 = mysqli_prepare($con, "SELECT * FROM employe");
                mysqli_stmt_execute($req1);
                $result1 = mysqli_stmt_get_result($req1);
                ?>
                <div class="superposition">
                    <div class="liste">
                        <table>
                            <tr>
                                <th style="text-align: left; padding-left: 100px;">Employé</th>
                                <th>Téléphone</th>
                                <th>Matricule</th>
                                <th>Code</th>
                                <th>Actions</th>
                            </tr>

                            <?php
                            while ($user1 = mysqli_fetch_assoc($result1)) {
                                if ($user1["idEmploye"] == $idEmploye) {
                                echo "<tr class='employe_choisi'>";
                                echo "<td><a style='font-size: 16px; display: flex; align-items: center; gap: 50px; text-decoration: none; color: black; 'href='afficher_un_employe.php?id=".$user1["idEmploye"]."'><div class='employe_photo'><img src='photo_profil/".$user1["photo"]."'></div><strong>".mb_convert_case($user1['prenom'],"2")." ".mb_strtoupper($user1['nom'])."</strong></a></td>";
                                echo "<td><a style='font-size: 16px; display: flex; align-items: center; gap: 50px; text-decoration: none; color: black; justify-content: center;' href='afficher_un_employe.php?id=".$user1["idEmploye"]."'><strong>".$user1['tel']."</strong></a></td>";
                                echo "<td><a style='font-size: 16px; display: flex; align-items: center; gap: 50px; text-decoration: none; color: black; justify-content: center;' href='afficher_un_employe.php?id=".$user1["idEmploye"]."'><span style='border: 1px solid var(--primary); border-radius: 5px; color: var(--primary); font-weight: bold; padding: 5px;'>".$user1['matricule']."</span></a></td>";
                                
                                if ($user1["code"] == "sa") {
                                    $user1["code"] = "Salarié(e)";
                                }  elseif ($user1["code"] == "ap") {
                                    $user1["code"] = "Apprenti(e)";
                                }
                                echo "<td><a style='font-size: 16px; display: flex; align-items: center; gap: 50px; text-decoration: none; color: black; justify-content: center;' href='afficher_un_employe.php?id=".$user1["idEmploye"]."'><strong>".$user1['code']."</strong></a></td>";
                                
                                echo "<td>
                                        <a class='update' href=''><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-three-dots' viewBox='0 0 16 16'>
    <path d='M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3'/>
    </svg></a> /
                                        <a class='delete' href=''><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
    <path d='M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5'/>
    </svg></a>
                                    </td>";
                                echo "</tr>";
                                }else{
                                echo "<tr>";
                                echo "<td><a style='font-size: 16px; display: flex; align-items: center; gap: 50px; text-decoration: none; color: black; 'href='afficher_un_employe.php?id=".$user1["idEmploye"]."'><div class='employe_photo'><img src='photo_profil/".$user1["photo"]."'></div><strong>".mb_convert_case($user1['prenom'],"2")." ".mb_strtoupper($user1['nom'])."</strong></a></td>";
                                echo "<td><a style='font-size: 16px; display: flex; align-items: center; gap: 50px; text-decoration: none; color: black; justify-content: center;' href='afficher_un_employe.php?id=".$user1["idEmploye"]."'><strong>".$user1['tel']."</strong></a></td>";
                                echo "<td><a style='font-size: 16px; display: flex; align-items: center; gap: 50px; text-decoration: none; color: black; justify-content: center;' href='afficher_un_employe.php?id=".$user1["idEmploye"]."'><span style='border: 1px solid var(--primary); border-radius: 5px; color: var(--primary); font-weight: bold; padding: 5px;'>".$user1['matricule']."</span></a></td>";
                                
                                if ($user1["code"] == "sa") {
                                    $user1["code"] = "Salarié(e)";
                                }  elseif ($user1["code"] == "ap") {
                                    $user1["code"] = "Apprenti(e)";
                                }
                                echo "<td><a style='font-size: 16px; display: flex; align-items: center; gap: 50px; text-decoration: none; color: black; justify-content: center;' href='afficher_un_employe.php?id=".$user1["idEmploye"]."'><strong>".$user1['code']."</strong></a></td>";
                                
                                echo "<td>
                                        <a class='update' href=''><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-three-dots' viewBox='0 0 16 16'>
    <path d='M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3'/>
    </svg></a> /
                                        <a class='delete' href=''><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3' viewBox='0 0 16 16'>
    <path d='M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5'/>
    </svg></a>
                                    </td>";
                                echo "</tr>";
                            }
                            }
                            ?>
                        </table>

                    </div>
                    <?php 
                    $fiche = mysqli_prepare($con, "SELECT * FROM employe WHERE idEmploye = ? ");
                    mysqli_stmt_bind_param($fiche, "i", $idEmploye);
                    mysqli_stmt_execute($fiche);
                    $result_fiche = mysqli_stmt_get_result($fiche);
                    $fiche_employe = mysqli_fetch_assoc($result_fiche);
                    
                    ?>
                    <div class="fiche_employe">
                        <a href="gestion_employe.php" class="cancel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                            </svg>
                        </a>
                        <div class="employe">
                            <div class="image_employe">
                                <div class="photo_employe">
                                    <img src="photo_profil/<?php echo $fiche_employe["photo"] ?>" alt="">
                                </div>
                                <div class="matricule_employe">
                                    <?php echo $fiche_employe["matricule"] ?>
                                </div>
                            </div>
                            <div class="info_employe">
                                <div class="Prenom_nom">
                                    <h1 class="prenom"><?php echo mb_convert_case($fiche_employe["prenom"], 2)." ".mb_strtoupper($fiche_employe["nom"]) ?> </h1>
                                </div>
                                <div class="naissance" style="border-bottom: 1px solid var(--primary); padding-bottom:10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar2-plus-fill" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 3.5v1c0 .276.244.5.545.5h10.91c.3 0 .545-.224.545-.5v-1c0-.276-.244-.5-.546-.5H2.545c-.3 0-.545.224-.545.5m6.5 5a.5.5 0 0 0-1 0V10H6a.5.5 0 0 0 0 1h1.5v1.5a.5.5 0 0 0 1 0V11H10a.5.5 0 0 0 0-1H8.5z"/>
                                    </svg>
                                    <span>Date de naissance: </span> <strong><?php echo date("d-m-Y",strtotime($fiche_employe["dateNaissance"])) ?></strong>
                                </div>
                                <div class="sexe" style="border-bottom: 1px solid var(--primary); padding-bottom:10px;">
                                    <?php
                                    if ($fiche_employe["sexe"] == "F") {
                                        $fiche_employe["sexe"] = "Femme";
                                    }else{
                                        $fiche_employe["sexe"] = "Homme";
                                    }

                                    ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-gender-ambiguous" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M11.5 1a.5.5 0 0 1 0-1h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-3.45 3.45A4 4 0 0 1 8.5 10.97V13H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V14H6a.5.5 0 0 1 0-1h1.5v-2.03a4 4 0 1 1 3.471-6.648L14.293 1zm-.997 4.346a3 3 0 1 0-5.006 3.309 3 3 0 0 0 5.006-3.31z"/>
                                    </svg>
                                    <span>Sexe: </span> <strong><?php echo $fiche_employe["sexe"]  ?></strong>
                                </div>
                                <div class="tel" style="border-bottom: 1px solid var(--primary); padding-bottom:10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                                    </svg>
                                   <span>Téléphone: </span> <strong><?php echo $fiche_employe["tel"]  ?></strong>
                                </div>
                                <div class="adresse" style="border-bottom: 1px solid var(--primary); padding-bottom:10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                    </svg>
                                    <span>Adresse: </span> <strong><?php echo mb_convert_case($fiche_employe["adresse"],0)  ?></strong>
                                </div>
                                <div class="departement" style="border-bottom: 1px solid var(--primary); padding-bottom:10px;">
                                    <?php 
                                    if ($fiche_employe["departement"] == "ad") {
                                        $fiche_employe["departement"] = "Administration";
                                    }  elseif ($fiche_employe["departement"] == "c") {
                                        $fiche_employe["departement"] = "Salle de coupe";
                                    } elseif ($fiche_employe["departement"] == "m") {
                                        $fiche_employe["departement"] = "Salle de montage";
                                    } elseif ($fiche_employe["departement"] == "f") {
                                        $fiche_employe["departement"] = "Salle de finition";
                                    }    
                                    
                                    ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-building-fill" viewBox="0 0 16 16">
                                    <path d="M3 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h3v-3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V16h3a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm1 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"/>
                                    </svg>
                                    <span>Département: </span> <strong><?php echo $fiche_employe["departement"]  ?></strong>
                                </div>
                                <div class="code" style="border-bottom: 1px solid var(--primary); padding-bottom:10px;">
                                    <?php 
                                    if ($fiche_employe["code"] == "sa") {
                                        $fiche_employe["code"] = "Salarié(e)";
                                    }  elseif ($fiche_employe["code"] == "ap") {
                                        $fiche_employe["code"] = "Apprenti(e)";
                                    }

                                    ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
                                    <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                                    <path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2z"/>
                                    </svg>
                                    <span>Code: </span> <strong><?php echo $fiche_employe["code"]  ?></strong>
                                </div>
                                <div class="embauche">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar2-plus-fill" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 3.5v1c0 .276.244.5.545.5h10.91c.3 0 .545-.224.545-.5v-1c0-.276-.244-.5-.546-.5H2.545c-.3 0-.545.224-.545.5m6.5 5a.5.5 0 0 0-1 0V10H6a.5.5 0 0 0 0 1h1.5v1.5a.5.5 0 0 0 1 0V11H10a.5.5 0 0 0 0-1H8.5z"/>
                                    </svg>
                                    <span>Date d'embauche: </span> <strong><?php echo date("d-m-Y",strtotime($fiche_employe["dateEmbauche"])) ?></strong>
                                </div>
                                <a href="modifier_employer.php" class="modifier"> Modifier </a>
                            </div>
                        </div>
                        
                        

                    </div>
                    <?php 
                    if (isset($_GET["success"])) {
                        $success = $_GET["success"];
                        if ($success == 3) { 
                            $erreur = mysqli_prepare($con, "SELECT * FROM employe WHERE idEmploye = ? ");
                            mysqli_stmt_bind_param($erreur, "i", $idEmploye);
                            mysqli_stmt_execute($erreur);
                            $result_erreur = mysqli_stmt_get_result($erreur);
                            $message_erreur = mysqli_fetch_assoc($result_erreur);
                            echo "<div class='dansLaBase'>";
                                    echo "<a href='afficher_un_employe.php?id=".$message_erreur["idEmploye"]."' class='cancel_erreur'>";
                                        echo "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-x-circle-fill' viewBox='0 0 16 16'><path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z'/></svg>";
                                    echo "</a>";
                                    echo "<span>";
                                        echo "<strong>".mb_convert_case($message_erreur["prenom"], 2)." ".mb_strtoupper($message_erreur["nom"])." </strong> est déja dans la base. ";
   
                                echo "</span>";
                            echo "</div>";
                        } elseif ($success == 1) {
                            $ajout = mysqli_prepare($con, "SELECT * FROM employe WHERE idEmploye = ? ");
                            mysqli_stmt_bind_param($ajout, "i", $idEmploye);
                            mysqli_stmt_execute($ajout);
                            $result_ajout = mysqli_stmt_get_result($ajout);
                            $message_ajout = mysqli_fetch_assoc($result_ajout);
                            echo "<div class='ajoutReussi'>";
                                    echo "<a href='afficher_un_employe.php?id=".$message_ajout["idEmploye"]."' class='cancel_ajout'>";
                                        echo "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-x-circle-fill' viewBox='0 0 16 16'><path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z'/></svg>";
                                    echo "</a>";
                                    echo "<span>";
                                        echo "<strong>".mb_convert_case($message_ajout["prenom"], 2)." ".mb_strtoupper($message_ajout["nom"])." </strong> est bien ajouté dans la base. ";
   
                                echo "</span>";
                            echo "</div>";
                        } elseif ($success == 2) {
                            $ajout_user = mysqli_prepare($con, "SELECT * FROM employe WHERE idEmploye = ? ");
                            mysqli_stmt_bind_param($ajout_user, "i", $idEmploye);
                            mysqli_stmt_execute($ajout_user);
                            $result_ajout_user = mysqli_stmt_get_result($ajout_user);
                            $message_ajout_user = mysqli_fetch_assoc($result_ajout_user);
                            echo "<div class='ajoutUserReussi'>";
                                    echo "<a href='afficher_un_employe.php?id=".$message_ajout_user["idEmploye"]."' class='cancel_ajout_user'>";
                                        echo "<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' fill='currentColor' class='bi bi-x-circle-fill' viewBox='0 0 16 16'><path d='M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z'/></svg>";
                                    echo "</a>";
                                    echo "<span>";
                                        echo "<strong>".mb_convert_case($message_ajout_user["prenom"], 2)." ".mb_strtoupper($message_ajout_user["nom"])." </strong> (UTILSATEUR) est bien ajouté dans la base. ";
   
                                echo "</span>";
                            echo "</div>";
                        }
                    }
                    ?>
                </div>
            </div>    
        </div>
    </div>
</body>
</html>