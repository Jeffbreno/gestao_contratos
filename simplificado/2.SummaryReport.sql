SELECT 
    banco.nome AS nome_banco,
    convenio.verba,
    MIN(contrato.data_inclusao) AS data_contrato_mais_antigo,
    MAX(contrato.data_inclusao) AS data_contrato_mais_novo,
    SUM(contrato.valor) AS soma_valor_contratos
FROM 
    tb_contrato AS contrato
INNER JOIN 
    tb_convenio_servico AS servico ON contrato.convenio_servico = servico.codigo
INNER JOIN 
    tb_convenio AS convenio ON servico.convenio = convenio.codigo
INNER JOIN 
    tb_banco AS banco ON convenio.banco = banco.codigo
GROUP BY 
    banco.nome, convenio.verba
ORDER BY 
    banco.nome, convenio.verba;
