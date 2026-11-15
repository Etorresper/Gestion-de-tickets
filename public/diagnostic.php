<?php
// public/diagnostic.php
require_once '../config.php';
require_once '../app/Services/GmailService.php';

use App\Services\GmailService;

$gmail = new GmailService();

echo "<h3>Diagnóstico de OAuth 2.0 de Google</h3>";
echo "<p><strong>GMAIL_CLIENT_ID:</strong> <pre>" . htmlspecialchars(GMAIL_CLIENT_ID) . "</pre></p>";
echo "<p><strong>GMAIL_CLIENT_SECRET:</strong> <pre>" . htmlspecialchars(GMAIL_CLIENT_SECRET) . "</pre></p>";
echo "<p><strong>GMAIL_REDIRECT_URI:</strong> <pre>" . htmlspecialchars(GMAIL_REDIRECT_URI) . "</pre></p>";
echo "<p><strong>GMAIL_MOCK_MODE:</strong> " . (GMAIL_MOCK_MODE ? "TRUE (Simulación)" : "FALSE (Real)") . "</p>";

$authUrl = $gmail->getAuthUrl();
echo "<p><strong>URL de Autorización Generada:</strong></p>";
echo "<textarea style='width: 100%; height: 150px;'>" . htmlspecialchars($authUrl) . "</textarea>";

echo "<p><a href='" . htmlspecialchars($authUrl) . "' target='_blank'>Hacer clic aquí para probar redirección directa</a></p>";
