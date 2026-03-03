<head>
    <title>Macopres</title>
    <link rel="icon" type="image/png" href="logo.jpeg">
</head>
<style>
:root {
  --primary: #323E82;
  --bg: #ffffff;
}

.login-page {
  min-height: 100vh;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 3rem;
  justify-content: center;
  align-items: center;
  background: #F9FAFB;
  font-family: 'Inter', system-ui, sans-serif;
  padding: 2rem 1rem;
}

/* Carte */
.login-card {
  background: white;
  width: 90%;
  max-width: 26rem; /* ≈ 416px */
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  text-align: center;
}

/* Message erreur */
.echoue {
  background-color: rgb(234, 186, 186);
  animation: hide 10s forwards;
  padding: 1rem;
  width: 90%;
  max-width: 26rem;
  border: 1px solid red;
  border-radius: 0.5rem;
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

@keyframes hide {
  0% { opacity: 1; }
  90% { opacity: 1; }
  100% { opacity: 0; }
}

.login-card-head {
  display: flex;
  gap: 0.6rem;
  justify-content: center;
  align-items: center;
}

/* Logo */
.logo {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  overflow: hidden;
  padding: 0.1rem;
}

.logo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.login-card h1 {
  margin-bottom: 0.5rem;
  color: var(--primary);
  font-size: 1.5rem;
}

.subtitle {
  font-size: 0.95rem;
  color: #6B7280;
  margin-bottom: 1.5rem;
}

/* Champs */
.login,
.password {
  display: flex;
  width: 100%;
  padding: 0.8rem;
  margin-bottom: 1rem;
  border-radius: 0.6rem;
  border: 1px solid #D1D5DB;
  font-size: 0.9rem;
  gap: 0.6rem;
}

.login svg,
.password svg {
  color: gray;
  flex-shrink: 0;
}

.login-card input {
  border: none;
  width: 100%;
  font-size: 0.9rem;
}

/* Bouton */
.login-card button {
  width: 100%;
  background: var(--primary);
  color: white;
  padding: 0.8rem;
  border: none;
  border-radius: 0.8rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: 0.3s;
}

.login-card button:hover {
  transform: scale(0.97);
}

/* Options */
.login-options p {
  font-size: 0.8rem;
  color: #6B7280;
  margin-top: 1rem;
}

form {
  display: flex;
  flex-direction: column;
  align-items: center;
}

</style>


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
        <div class="logo">
            <img src="logo.jpeg" alt="macopres">
        </div>
        <h1>Macopres</h1>
    </div>
    <p class="subtitle">
      Connexion au système.
    </p>

    <form action="index_traitement.php" method="post">
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
