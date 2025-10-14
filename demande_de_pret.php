<?php
// Configuration pour WAMP
$host = 'localhost';
$dbname = 'vulnerable_app';
$username = 'root';    // Utilisateur par défaut WAMP
$password = '';        // Mot de passe par défaut WAMP (vide)

try {
    $pdo = new PDO("mysql:host=$host;port=3307;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
// Traitement du formulaire de demande de prêt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_loan'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $montant = $_POST['montant'];
    $motif = $_POST['motif'];
    
    // VULNÉRABILITÉ XSS STOCKÉE : Les données ne sont pas échappées avant insertion
    try {
        // Recherche de l'utilisateur par nom/prenom (vulnérable à l'injection SQL)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$nom]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Insertion de la demande de prêt avec les données non échappées
            $stmt = $pdo->prepare("INSERT INTO loan_requests (user_id, amount, description, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$user['id'], $montant, $motif]);
            
            $success_message = "Votre demande de prêt a été enregistrée.";
        } else {
            $error_message = "Utilisateur non trouvé.";
        }
    } catch(PDOException $e) {
        $error_message = "Erreur lors de l'enregistrement : " . $e->getMessage();
    }
    
    // Popup JavaScript pour refuser le prêt
    echo "<script>alert('Votre demande de prêt de ' + " . floatval($montant) . " + ' euros a été refusée.');</script>";
}

// Récupération des demandes de prêt pour affichage
try {
    $stmt = $pdo->query("
        SELECT u.username, lr.amount, lr.description, lr.created_at, lr.status 
        FROM loan_requests lr 
        JOIN users u ON lr.user_id = u.id 
        ORDER BY lr.created_at DESC 
        LIMIT 10
    ");
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $demandes = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Banque Trésor De Smaug - Demande de Prêt</title>
</head>
<body>
    <h1>Banque Trésor De Smaug	- Demande de Prêt</h1>
    
    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo $success_message; ?></p>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <h2>Formulaire de Demande de Prêt</h2>
    <form method="POST" action="">
        <table>
            <tr>
                <td><label for="nom">Nom d'utilisateur :</label></td>
                <td><input type="text" id="nom" name="nom" required placeholder="Ex: alice"></td>
            </tr>
            <tr>
                <td><label for="prenom">Prénom :</label></td>
                <td><input type="text" id="prenom" name="prenom" required></td>
            </tr>
            <tr>
                <td><label for="montant">Montant :</label></td>
                <td><input type="number" id="montant" name="montant" min="100" max="100000" step="0.01" required> €</td>
            </tr>
            <tr>
                <td><label for="motif">Motif du prêt :</label></td>
                <td><textarea id="motif" name="motif" rows="4" cols="50" required placeholder="Décrivez l'utilisation du prêt..."></textarea></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" name="submit_loan" value="Soumettre la demande">
                    <input type="reset" value="Effacer">
                </td>
            </tr>
        </table>
    </form>

    <h2>Historique des Demandes de Prêt</h2>
    <?php if (!empty($demandes)): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Montant</th>
                    <th>Description</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($demandes as $demande): ?>
                    <tr>
                        <!-- VULNÉRABILITÉ XSS STOCKÉE : Les données de la base sont affichées sans échappement -->
                        <td><?php echo $demande['username']; ?></td>
                        <td><?php echo number_format($demande['amount'], 2, ',', ' '); ?> €</td>
                        <td><?php echo $demande['description']; ?></td>
                        <td><?php echo $demande['status']; ?></td>
                        <td><?php echo $demande['created_at']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune demande de prêt enregistrée.</p>
    <?php endif; ?>

    <hr>
    <h3>Informations pour le TP</h3>
    <p>Comptes de test disponibles :</p>
    <ul>
        <li>Utilisateur : <strong>alice</strong></li>
        <li>Utilisateur : <strong>bob</strong></li>
        <li>Utilisateur : <strong>charlie</strong></li>
    </ul>
    <p><em>Ce formulaire contient une vulnérabilité XSS stockée pour des exercices de cybersécurité.</em></p>
</body>
</html>


<!-- Image de Smaug -->
<img src="https://flashmuseum.org/wp-content/uploads/2023/05/South-Parks-Heroin-Hero_logo.png"  alt="Smaug le dragon" style="width: 300px; margin: 20px;">

<!-- Lecteur audio avec support FLAC -->
<audio autoplay loop controls style="display: none;" id="dragonAudio">
    <source src="ost.flac" type="audio/flac">
    Votre navigateur ne supporte pas l'élément audio.
</audio>

<script>
// Gestion de la lecture FLAC
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('dragonAudio');
    
    // Tenter la lecture après interaction utilisateur
    document.body.addEventListener('click', function initAudio() {
        audio.play().catch(e => {
            console.log("Lecture FLAC bloquée, besoin d'interaction:", e);
        });
        document.body.removeEventListener('click', initAudio);
    });
});
</script>