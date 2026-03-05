<?php 
session_start();
require "connexion.php";

if (!isset($_SESSION["id_user"])) {
    header("Location: login.php");
    exit();
}
$id_user = $_SESSION["id_user"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Macopres</title>
    <link rel="icon" type="image/png" href="logo.jpeg">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page">
        <div class="menu">
            <div class="company">
                <div class="logo-menu">
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
                <a href="gestion_utilisateurs.php" class="menu-item-utilisateurs">
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
                <a class="menu-item" href="gestion_employe.php">
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
                <div class="before-table">
                        <form method="get" class="search-box-form">
                            <input type="text" class="barre_de_recherche" name="search" placeholder="Rechercher un utilisateur." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                            <button type="submit" >
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                </svg>
                            </button>
                        </form>

                        <a href="ajout_employe.php" class="add">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                            </svg>
                            <div>Ajouter</div>
                        </a>    

                </div>

                <?php
                $req1 = mysqli_prepare($con, "SELECT * FROM user u JOIN role r ON u.idRole = r.idRole");
                

                if (isset($_GET["search"]) && $_GET["search"] != "") {
                    $search = mysqli_real_escape_string($con, $_GET["search"]);
                    $req1 = mysqli_prepare($con, "SELECT * FROM user u JOIN role r ON u.idRole = r.idRole WHERE u.nom LIKE ?  OR u.prenom LIKE ? OR u.tel LIKE ? OR r.nomRole LIKE ? OR u.statut LIKE ?");
                    mysqli_stmt_bind_param($req1, "sssss", $search, $search, $search, $search, $search);
                }
                mysqli_stmt_execute($req1);
                $result1 = mysqli_stmt_get_result($req1);
                ?>
                <div class="liste">
                    <table>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Téléphone</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>

                        <?php
                        while ($user1 = mysqli_fetch_assoc($result1)) {
                            $statut = $user1["statut"] == 1 ? "Actif" : "Bloqué";
                            echo "<tr>";
                            echo "<td>".mb_strtoupper($user1['nom'])."</td>";
                            echo "<td>".mb_convert_case($user1['prenom'],"2")."</td>";
                            echo "<td>".$user1['tel']."</td>";
                            
                            echo "<td><strong>".$user1['nomRole']."</strong></td>";
                            if ($statut == "Actif") {
                                echo "<td><div class='actif'>".$statut."</div></td>";
                            } else {
                                  echo "<td><div class='bloque'>".$statut."</div></td>";
                            }
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
                        ?>
                    </table>

                </div>
            </div>    
        </div>
    </div>
</body>
</html>