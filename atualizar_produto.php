<?php

// Adiciona cabeçalhos para permitir acesso CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");

/*
 * Script para atualizar um produto específico.
 * Somente o usuário que criou o produto deve ser capaz de atualizá-lo.
 */

// Conexão com o banco de dados
require_once('conexao_db.php');

// Autenticação
require_once('autenticacao.php');

// Array de resposta
$resposta = array();

// Verifica se o usuário conseguiu autenticar
if (autenticar($db_con)) {
    
    // Verifica se todos os parâmetros foram enviados pelo cliente
    if (isset($_POST['id']) && isset($_POST['novo_nome']) && isset($_POST['novo_preco']) && isset($_POST['nova_descricao']) && isset($_POST['nova_img'])) {
        
        // Obtém os parâmetros
        $id = $_POST['id'];
        $novo_nome = $_POST['novo_nome'];
        $novo_preco = $_POST['novo_preco'];
        $nova_descricao = $_POST['nova_descricao'];
        $nova_img = $_POST['nova_img'];
        
        // Atualiza o produto no banco de dados
        $consulta = $db_con->prepare("UPDATE produtos SET nome = '$novo_nome', preco = '$novo_preco', descricao = '$nova_descricao', img = '$nova_img' WHERE id = '$id' AND usuarios_login = '$login'");
        if ($consulta->execute()) {
            // Se o produto foi atualizado corretamente no servidor, o cliente 
            // recebe a chave "sucesso" com valor 1
            $resposta["sucesso"] = 1;
        } else {
            // Se ocorreu algum erro ao atualizar o produto no servidor, o cliente 
            // recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
            // motivo da falha.
            $resposta["sucesso"] = 0;
            $resposta["erro"] = "Erro ao atualizar produto no BD: " . $consulta->error;
            $resposta["cod_erro"] = 2;
        }
    } else {
        // Se a requisição foi feita incorretamente, ou seja, os parâmetros 
        // não foram enviados corretamente para o servidor, o cliente 
        // recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
        // motivo da falha.
        $resposta["sucesso"] = 0;
        $resposta["erro"] = "Campo requerido não preenchido";
        $resposta["cod_erro"] = 3;
    }
} else {
    // Se a autenticação falhar, o cliente recebe uma resposta negativa
    $resposta["sucesso"] = 0;
    $resposta["erro"] = "Usuário ou senha não confere";
    $resposta["cod_erro"] = 0;
}

// Fecha a conexão com o BD
$db_con = null;

// Converte a resposta para o formato JSON
echo json_encode($resposta);
?>
