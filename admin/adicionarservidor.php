<?php
// Inicia a sessão se ela ainda não foi iniciada
if (!isset($_SESSION)) {
    session_start();
}

// Se a sessão não existir, redireciona para a página de login
if (!isset($_SESSION['login']) || !isset($_SESSION['senha'])) {
    session_destroy();
    header('location:index.php');
    exit();
}

// Inclui o arquivo de conexão com o banco de dados
require_once '../atlas/conexao.php';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("Connection failed: ". mysqli_connect_error());
}

// Inclui o cabeçalho da página para usuários administradores
include('headeradmin2.php');
?>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <p class="text-primary">Novo Servidor</p>
        <div class="content-header row">
        </div>
        <div class="content-body">
            <section id="dashboard-ecommerce">
                <div class="row">
                    <section id="basic-horizontal-layouts">
                        <div class="row match-height">
                            <div class="col-md-6 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Cadastro de Novo Servidor</h4>
                                    </div>

                                    <div id="alerta">
                                    </div>

                                    <div class="card-content">
                                        <div class="card-body">
                                            <p class="card-description">Aqui Você Pode Editar Suas Formas De Pagamento</code></p>
                                            <form class="form form-horizontal" action="adicionarservidor.php" method="POST">
                                                <div class="form-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>Nome</label>
                                                        </div>
                                                        <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" name="nomeservidor" placeholder="Insira o Nome do Servidor">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Ip</label>
                                                        </div>
                                                        <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" name="ipservidor" placeholder="Insira o IP do Servidor">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Usuario</label>
                                                        </div>
                                                        <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" name="usuarioservidor" placeholder="Insira a Senha do Servidor" value="root">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Senha</label>
                                                        </div>
                                                        <div class="col-md-8 form-group">
                                                            <input type="password" class="form-control" name="senhaservidor" placeholder="Insira a Senha do Servidor">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Porta</label>
                                                        </div>
                                                        <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" name="portaservidor" placeholder="Porta do Servidor" value="22">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Categoria do Servidor</label>
                                                        </div>
                                                        <div class="col-md-8 form-group">
                                                            <input type="text" class="form-control" name="categoriaservidor" placeholder="Categoria" value="1">
                                                        </div>

                                                        <div class="col-12 col-md-8 offset-md-4 form-group">
                                                            <fieldset>
                                                                
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-sm-12 d-flex justify-content-end">
                                                            <button type="submit" class="btn btn-primary mr-1 mb-1" name="adcservidor">Salvar</button>
                                                            <a href="home.php" class="btn btn-light-secondary mr-1 mb-1">Cancelar</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </div>
    </div>
</div>

<?php
// Função para prevenir ataques de injeção de SQL
function anti_sql($input) {
    $seg = preg_replace