<?php

    use PHPMailer\PHPMailer\PHPMailer;

    require '../vendor/autoload.php';

    $data = [
        "lastName" => "",
        "firstName" => "",
        "email" => "",
        "phone" => "",
        "subject" => "",
        "message" => "",
        "lastNameError" => "",
        "firstNameError" => "",
        "emailError" => "",
        "phoneError" => "",
        "subjectError" => "",
        "messageError" => "",
        "isSuccess" => false
    ];
    $emailTo = "baptistelise@orange.fr";

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $data["lastName"] = checkInput($_POST["lastName"]);
        $data["firstName"] = checkInput($_POST["firstName"]);
        $data["email"] = checkInput($_POST["email"]);
        $data["phone"] = checkInput($_POST["phone"]);
        $data["subject"] = checkInput($_POST["subject"]);
        $data["message"] = checkInput($_POST["message"]);
        $data ["isSuccess"] = true;
        $emailToText = "";

        if (empty($data["lastName"])) {
            $data["lastNameError"] = "À qui ai-je l'honneur !?";
            $data["isSuccess"] = false;
        } else if (strlen($data["lastName"]) < 3) {
            $data["lastNameError"] = "Votre nom doit comporter au moins 3 caractères !";
            $data["isSuccess"] = false;
        } else if (strlen($data["lastName"]) > 30) {
            $data["lastNameError"] = "Votre nom doit comporter moins de 30 caractères !";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Nom: {$data["lastName"]} <br>";
        }

        if (empty($data["firstName"])) {
            $emailToText .= "Prénom: non-renseigné <br>";
        } else if(strlen($data["firstName"]) < 3) {
            $data["firstNameError"] = "Votre prénom doit comporter au moins 3 caractères !";
            $data["isSuccess"] = false;
        } else if(strlen($data["firstName"]) > 30) {
            $data["firstNameError"] = "Votre prénom doit comporter moins de 30 caractères !";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Prénom: {$data["firstName"]} <br>";
        }

        if (empty($data["email"])) {
            $data["emailError"] = "Je souhaiterais pouvoir vous recontacter !";
            $data["isSuccess"] = false;
        } else if(!isEmail($data["email"])) {
            $data["emailError"] = "Bizarre ce mail non !?";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Email: {$data["email"]} <br>";
        }

        if (empty($data["phone"])) {
            $emailToText .= "Téléphone: non-renseigné <br>";
        } else if (!isPhone($data["phone"])) {
            $data["phoneError"] = "Votre numéro de téléphone ne semble pas valide !";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Téléphone: {$data["phone"]} <br>";
        }

        if (empty($data["subject"]) || $data["subject"] === "Quel est le sujet de votre message ?") {
            $data["subjectError"] = "De quoi voulez-vous parler ?";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Sujet: {$data["subject"]} <br>";
        }

        if (empty($data["message"])) {
            $data["messageError"] = "Que voulez-vous me dire ?";
            $data["isSuccess"] = false;
        } else if (strlen($data["message"]) < 10) {
            $data["messageError"] = "Votre message doit comporter au moins 10 caractères !";
            $data["isSuccess"] = false;
        } else if (strlen($data["message"]) > 10000) {
            $data["messageError"] = "Votre message doit comporter moins de 10000 caractères !";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Message: {$data["message"]} <br>";
        }

        if ($data["isSuccess"]) {
            // Envoi du mail

            $mail = new PHPMailer(true);
            $fullName = (!empty($data["firstName"])) ? $data["firstName"] . " " : "";
            $fullName .= $data["lastName"];

            $mail->isSMTP();
            $mail->CharSet = "UTF-8";

            $mail->Host = "smtp.orange.fr";
            $mail->SMTPDebug = 0;
            $mail->SMTPAuth = true;
            $mail->Port  = 465;
            $mail->SMTPSecure = "ssl";
            $mail->Username = "baptistelise@orange.fr";
            $mail->Password = $_ENV["PASSWORD"];

            $mail->setFrom("baptistelise@orange.fr", $fullName);
            $mail->addAddress("baptistelise@orange.fr", "Baptiste Bidaux");
            $mail->addReplyTo($data["email"]);

            $mail->isHTML(true);
            $mail->Subject = "Nouveau message depuis le CV en ligne !";
            $mail->Body = $emailToText;

            $mail->send();
        }

        // On envoie toutes les données au format json pour permettre le traitement AJAX dans le fichier javascript
        echo json_encode($data);
    }

    function isEmail($var) {
        return filter_var($var, FILTER_VALIDATE_EMAIL);
    }

    function isPhone($var) {
        return preg_match("/^([+(\d]{1})(([\d+() -.]){5,16})([+(\d]{1})$/", $var);
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