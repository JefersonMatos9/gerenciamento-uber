<?php
// Incluímos nossos arquivos mágicos
require_once __DIR__ . '/../autoload.php';

// Variável para guardar os resultados
$resultado = null;
$mensagem = '';

// Verificar se há uma mensagem de sucesso via GET
if (isset($_GET['mensagem'])) {
    $mensagem = htmlspecialchars($_GET['mensagem']);
}

// Criamos nossa conexão mágica
$database = new Database();
$db = $database->getConnection();
$uber = new Uber($db);

// Tratamento da ação de exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'excluir') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    // Tentar excluir o registro
    try {
        $resultado_exclusao = $uber->excluir($id);
        if ($resultado_exclusao) {
            $mensagem = "Registro excluído com sucesso! 🗑️";
        } else {
            $mensagem = "Erro ao excluir o registro. 😓";
        }
    } catch (PDOException $e) {
        $mensagem = "Erro de banco de dados: " . $e->getMessage();
    }
}

// Tratamento da ação de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editar') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        // Redirecionar para a página de edição com o ID específico
        header("Location: editar.php?id=" . $id);
        exit();
    }
}

// Se apertamos o botão de buscar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
    // Filtrar e validar a data de entrada de forma moderna
    $data = filter_input(INPUT_POST, 'data', FILTER_VALIDATE_REGEXP, [
        'options' => [
            'regexp' => '/^\d{4}-\d{2}-\d{2}$/' // Formato de data YYYY-MM-DD
        ]
    ]);

    // Verificar se a data não está vazia após sanitização
    if (!empty($data)) {
        // Buscamos as informações do dia
        $resultado = $uber->consultarDiario($data);
    } else {
        // Tratar data inválida
        $erro = "Data inválida. Use o formato AAAA-MM-DD.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>🕵️ Detetive das Viagens</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>

<body>
    <div class="container">
        <!-- Mostrar mensagens de sucesso ou erro -->
        <?php if (!empty($mensagem)): ?>
            <div class="mensagem" id="mensagem">
                <?= $mensagem ?>
            </div>
        <?php endif; ?>

        <h1>🕵️ Investigar um Dia Específico</h1>

        <!-- Formulário de busca -->
        <form method="POST" action="">
            <label>Escolha o dia:</label>
            <input type="date" name="data" required><br>
            <button type="submit" class="btn">🔍 Investigar</button>
        </form>

        <!-- Mostrar resultados -->
        <?php if (isset($resultado) && !empty($resultado)): ?>
            <div class="resultado">
                <h2>🚗 Resultados da Investigação</h2>
                <table>
                    <tr>
                        <th>Data</th>
                        <th>KM Rodado</th>
                        <th>Ganhos</th>
                        <th>Gastos</th>
                        <th>Lucro Total do Dia</th>
                        <th>Horas Trabalhadas:</th>
                        <th>Observação:</th>
                    </tr>
                    <?php foreach ($resultado as $linha): ?>
                        <tr>
                            <td><?= $linha['data'] ?></td>
                            <td><?= $linha['km_rodado'] ?> km</td>
                            <td>R$ <?= $linha['total_arrecadado'] ?></td>
                            <td>R$ <?= $linha['total_abastecido'] ?></td>
                            <td>R$ <?= $linha['lucro_do_dia']?></td>
                            <td> <?=$linha['hora_trabalhada']?></td>
                            <td><?= $linha['observacao']?></td>
                            <td>
                                <div class="action-buttons">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="editar">
                                        <input type="hidden" name="id" value="<?= $linha['id'] ?>">
                                        <button type="submit" class="btn editar">✏️ Editar</button>
                                    </form>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esse registro?');">
                                        <input type="hidden" name="action" value="excluir">
                                        <input type="hidden" name="id" value="<?= $linha['id'] ?>">
                                        <button type="submit" class="btn excluir">❌ Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p>🤷‍♂️ Nenhuma informação encontrada neste dia!</p>
        <?php endif; ?>

        <a href="index.php" class="btn voltar">⬅️ Voltar</a>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mensagemEl = document.getElementById('mensagem');
            
            if (mensagemEl) {
                // Desaparecer após 4 segundos
                setTimeout(() => {
                    mensagemEl.classList.add('ocultar');
                    
                    // Remover elemento do DOM após a transição
                    setTimeout(() => {
                        mensagemEl.remove();
                    }, 500); // Corresponde ao tempo de transição no CSS
                }, 4000);
            }
        });
    </script>
</body>

</html>
