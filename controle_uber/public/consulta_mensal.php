<?php

require_once __DIR__ . '/../autoload.php';



$resultado = null;


function secondsToHoursMinutes($seconds) { 
    $hours = floor($seconds / 3600); 
    $minutes = floor(($seconds % 3600) / 60); 
    return sprintf("%02d:%02d", $hours, $minutes); 
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $mes = filter_input(INPUT_POST, 'mes', FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1, 
            'max_range' => 12
        ]
    ]);
    $ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 2000, 
            'max_range' => date('Y')
        ]
    ]);

    
    if ($mes && $ano) {
        $database = new Database();
        $db = $database->getConnection();
        $uber = new Uber($db);

        $resultado = $uber->consultarMensal($mes, $ano);
        if (isset($resultado['total_segundos_trabalhadas'])) {
         $resultado['total_horas_trabalhadas'] = secondsToHoursMinutes($resultado['total_segundos_trabalhadas']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Mensal de Dados - Uber</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <div class="container">
        <h1>Consultar Dados Mensais</h1>
        
        <form method="POST" action="">
            <label for="mes">Mês:</label>
            <input type="number" 
                   id="mes" 
                   name="mes" 
                   min="1" 
                   max="12" 
                   required 
                   placeholder="Insira o mês (1-12)"
                   value="<?php echo isset($_POST['mes']) ? htmlspecialchars($_POST['mes']) : ''; ?>">
            
            <label for="ano">Ano:</label>
            <input type="number" 
                   id="ano" 
                   name="ano" 
                   min="2000" 
                   max="<?php echo date('Y'); ?>" 
                   required 
                   placeholder="Insira o ano"
                   value="<?php echo isset($_POST['ano']) ? htmlspecialchars($_POST['ano']) : ''; ?>">
            
            <button type="submit" class="btn">Consultar</button>
        </form>

        <?php if ($resultado): ?>
            <div class="resultado">
                <h2>Resultados do Mês:</h2>
                <p>
                    <strong>Total KM Rodados:</strong> 
                    <?php echo number_format($resultado['total_km']?? 0, 2, ',', '.'); ?>
                </p>
                <p>
                    <strong>Total Recebido:</strong> 
                    R$ <?php echo number_format($resultado['total_lucro']?? 0, 2, ',', '.'); ?>
                </p>
                <p>
                    <strong>Gastos Totais:</strong> 
                    R$ <?php echo number_format($resultado['total_gastos']?? 0, 2, ',', '.'); ?>
                </p>
                <p>
                    <strong>Lucro Líquido:</strong> 
                    R$ <?php echo number_format(($resultado['total_lucro'] ?? 0)- ($resultado['total_gastos']?? 0), 2, ',', '.'); ?>
                </p>
                <p>
                    <strong>Total de Horas Trabalhadas:</strong>
                    <?php echo $resultado['total_horas_trabalhadas'] ?? '00:00'; ?>
                </p>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="resultado">
                <p>Nenhum dado encontrado para o período informado.</p>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn voltar">Voltar</a>
    </div>
</body>
</html>