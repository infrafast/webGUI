<?php
// upload.php

header('Content-Type: text/plain');

// Est-ce qu'un fichier nommé "file1" a été envoyé ?
if (isset($_FILES['file1']) && $_FILES['file1']['error'] === UPLOAD_ERR_OK) {
    echo "Download complete";
} else {
    echo "ERREUR: aucun fichier reçu ou erreur d'upload";
}

?>
