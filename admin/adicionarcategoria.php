<?php
// Inclusão de arquivos necessários
require_once('../atlas/conexao.php');
require_once('headeradmin2.php');

// Conexão com o banco de dados
$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Função para escapar strings para evitar ataques de injeção de SQL
function escape_string($input) {
    return $conn->real_escape_string($input);
}

// Processamento do formulário
if (isset($_POST['criarcategoria'])) {
    $nomecategoria = escape_string($_POST['nomecategoria']);
    $idcategoria = escape_string($_POST['idcategoria']);

    // Verificar se o subid já existe
    $sql = "SELECT * FROM categorias WHERE subid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idcategoria);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>swal('Erro!', 'O ID da categoria já existe!', 'error').then((value) => {window.location='adicionarcategoria.php'});</script>";
        exit;
    }

    // Inserir categoria no banco de dados
    $sql = "INSERT INTO categorias (nome, subid) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nomecategoria, $idcategoria);
    if ($stmt->execute()) {
        echo "<script>swal('Sucesso!', 'Categoria criada com sucesso!', 'success').then((value) => {window.location='categorias.php'});</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!-- HTML e JavaScript -->
<script src="../app-assets/sweetalert.min.js"></script>
<div class="app-content content">
    <!-- Conteúdo do formulário e da página -->
    <form class="forms-sample" action="adicionarcategoria.php" method="POST">
        <!-- Campos do formulário -->
        <div class="form-group">
            <label for="exampleInputUsername1">Nome</label>
            <input type="text" class="form-control" name="nomecategoria" placeholder="Ex: Servidor 1" value="">
        </div>
        <div class="form-group">
            <label for="exampleInputPassword1">Id da Categoria</label>
            <input type="text" class="form-control" name="idcategoria" placeholder="Insira o Id" value="1">
        </div>
        
        <button type="submit" id="criarcategoria" name="criarcategoria" class="btn btn-primary mr-2">Criar</button>
        <a href="home.php" class="btn btn-dark" id="sair" name="sair" >Cancelar</a>
    </form>
</div>