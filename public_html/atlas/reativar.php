<?php 
if (!isset($_SESSION)){
   error_reporting(0);
session_start();

}
set_include_path(get_include_path() . PATH_SEPARATOR . '../lib2');
include ('Net/SSH2.php');
if(!isset($_SESSION['login']) and !isset($_SESSION['senha'])){
    session_destroy();
    unset($_SESSION['login']);
    unset($_SESSION['senha']);
    header('location:index.php');
}
include('../atlas/conexao.php');
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    
}
include('../vendor/event/autoload.php');
use React\EventLoop\Factory;

function anti_sql($input)
{
    $seg = preg_replace_callback("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/i", function($match) {
        return '';
    }, $input);
    $seg = trim($seg);
    $seg = strip_tags($seg);
    $seg = addslashes($seg);
    return $seg;
}
//anti sql injection na $_GET['id']
$_GET['id'] = anti_sql($_GET['id']);

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM ssh_accounts WHERE id = '$id'";
    //consulta login e senha do id
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $login = $row['login'];
    $senha = $row['senha'];
    $categoria = $row['categoriaid'];
    $validade = $row['expira'];
    $limite = $row['limite'];
    $byid = $row['byid'];
    $uuid = $row['uuid'];
}
if ($byid == $_SESSION['iduser']) {
}else{
    echo "<script>sweetAlert('Oops...', 'Você não tem permissão para editar este usuário!', 'error').then(function(){window.location.href='../home.php'});</script>";
    unset($_POST['criaruser']);
    unset($_POST['usuariofin']);
    unset($_POST['senhafin']);
    unset($_POST['validadefin']);
    exit();
}
//time zone
date_default_timezone_set('America/Sao_Paulo');

//validade para dias restantes
$validade = date('Y-m-d', strtotime($validade));
$hoje = date('Y-m-d');
$validade = strtotime($validade);
$hoje = strtotime($hoje);
$validade = $validade - $hoje;
$validade = floor($validade / (60 * 60 * 24));
//todos os servidores com a categoria do usuario
if ($validade < 1) {
    $validade = 2;
}
$sql2 = "SELECT * FROM servidores WHERE subid = '$categoria'";
$result = mysqli_query($conn, $sql2);
          
          set_time_limit(0); // Limite de tempo de execução: 2h. Deixe 0 (zero) para sem limite
          ignore_user_abort(true); // Continua a execução mesmo que o usuário cancele o download
          $loop = Factory::create();
        $servidores_com_erro = [];
        $sucess = false;       
        $senhatoken = $_SESSION['token'];
        $senhatoken = md5($senhatoken);  

        while ($user_data = mysqli_fetch_assoc($result)) {
            $conectado = false;
                $ipeporta = $user_data['ip'] . ':6969';
                $timeout = 3;
                $socket = @fsockopen($user_data['ip'], 6969, $errno, $errstr, $timeout);
                if ($socket) {
                    fclose($socket);
                    $loop->addTimer(0.001, function () use ($user_data, $conn, $login, $senha, $validade, $limite, $senhatoken, $uuid) {
                        if ($uuid == ""){
                            $comando1 = 'sudo ./atlasremove.sh ' . $login;
                            $comando2 = 'sudo ./atlascreate.sh ' . $login . ' ' . $senha . ' ' . $validade . ' ' . $limite;
                        }else{
                            $comando1 = 'sudo ./rem.sh ' . $uuid . ' ' . $login;
                            $comando2 = 'sudo ./add.sh ' . $uuid . ' ' . $login . ' ' . $senha . ' ' . $validade . ' ' . $limite;
                        }
                    $headers = array(
                        'Senha: ' . $senhatoken
                    );
                    $ch = curl_init();
                               curl_setopt($ch, CURLOPT_URL, $user_data['ip'] . ':6969');
                               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                               curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                               curl_setopt($ch, CURLOPT_POST, 1);
                               curl_setopt($ch, CURLOPT_POSTFIELDS, "comando=$comando1");
                               $output = curl_exec($ch);
                                 curl_close($ch);
                                 $ch = curl_init();
                                 curl_setopt($ch, CURLOPT_URL, $user_data['ip'] . ':6969');
                                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                 curl_setopt($ch, CURLOPT_POST, 1);
                                 curl_setopt($ch, CURLOPT_POSTFIELDS, "comando=$comando2");
                                 $output = curl_exec($ch);
                                    curl_close($ch);
                                    
                });
                $sucess = true;
            } else {
                $servidores_com_erro[] = $user_data['ip'];
            }
        }
            
            

        if ($sucess == false) {
            echo '<script>swal("Erro!", "Erro ao conectar com o servidor!", "error").then(function() { window.location = "listarusuarios.php"; });</script>';
        }elseif ($sucess == true) {
            $suspenso = "";
            $sql3 = "UPDATE ssh_accounts SET mainid = '$suspenso' WHERE id = '$id'";
            
            if (mysqli_query($conn, $sql3)) {
                
            } else {
                //echo "Error updating record: " . mysqli_error($conn);
            }
            echo 'reativado com sucesso';
        }
        $loop->run();

?>