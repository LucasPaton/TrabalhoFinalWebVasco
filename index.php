<?php
// Determina a pasta base dinamicamente para suportar subpastas no Apache (XAMPP)
$baseDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

// Redireciona o usuário para a página inicial (Home) do GOMOS
header("Location: " . $baseDir . "/gomos/public/");
exit();
