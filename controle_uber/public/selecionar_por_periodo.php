<?php
// Inclusão dos arquivos necessários
require_once __DIR__ . '/../autoload.php';

// Inicialização de variáveis
$resultado = null;
$mensagem = '';

// Verificação de mensagem de sucesso via GET
if (isset($_GET['mensagem'])) {
    $mensagem = htmlspecialchars($_GET['mensagem']);
}

// Conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();
$uber = new Uber($db);


// Tratamento da busca por período
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Filtra e valida as datas
    $data_inicial = filter_input(INPUT_POST, 'data_inicial', FILTER_VALIDATE_REGEXP, [
        'options' => ['regexp' => '/^\d{4}-\d{2}-\d{2}$/']
    ]);
    $data_final = filter_input(INPUT_POST, 'data_final', FILTER_VALIDATE_REGEXP, [
        'options' => ['regexp' => '/^\d{4}-\d{2}-\d{2}$/']
    ]);

    // Verificação de validade das datas
    if ($data_inicial && $data_final) {
        try {
            $resultado = $uber->consultarPeriodo($data_inicial, $data_final);
        } catch (PDOException $e) {
            $mensagem = "Erro de banco de dados: " . $e->getMessage();
        }
    } else {
        $mensagem = "Datas inválidas. Use o formato AAAA-MM-DD.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investigar Por Período</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <div class="container">
        <!-- Exibição de mensagem -->
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem" id="mensagem">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <h1>🕵️ Investigar Período Selecionado</h1>

        <!-- Formulário de Busca -->
        <form method="POST" action="">
            <div class="form-grupo">
                <label for="data_inicial">Data Inicial:</label>
                <input type="date" id="data_inicial" name="data_inicial" required>
            </div>

            <div class="form-grupo">
                <label for="data_final">Data Final:</label>
                <input type="date" id="data_final" name="data_final" required>
            </div>

            <button type="submit" class="btn">🔍 Investigar Período</button>
        </form>

        <!-- Exibição de Resultados -->
        <?php if (isset($resultado) && !empty($resultado)): ?>
            <div class="resultado">
                <h2>Resultados do Período</h2>
                <p><strong>Período:</strong> <?= date('d/m/Y', strtotime($data_inicial)) ?> a <?= date('d/m/Y', strtotime($data_final)) ?></p>
                <p><strong>Total KM Rodados:</strong> <?= number_format($resultado['total_km'] ?? 0, 2, ',', '.') ?></p>
                <p><strong>Total Recebido:</strong> R$ <?= number_format($resultado['total_lucro'] ?? 0, 2, ',', '.') ?></p>
                <p><strong>Gastos Totais:</strong> R$ <?= number_format($resultado['total_gastos'] ?? 0, 2, ',', '.') ?></p>
                <p><strong>Lucro Líquido:</strong> R$ <?= number_format(($resultado['total_lucro'] ?? 0) - ($resultado['total_gastos'] ?? 0), 2, ',', '.') ?></p>
                <p><strong>Total de Horas Trabalhadas:</strong> <?= $resultado['total_horas_trabalhadas'] ?? '00:00' ?></p>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="resultado">
                <p>Nenhum dado encontrado para o período informado.</p>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn voltar">⬅️ Voltar</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mensagemEl = document.getElementById('mensagem');

            if (mensagemEl) {
                setTimeout(() => {
                    mensagemEl.classList.add('ocultar');
                    setTimeout(() => mensagemEl.remove(), 500);
                }, 4000);
            }
        });
    </script>
</body>
</html>
