<?php
// Database instellingen
$servername = "jouw_mysql_server";
$username = "jouw_mysql_gebruikersnaam";
$password = "jouw_mysql_wachtwoord";
$dbname = "jouw_database_naam";

// Maak een verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding is gelukt
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Functie om gebruikersinvoer schoon te maken
function sanitize_input($input) {
    // Implementeer een veilige manier om invoer te controleren
    return $input;
}

// Functie om het wachtwoord te hashen
function hash_password($password) {
    // Gebruik een betrouwbare methode om het wachtwoord te versleutelen
    return password_hash($password, PASSWORD_DEFAULT);
}

// Controleer of het formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ontvang de ingevulde gegevens
    $gebruikersnaam = sanitize_input($_POST["gebruikersnaam"]);
    $wachtwoord = sanitize_input($_POST["wachtwoord"]);

    // Zoek de gebruiker in de database
    $sql = "SELECT id, gebruikersnaam, wachtwoord FROM gebruikers WHERE gebruikersnaam = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $gebruikersnaam);
    $stmt->execute();
    $stmt->bind_result($gebruiker_id, $db_gebruikersnaam, $db_wachtwoord);
    $stmt->fetch();
    $stmt->close();

    // Controleer of het ingevoerde wachtwoord overeenkomt met het opgeslagen wachtwoord
    if (password_verify($wachtwoord, $db_wachtwoord)) {
        // Wachtwoord is correct, start een sessie en stuur door naar de welkomstpagina
        session_start();
        $_SESSION["gebruiker_id"] = $gebruiker_id;
        header("Location: welkom.php");
        exit();
    } else {
        // Wachtwoord is onjuist
        $foutmelding = "Ongeldige gebruikersnaam of wachtwoord";
    }
}

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pagina</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($foutmelding)) { ?>
        <p style="color: red;"><?php echo $foutmelding; ?></p>
    <?php } ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="gebruikersnaam">Gebruikersnaam:</label>
        <input type="text" id="gebruikersnaam" name="gebruikersnaam" required><br>

        <label for="wachtwoord">Wachtwoord:</label>
        <input type="password" id="wachtwoord" name="wachtwoord" required><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
