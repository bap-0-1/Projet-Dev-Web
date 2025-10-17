<?php
require_once('constants.php');
session_start();



// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: demande_de_pret.php');
    exit;
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // basic validation
    if ($username === '' || $password === '') {
        $login_error = 'Veuillez fournir nom d\'utilisateur et mot de passe.';
    } else {
        $mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
        if ($mysqli->connect_errno) {
            $login_error = 'Erreur connexion BDD.';
        } else {
            $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
            $res = $mysqli->query($query);

            if ($row = $res->fetch_assoc()) {
                    // Auth success
                    session_regenerate_id(true); // éviter fixation de session
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    header('Location: demande_de_pret.php');
                    exit;
                
            } else {
                $login_error = 'Identifiants invalides.'.var_dump($row, $stored);
            }

            $mysqli->close();
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Trésor de Smaug</title>
</head>
<body>
  <h1>Connexion</h1>
    <?php if ($login_error): ?>
    <p><?php echo htmlspecialchars($login_error); ?></p>
  <?php endif; ?>
  <form method="post" action="login.php">
    <label>
      Nom d'utilisateur:<br>
      <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
    </label>
    <br><br>
    <label>
      Mot de passe:<br>
      <input type="password" name="password">
    </label>
    <br><br>
    <button type="submit">Se connecter</button>
  </form>
</body>
</html>