<?php
// Inclua o arquivo de conexão e a classe Uber
require_once __DIR__ . '/../autoload.php';
$database = new Database();
$db = $database->getConnection();
$uber = new Uber($db);

// Verifique se o ID foi passado
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Busque os dados do registro pelo ID 
    if ($id) { 
        $query = "SELECT * FROM registros WHERE id = :id";
         $stmt = $db->prepare($query);
         $stmt->bindParam(':id', $id); 
         $stmt->execute();
          $registro = $stmt->fetch(PDO::FETCH_ASSOC); 
          if (!$registro) 
          { 
            echo "Registro não encontrado."; 
            exit(); 
        }
     } else {
         echo "ID inválido."; 
         exit(); 
        }

    // Verifique se o formulário foi submetido para salvar as alterações
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = $_POST['data'];
        $kmRodado = $_POST['kmRodado'];
        $totalArrecadado = $_POST['totalArrecadado'];
        $totalAbastecido = $_POST['totalAbastecido'];
        $horaTrabalhada = $_POST['horaTrabalhada'];
        $observacao = $_POST['observacao'];

        // Atualize o registro no banco de dados
        $uber->editar($id, $data, $kmRodado, $totalArrecadado, $totalAbastecido, $horaTrabalhada, $observacao);
        
        // Redirecione após a edição
        header("Location: index.php?mensagem=Registro atualizado com sucesso!");
        exit();
    }
} else {
    echo "ID não foi passado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <div class="container">
    <h1>Editar Registro</h1>
    <?php if (!empty($mensagem_sucesso)): ?>

        <div class="mensagem" id="mensagem">
            <=? $mensagem_sucesso ?>
        </div>
        <?php endif; ?>
        <?php if ($registro): ?>
    <form method="POST" action="">
        Data: <input type="date" name="data" value="<?= $registro['data'] ?>" required><br>
        KM Rodado: <input type="number" name="kmRodado" value="<?= $registro['km_rodado'] ?>" required><br>
        Total Arrecadado: <input type="number" name="totalArrecadado" value="<?= $registro['total_arrecadado'] ?>" required><br>
        Total Abastecido: <input type="number" name="totalAbastecido" value="<?= $registro['total_abastecido'] ?>" required><br>
        Hora Trabalhada: <input type="time" name="horaTrabalhada" value="<?= $registro['hora_trabalhada'] ?>" required><br>
        Observação: <input type="text" name="observacao" value="<?= $registro['observacao'] ?>" ><br>
        <button type="submit">Salvar Alterações</button>
    </form>
    <a href="index.php" class="btn voltar">⬅️ Voltar</a>
    <?php else: ?>
    <p>Registro não encontrado.</p>
    <?php endif; ?>
    </div>
    <script> 
    document.addEventListener('DOMContentLoaded', function() { 
        const mensagemEl = document.getElementById('mensagem');
         if (mensagemEl) {
         // Desaparecer após 4 segundos 
         setTimeout(() => { mensagemEl.classList.add('ocultar'); 
            // Remover elemento do DOM após a transição 
            setTimeout(() => { mensagemEl.remove(); }, 500); 
            // Corresponde ao tempo de transição no CSS
             }, 4000); } }); </script>
</body>
</html>
