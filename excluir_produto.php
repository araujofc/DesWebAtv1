<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

/*
 * O seguinte código apaga um produto específico. Somente o usuário que criou o produto
 * deve ser capaz de remover o produto.
 */

// conexão com o banco de dados
require_once('conexao_db.php');

// autenticação
require_once('autenticacao.php');

// array de resposta
$resposta = array();

// verifica se o usuário conseguiu autenticar
if (autenticar($db_con)) {

    // Verifica se o parâmetro 'id' foi enviado pelo cliente
    if (isset($_POST['id'])) {

        // Obtém o ID do produto a ser excluído
        $id = $_POST['id'];

        // Exclui o produto do banco de dados
        $consulta = $db_con->prepare("DELETE FROM produtos WHERE id = $id");
        
        // Verifica se a consulta foi executada com sucesso
        if ($consulta->execute()) {
            // Se o produto foi excluído corretamente do servidor, o cliente recebe "sucesso" com valor 1
            $resposta["sucesso"] = 1;
        } else {
            // Se ocorrer algum erro na execução da consulta, retorna uma mensagem de erro
            $resposta["sucesso"] = 0;
            $resposta["erro"] = "Erro ao excluir produto do BD: " . $consulta->error;
            $resposta["cod_erro"] = 2;
        }
    } else {
        // Se o parâmetro 'id' não foi enviado corretamente, retorna uma mensagem de erro
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "Campo 'id' não fornecido";
        $resposta["cod_erro"] = 3;
    }
} else {
    // Se a autenticação falhar, retorna uma mensagem de erro
    $resposta["sucesso"] = 0;
    $resposta["erro"] = "Usuário não autenticado";
    $resposta["cod_erro"] = 0;
}

// Fecha a conexão com o banco de dados
$db_con = null;

// Converte a resposta para o formato JSON
echo json_encode($resposta);
?>
