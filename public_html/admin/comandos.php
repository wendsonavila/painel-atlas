<?php

// Inicializar sessão
if (!isset($_SESSION)) {
    error_reporting(0);
    session_start();
}

// Verificar se a sessão existe
if (!isset($_SESSION['login']) &&!isset($_SESSION['senha'])) {
    session_destroy();
    unset($_SESSION['login']);
    unset($_SESSION['senha']);
    header('location:index.php');
    exit;
}

// Incluir conexão com o banco de dados
include('../atlas/conexao.php');
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("Connection failed: ". mysqli_connect_error());
}

// Verificar permissão de acesso
if ($_SESSION['login']!= 'admin') {
    echo "<script>alert('Você não tem permissão para acessar essa página!');window.location.href='../logout.php';</script>";
    exit;
}

// Função para evitar ataques de injeção de SQL
function anti_sql($input) {
    $seg = preg_replace_callback("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i", function($match) {
        return '';
    }, $input);
    $seg = trim($seg);
    $seg = strip_tags($seg);
    $seg = addslashes($seg);
    return $seg;
}

// Verificar se o formulário foi enviado
if (!empty($_POST['id'])) {
    $id = anti_sql($_POST['id']);
    $comando = anti_sql($_POST['comando']);

    // Configurações de tempo limite
    set_time_limit(0);
    ignore_user_abort(true);

    // Incluir biblioteca Net/SSH2
    set_include_path(get_include_path(). PATH_SEPARATOR. '../lib2');
    include ('Net/SSH2.php');

    // Pegar credenciais do servidor do banco de dados
    $sql2 = "SELECT * FROM servidores WHERE id = '$id'";
    $result = $conn->query($sql2);
    $row = mysqli_fetch_assoc($result);
    $login = $row['usuario'];
    $senha = $row['senha'];
    $porta = $row['porta'];
    $ip = $row['ip'];

    // Conectar ao servidor via SSH
    try {
        $ssh = new Net_SSH2($ip, $porta);
        if (!$ssh->login($login, $senha)) {
            echo "Não foi possível autenticar";
        } else {
            $ssh->exec("$comando > /dev/null 2>&1 &");
            $ssh->disconnect();
            echo "Comando enviado com sucesso";
        }
    } catch (Exception $e) {
        echo "Não foi possível conectar ao servidor";
    }
}

?>