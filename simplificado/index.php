<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'gestao_contratos_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para buscar os dados conforme solicitado no quesito 1 do teste
    $sql = "
        SELECT 
            tb_banco.nome AS nome_banco,
            tb_convenio.verba,
            tb_contrato.codigo AS codigo_contrato,
            tb_contrato.data_inclusao,
            tb_contrato.valor,
            tb_contrato.prazo
        FROM 
            tb_contrato
        JOIN 
            tb_convenio_servico ON tb_contrato.convenio_servico = tb_convenio_servico.codigo
        JOIN 
            tb_convenio ON tb_convenio_servico.convenio = tb_convenio.codigo
        JOIN 
            tb_banco ON tb_convenio.banco = tb_banco.codigo
    ";

    // Executa a consulta
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Busca os resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Exibe os resultados no browser
    if ($resultados) {
        echo "<table border='1'>";
        echo "<tr>
                <th>Nome do Banco</th>
                <th>Verba</th>
                <th>Código do Contrato</th>
                <th>Data de Inclusão</th>
                <th>Valor</th>
                <th>Prazo</th>
              </tr>";
        foreach ($resultados as $row) {
            echo "<tr>
                    <td>{$row['nome_banco']}</td>
                    <td>{$row['verba']}</td>
                    <td>{$row['codigo_contrato']}</td>
                    <td>{$row['data_inclusao']}</td>
                    <td>{$row['valor']}</td>
                    <td>{$row['prazo']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum resultado encontrado.";
    }
} catch (PDOException $e) {
    echo "Erro na conexão ou consulta: " . $e->getMessage();
}
