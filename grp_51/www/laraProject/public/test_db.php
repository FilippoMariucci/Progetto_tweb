<?php
try {
    include_once('/home/grp_51/www/include/connect.php');
    
    $pdo = new PDO("mysql:host=$HOST;dbname=$DB", $USER, $PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connessione riuscita!<br>";
    echo "Host: $HOST<br>";
    echo "Database: $DB<br>";
    echo "User: $USER<br>";

    
} catch(PDOException $e) {
    echo "Errore connessione: " . $e->getMessage();
}
?>