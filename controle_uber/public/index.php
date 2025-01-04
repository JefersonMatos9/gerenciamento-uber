<?php

require_once __DIR__ . '/../autoload.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚗 Meu Diário de Uber</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <div class="container">
        <h1>🚨 Central de Controle do Uber</h1>
        
        
        <div class="menu">
            <a href="cadastro.php" class="btn">📝 Registrar Viagem</a>
            <a href="consulta_diaria.php" class="btn">🔍 Ver Dia Específico</a>
            <a href="consulta_mensal.php" class="btn">📊 Resumo do Mês</a>
            <a href="selecionar_por_periodo.php" class="btn">🔍 Selecionar Por Período </a>
        </div>

        
        <div class="mensagem-inicial">
            <p>Olá, motorista! Aqui você pode guardar todos os seus segredos de viagem! 🚗✨</p>
        </div>
    </div>
</body>
</html>