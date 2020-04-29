<?php
    $data = [
        "firstName" => "",
        "lastName" => "",
        "email" => "",
        "phone" => "",
        "subject" => "",
        "message" => "",
        "firstNameError" => "",
        "lastNameError" => "",
        "emailError" => "",
        "phoneError" => "",
        "subjectError" => "",
        "messageError" => "",
        "isSucces" => false
    ];
    $emailTo = "baptistelise@orange.fr";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $data["firstName"] = checkInput($_POST['firstName']);
        $data["lastName"] = checkInput($_POST['lastName']);
        $data["email"] = checkInput($_POST['email']);
        $data["phone"] = checkInput($_POST['phone']);
        $data["subject"] = checkInput($_POST['subject']);
        $data["message"] = checkInput($_POST['message']);
        $data ["isSuccess"] = true;
        $emailToText = "";

        if (empty($data["firstName"])) {
            $data["firstNameError"] = "Je veux connaître votre prénom !";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Prénom: {$data["firstName"]} \n";
        }

        if (empty($data["lastName"])) {
            $data["lastNameError"] = "Et oui je veux tout savoir. Même votre nom !";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Nom: {$data["lastName"]} \n";
        }

        if(!isEmail($data["email"])) {
            $data["emailError"] = "Bizarre ce mail non !?";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Email: {$data["email"]} \n";
        }

        if (!isPhone($data["phone"])) {
            $data["phoneError"] = "Que des chiffres et/ou des espaces svp !";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Téléphone: {$data["phone"]} \n";
        }

        if (empty($data["subject"]) || $data['subject'] === "Quel est le sujet de votre message ?") {
            $data["subjectError"] = "De quoi voulez-vous parler ?";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Sujet: {$data["subject"]} \n";
        }

        if (empty($data["message"])) {
            $data["messageError"] = "Que voulez-vous me dire ?";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Message: {$data["message"]} \n";
        }

        if ($data["isSuccess"]) {
            // Envoi du mail (ne fonctionne pas en local)
            // $headers = "From: {$data["firstName"]} {$data["lastName"]} <{$data["email"]}>\r\nReply-To: {$data["email"]}";
            // mail($emailTo, "Nouveau message", $emailToText, $headers);
        }

        // On envoie toutes les données au format json pour permettre le traitement AJAX dans le fichier javascript
        echo json_encode($data);
    }

    function isEmail($var) {
        return filter_var($var, FILTER_VALIDATE_EMAIL);
    }

    function isPhone($var) {
        return preg_match("/^[0-9 ]*$/", $var);
    }

    function checkInput($var) {
        // trim() supprime les espaces et les caractères invisibles en début et fin de chaîne.
        $var = trim($var);
        // stripslashes() supprime les antislashs dans une chaîne
        $var = stripslashes($var);
        // htmlspecialchars() convertit les caractères spéciaux en entités HTML
        $var = htmlspecialchars($var);

        return $var;
    }
?>