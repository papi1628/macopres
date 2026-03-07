<head>
    <title>Macopres</title>
    <link rel="icon" type="image/png" href="logo.jpeg">
    <link rel="stylesheet" href="style.css">
</head>

<div class="login-page">
  <?php
    if (isset($_GET["log"])) {
      $log = $_GET["log"];
      if ($log == 0) { 
        echo "<div class='echoue'>";
          echo "<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-door-closed-fill' viewBox='0 0 16 16'>
      <path d='M12 1a1 1 0 0 1 1 1v13h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V2a1 1 0 0 1 1-1zm-2 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2'/>
    </svg>";
          echo "<span>";
            echo "<strong>Désolé, nom d'utilisateur ou mot de passe non reconnu.</strong>";
          echo "</span>";
        echo "</div>";
      }
    }  
  ?>
  <div class="login-card">
    
    <div class="login-card-head">
        <div class="logo-login">
            <img src="logo.jpeg" alt="macopres">
        </div>
        <h1>Macopres</h1>
    </div>
    <p class="subtitle">
      Connexion au système.
    </p>

    <form action="index_traitement.php" method="post" class="form-login">
        <div class="login">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
            </svg>
            <input type="text" name="login" placeholder="Identifiant" required>
        </div>
    
        <div class="password">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 0a4 4 0 0 1 4 4v2.05a2.5 2.5 0 0 1 2 2.45v5a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 13.5v-5a2.5 2.5 0 0 1 2-2.45V4a4 4 0 0 1 4-4M4.5 7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7zM8 1a3 3 0 0 0-3 3v2h6V4a3 3 0 0 0-3-3"/>
            </svg>
            <input type="password" name="password" placeholder="Mot de passe" required>
        </div>

      <button type="submit" name="connecter">
        Se connecter
      </button>

    </form>

    <div class="login-options">
      <p>Contactez l'admin si vous avez un probléme de connexion.</p>
    </div>

    
  </div>

</div>
