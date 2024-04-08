<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");


// Conexão com o banco de dados
require_once('conexao_db.php');

// Autenticação
require_once('autenticacao.php');

// Array de resposta
$resposta = array();

// Verifica se o usuário conseguiu autenticar
if (autenticar($db_con)) {
    // Verifica se o parâmetro 'id' foi enviado ao servidor
    if (isset($_GET['id'])) {
        // Obtém o ID do produto
        $id_produto = $_GET['id'];

        // Realiza a consulta para obter os detalhes do produto com o ID fornecido
        $consulta = $db_con->prepare("SELECT nome, preco, descricao, usuarios_login, criado_em, img FROM produtos WHERE id = :id");
        $consulta->bindParam(':id', $id_produto);
        $consulta->execute();

        // Verifica se o produto foi encontrado
        if ($consulta->rowCount() > 0) {
            // Obtém os detalhes do produto
            $produto = $consulta->fetch(PDO::FETCH_ASSOC);

            // Monta a resposta positiva
            $resposta["sucesso"] = 1;
            $resposta["nome"] = $produto["nome"];
            $resposta["preco"] = $produto["preco"];
            $resposta["descricao"] = $produto["descricao"];
            $resposta["criado_por"] = $produto["usuarios_login"];
            $resposta["criado_em"] = $produto["criado_em"];
            $resposta["img"] = $produto["img"];
        } else {
            // Produto não encontrado
            $resposta["sucesso"] = 0;
            $resposta["erro"] = "Produto não encontrado";
            $resposta["cod_erro"] = 4;
        }
    } else {
        // Parâmetro 'id' não foi enviado
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "Faltam parâmetros";
        $resposta["cod_erro"] = 3;
    }
} else {
    // Autenticação falhou
    $resposta["sucesso"] = 0;
    $resposta["erro"] = "Autenticação falhou";
    $resposta["cod_erro"] = 0;
}

// Converte a resposta para o formato JSON e imprime
echo json_encode($resposta);
?>
