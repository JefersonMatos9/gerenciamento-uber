<?php
// Classe Uber para gerenciar registros de viagens
class Uber {
    private $conn; // Conexão com o banco de dados
    private $table_name = "registros"; // Nome da tabela no banco de dados

    public function __construct($db) {
        if(!$db){
            throw new Exception("Conexão com o bando de dados não estabelecida.");
        }
        $this->conn = $db;
    }

    // Cadastrar um novo registro
    public function cadastrar($data, $kmRodado, $totalArrecadado, $totalAbastecido, $horaTrabalhada, $observacao) {
try{
    if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)){
        throw new Exception("Data inválida. Use o formato AAAA-MM-DD");
    }
    if(!is_numeric($kmRodado) || $kmRodado < 0){
        throw new Exception("Quilometragem inválida");
}
if(!is_numeric($totalArrecadado) || $totalArrecadado < 0){
    throw new Exception("Valor arrecadado inválido");
}
if(!is_numeric($totalAbastecido) || $totalAbastecido < 0){
    throw new Exception("Valor de abastecimento inválido.");
}
if(!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $horaTrabalhada)){
    throw new Exception('Formato de hora inválido. Use HH:MM');
}

$lucroDoDia = $totalArrecadado - $totalAbastecido;
        $query = "INSERT INTO $this->table_name 
                  (data, km_rodado, total_arrecadado, total_abastecido, hora_trabalhada, lucro_do_dia, observacao) 
                  VALUES (:data, :kmRodado, :totalArrecadado, :totalAbastecido, :horaTrabalhada, :lucroDoDia, :observacao)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':kmRodado', $kmRodado);
        $stmt->bindParam(':totalArrecadado', $totalArrecadado);
        $stmt->bindParam(':totalAbastecido', $totalAbastecido);
        $stmt->bindParam(':horaTrabalhada', $horaTrabalhada);
        $stmt->bindParam(':lucroDoDia', $lucroDoDia);
        $stmt->bindParam(':observacao', $observacao);

        if(!$stmt->execute()){
            throw new Exception('Erro ao cadastrar registro');
        }
        return true;
    }catch(PDOException $e ){
        throw new Exception("Erro no banco de dados: " . $e->getMessage());
    }
}

    // Consultar registros por data específica
    public function consultarDiario($data) {
        try{
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            throw new PDOException("Data inválida. Use o formato AAAA-MM-DD");
        }

        $query = "SELECT * FROM $this->table_name WHERE data = :data";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data', $data);
        $stmt->execute();

        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(empty($resultado)){
            throw new Exception('Nenhum registro encontrado para data informada');
    }
    return $resultado;
}catch(PDOException $e){
    throw new Exception('Erro ao consultar registros:'. $e->getMessage());
}
    }

    // Consultar resumo mensal
    public function consultarMensal($mes, $ano) {
        try{
        if (!ctype_digit((string)$mes) || $mes < 1 || $mes > 12) {
            throw new Exception("Mês Inválido");
        }
        if(!ctype_digit((string)$ano) || $ano < 1|| $ano > date('Y') + 1) {
            throw new Exception('Ano Inválido');
        }

        $query = "SELECT 
                    SUM(km_rodado) AS total_km, 
                    SUM(total_arrecadado) AS total_lucro, 
                    SUM(total_abastecido) AS total_gastos, 
                    SUM(TIME_TO_SEC(hora_trabalhada)) AS total_segundos_trabalhadas
                  FROM $this->table_name 
                  WHERE MONTH(data) = :mes AND YEAR(data) = :ano";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mes', $mes);
        $stmt->bindParam(':ano', $ano);
        $stmt->execute();

        $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$resultado['total_km'] && !$resultado['total_lucro'] && !$resultado['total_gastos']){
            throw new Exception('Nenhum registro encontrado para o paríodo informado');
        }
        return $resultado;
    }catch(PDOException $e){
        throw new Exception('Erro ao consultar registro mensais:'. $e->getMessage());
    }
}

    // Editar um registro existente
    public function editar($id, $data, $kmRodado, $totalArrecadado, $totalAbastecido, $horaTrabalhada, $observacao) {
        try{
            if (!is_numeric($id) || $id <= 0) {
                throw new Exception("ID inválido");
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
                throw new Exception("Data inválida. Use o formato AAAA-MM-DD");
            }
            if (!is_numeric($kmRodado) || $kmRodado < 0) {
                throw new Exception("Quilometragem inválida");
            }
            if (!is_numeric($totalArrecadado) || $totalArrecadado < 0) {
                throw new Exception("Valor arrecadado inválido");
            }
            if (!is_numeric($totalAbastecido) || $totalAbastecido < 0) {
                throw new Exception("Valor de abastecimento inválido");
            }
            if (!preg_match('/^\d{2}:\d{2}$/', $horaTrabalhada)) {
                throw new Exception("Formato de hora inválido. Use HH:MM");
            }
            //verifica se o registro existe
            $checkQuery = "SELECT id FROM $this->table_name WHERE id = :id";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':id', $id);
            $checkStmt->execute();
            
            if (!$checkStmt->fetch()) {
                throw new Exception("Registro não encontrado");
            }

        $query = "UPDATE $this->table_name 
                  SET data = :data, 
                      km_rodado = :kmRodado, 
                      total_arrecadado = :totalArrecadado, 
                      total_abastecido = :totalAbastecido, 
                      hora_trabalhada = :horaTrabalhada, 
                      observacao = :observacao 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':kmRodado', $kmRodado);
        $stmt->bindParam(':totalArrecadado', $totalArrecadado);
        $stmt->bindParam(':totalAbastecido', $totalAbastecido);
        $stmt->bindParam(':horaTrabalhada', $horaTrabalhada);
        $stmt->bindParam(':observacao', $observacao);

        if(!$stmt->execute()){
throw new Exception('Erro ao atualizar registro');
        }
        return true;
    }catch(PDOException $e){
        throw new Exception("Erro ao editar registro: " . $e->getMessage());
    }
}

    // Excluir um registro
    public function excluir($id) {
        try {
            if(!is_numeric($id) || $id <= 0){
                throw new Exception("ID inválido");
            }
             // Verifica se o registro existe
             $checkQuery = "SELECT id FROM $this->table_name WHERE id = :id";
             $checkStmt = $this->conn->prepare($checkQuery);
             $checkStmt->bindParam(':id', $id);
             $checkStmt->execute();
             
             if (!$checkStmt->fetch()) {
                 throw new Exception("Registro não encontrado");
             }

        $query = "DELETE FROM $this->table_name WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if(!$stmt->execute()){
            throw new Exception('Erro ao excluir registro');
        }
        return true;
    }catch(PDOException $e){
        throw new Exception('Erro ao excluir registro: '. $e->getMessage());
    }
}

    // Consultar registros dentro de um período
    public function consultarPeriodo($data_inicial, $data_final) {
        try{
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_inicial) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_final)) {
            throw new Exception("Datas inválidas. Use o formato AAAA-MM-DD");
        }
        if(strtotime($data_inicial) > strtotime($data_final)) {
            throw new Exception("A data inicial não pode ser maior que a data final");
        }

        $query = "SELECT 
                    SUM(km_rodado) AS total_km,
                    SUM(total_arrecadado) AS total_lucro,
                    SUM(total_abastecido) AS total_gastos,
                    SEC_TO_TIME(SUM(TIME_TO_SEC(hora_trabalhada))) AS total_horas_trabalhadas
                  FROM $this->table_name 
                  WHERE data BETWEEN :data_inicial AND :data_final";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data_inicial', $data_inicial);
        $stmt->bindParam(':data_final', $data_final);
        $stmt->execute();

        $resultado =  $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resultado['total_km'] && !$resultado['total_lucro'] && !$resultado['total_gastos']) {
            throw new Exception('Nenhum registro encontrado para o período informado');
    }
    return $resultado;
}catch(PDOException $e){
    throw new Exception('Erro ao consultar período: ' . $e->getMessage());
}
    }
}


