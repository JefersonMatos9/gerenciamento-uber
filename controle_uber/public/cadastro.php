<?php
// Incluímos nossos arquivos mágicos
require_once __DIR__ . '/../autoload.php';


// Variável para mostrar mensagens
$mensagem = "";

// Se apertamos o botão de salvar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pegamos todas as informações
    $data = $_POST['data'];
    $kmRodado = $_POST['km_rodado'];
    $totalArrecadado = $_POST['total_arrecadado'];
    $totalAbastecido = $_POST['total_abastecido'];
    $horaTrabalhada = $_POST['hora_trabalhada'] . ":00";
    $observacao = $_POST['observacao'];

    // Criamos nossa conexão mágica
    $database = new Database();
    $db = $database->getConnection();
    $uber = new Uber($db);

    // Tentamos salvar os dados
    if ($uber->cadastrar($data, $kmRodado, $totalArrecadado, $totalAbastecido, $horaTrabalhada, $observacao)) {
        $mensagem = "🎉 Dados salvos com sucesso! Viagem registrada!";
    } else {
        $mensagem = "🙁 Ops! Não consegui salvar a viagem.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>📝 Registrar Viagem</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <div class="container">
        <h1>📝 Registrar Nova Viagem</h1>
        
        <!-- Mensagem de resultado -->
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <!-- Formulário mágico -->
        <form method="POST" action="">
            <label>Data da Viagem:</label>
            <input type="date" name="data" required><br>
            
            <label>Quantos KM rodou:</label>
            <input type="number" step="0.1" name="km_rodado" required><br>
            
            <label>Quanto ganhou:</label>
            <input type="number" step="0.01" name="total_arrecadado" required><br>
            
            <label>Quanto gastou com combustível:</label>
            <input type="number" step="0.01" name="total_abastecido" required><br>

            <label>Quantidade de Horas Trabalhadas:</label>
            <input type="time" name="hora_trabalhada" required><br>

            <label>Alguma obervação a destacar do dia?</label>
            <input type="text" step="0.01" name="observacao" required><br>
            
            <button type="submit" class="btn">🚀 Salvar Viagem</button>
        </form>

        <a href="index.php" class="btn voltar">⬅️ Voltar</a>
    </div>
</body>
</html>