<?php
    require("../sendgrid/sendgrid-php/sendgrid-php.php");

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
        $data["lastName"] = checkInput($_POST['lastName']);
        $data["firstName"] = checkInput($_POST['firstName']);
        $data["email"] = checkInput($_POST['email']);
        $data["phone"] = checkInput($_POST['phone']);
        $data["subject"] = checkInput($_POST['subject']);
        $data["message"] = checkInput($_POST['message']);
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
            $emailToText .= "Nom: {$data["lastName"]} \n";
        }

        if (empty($data["firstName"])) {
            $emailToText .= "Prénom: non-renseigné \n";
        } else if(strlen($data["firstName"]) < 3) {
            $data["firstNameError"] = "Votre prénom doit comporter au moins 3 caractères !";
            $data["isSuccess"] = false;
        } else if(strlen($data["firstName"]) > 30) {
            $data["firstNameError"] = "Votre prénom doit comporter moins de 30 caractères !";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Prénom: {$data["firstName"]} \n";
        }

        if (empty($data["email"])) {
            $data["emailError"] = "Je souhaiterais pouvoir vous recontacter !";
            $data["isSuccess"] = false;
        } else if(!isEmail($data["email"])) {
            $data["emailError"] = "Bizarre ce mail non !?";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Email: {$data["email"]} \n";
        }

        if (empty($data["phone"])) {
            $emailToText .= "Téléphone: non-renseigné \n";
        } else if (!isPhone($data["phone"])) {
            $data["phoneError"] = "Votre numéro de téléphone ne semble pas valide !";
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
        } else if (strlen($data["message"]) < 10) {
            $data["messageError"] = "Votre message doit comporter au moins 10 caractères !";
            $data["isSuccess"] = false;
        } else if (strlen($data["message"]) > 10000) {
            $data["messageError"] = "Votre message doit comporter moins de 10000 caractères !";
            $data["isSuccess"] = false;
        } else {
            $emailToText .= "Message: {$data["message"]} \n";
        }

        if ($data["isSuccess"]) {
            // Envoi du mail (ne fonctionne pas en local)
            // $headers = "From: {$data["firstName"]} {$data["lastName"]} <{$data["email"]}>\r\nReply-To: {$data["email"]}";
            // mail($emailTo, "Nouveau message", $emailToText, $headers);
            $from = new SendGrid\Email(null, $data['email']);
            $subject = "Nouveau mail sur le CV en ligne de " . $data['firstName'] . " " . $data['lastName'];
            $to = new SendGrid\Email(null, "baptistelise@orange.fr");
            $content = new SendGrid\Content("text/plain", $emailToText);
            $mail = new SendGrid\Mail($from, $subject, $to, $content);

            $apiKey = getenv('SENDGRID_API_KEY');
            $sg = new \SendGrid($apiKey);

            $response = $sg->client->mail()->send()->post($mail);
            echo $response->statusCode();
            echo $response->headers();
            echo $response->body();
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
?>