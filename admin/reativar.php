<?php 
if (!isset($_SESSION)){
   error_reporting(0);
session_start();

}       
set_time_limit(0); // Limite de tempo de execução: 2h. Deixe 0 (zero) para sem limite
ignore_user_abort(true); // Continua a execução mesmo que o usuário cancele o download
//include('headeradmin2.php');
include('../vendor/event/autoload.php');
use React\EventLoop\Factory;
set_include_path(get_include_path() . PATH_SEPARATOR . '../lib2');
include ('Net/SSH2.php');
//se a sessão não existir, redireciona para o login
if(!isset($_SESSION['login']) and !isset($_SESSION['senha'])){
    session_destroy();
    unset($_SESSION['login']);
    unset($_SESSION['senha']);
    header('location:index.php');
}
include('../atlas/conexao.php');
//include 'header2.php';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    
}
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
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $login = $row['login'];
    $senha = $row['senha'];
    $categoria = $row['categoriaid'];
    $validade = $row['expira'];
    $limite = $row['limite'];
    $uuid = $row['uuid'];
}
//validade para dias restantes
$validade = date('Y-m-d', strtotime($validade));
$hoje = date('Y-m-d');
$validade = strtotime($validade);
$hoje = strtotime($hoje);
$validade = $validade - $hoje;
$validade = floor($validade / (60 * 60 * 24));
if ($validade == 0) {
    $validade = 1;
}
$sql2 = "SELECT * FROM servidores WHERE subid = '$categoria'";
$result = mysqli_query($conn, $sql2);

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
        if ($sucess == true) {
            $suspenso = "";
            $sql3 = "UPDATE ssh_accounts SET mainid = '$suspenso' WHERE id = '$id'";
            if (mysqli_query($conn, $sql3)) {
                
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }
            echo 'reativado com sucesso';
        }
        $loop->run();
        mysqli_close($conn);



?>