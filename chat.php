<?php
// Le chat doit renvoyer du JSON
header("Content-Type: application/json");

// Démarrage de la session (mémoire + limites)
session_start();

// Chargement de la clé API
require_once __DIR__ . "/../../config.php";

// Sécurité : clé absente
if (!defined("OPENAI_API_KEY")) {
    echo json_encode(["reply" => "Configuration serveur manquante"]);
    exit;
}

// Lecture du message envoyé par le frontend
$data = json_decode(file_get_contents("php://input"), true);
$message = trim($data["message"] ?? "");

// Sécurité : message vide
if ($message === "") {
    echo json_encode(["reply" => "Message vide"]);
    exit;
}

/* --------------------------------------------------
   LIMITATION DU NOMBRE DE MESSAGES
-------------------------------------------------- */

if (!isset($_SESSION["message_count"])) {
    $_SESSION["message_count"] = 0;
}

$_SESSION["message_count"]++;

if ($_SESSION["message_count"] > 20) {
    echo json_encode([
        "reply" => "Tu as atteint la limite de messages pour cette session."
    ]);
    exit;
}

/* --------------------------------------------------
   INITIALISATION DE LA MÉMOIRE DE CONVERSATION
-------------------------------------------------- */

if (!isset($_SESSION["conversation"])) {
    $_SESSION["conversation"] = [
        [
            "role" => "system",
            "content" => "Tu es le chatbot du portfolio de Abdel étudiant en métiers du multimédia et de l'internet (MMI). Tu présentes ses projets (les différents projets en mmi) , ses compétences (les compétences en mmi). Tu réponds de manière simple et humaine. Si une question n’a rien à voir avec le portfolio, tu expliques poliment que tu es là uniquement pour parler de ce sujet n'essaie pas de faire des liens qui n'ont rien à voir avec le portfolio si la question de l'utilisateur est hors sujet n'essaie pas de créer des liens inappropriés."
        ]
    ];
}

// Ajout du message utilisateur à la mémoire
$_SESSION["conversation"][] = [
    "role" => "user",
    "content" => $message
];

/* --------------------------------------------------
   APPEL À L’API OPENAI (API RESPONSES)
-------------------------------------------------- */

$payload = [
    "model" => "gpt-4.1-mini",
    "input" => $_SESSION["conversation"]
];

$ch = curl_init("https://api.openai.com/v1/responses");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . OPENAI_API_KEY
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);

// Erreur réseau
if ($response === false) {
    echo json_encode([
        "reply" => "Erreur serveur : " . curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);

$result = json_decode($response, true);

// Récupération du texte de réponse
$reply = $result["output"][0]["content"][0]["text"] ?? null;

// Sécurité : réponse inattendue
if (!$reply) {
    echo json_encode([
        "reply" => "Le bot n’a pas réussi à formuler une réponse."
    ]);
    exit;
}

// Ajout de la réponse du bot à la mémoire
$_SESSION["conversation"][] = [
    "role" => "assistant",
    "content" => $reply
];

// Envoi de la réponse au frontend
echo json_encode([
    "reply" => $reply
]);
