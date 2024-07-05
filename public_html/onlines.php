<?php

ignore_user_abort(true);
set_time_limit(0);
$start_time = microtime(true);
$lockfile = 'lockfile.txt';

// Abre o arquivo de bloqueio com exclusão e bloqueio
$handle = fopen($lockfile, 'w+');
if (!flock($handle, LOCK_EX | LOCK_NB)) {
    echo "Outra pessoa já está acessando a página, tente novamente mais tarde.";
    exit;
}
include('atlas/conexao.php');
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
set_include_path(get_include_path() . PATH_SEPARATOR . 'lib2');
//verifica se existe o arquivo
if (!file_exists("admin/suspenderrev.php")) {
    echo ("O código levou 1.0 segundos para ser executado");
    exit;
}
    include ('Net/SSH2.php');
    include('vendor/event/autoload.php');
use React\EventLoop\Factory;
    //verifica se o arquivo existe
    unlink("onlines.txt");

    $dellusers = array();
    $limiterativo = "SELECT * FROM configs WHERE id = 1";
    $resultlimiterativo = mysqli_query($conn, $limiterativo);
    $rowlimiterativo = mysqli_fetch_assoc($resultlimiterativo);
    $limiterativo = $rowlimiterativo['corbarranav'];
    $limitertempo = $rowlimiterativo['corletranav'];
    //minutos para segundos
    //converte para int
    $limitertempo = intval($limitertempo);
    $limitertempo = $limitertempo * 60;
    if ($limitertempo < 300) {
        $limitertempo = 300;
    }
    if ($limiterativo == 1) {
    $sql = "CREATE TABLE IF NOT EXISTS limiter (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(30) NOT NULL,
        tempo TEXT NOT NULL
    )";
    mysqli_query($conn, $sql);
    $sqluserdel = "SELECT * FROM limiter WHERE tempo = 'Deletado'";
    $resultuserdel = mysqli_query($conn, $sqluserdel);
    
    if (mysqli_num_rows($resultuserdel) > 0) {
        $lista = '';
        while ($rowuserdel = mysqli_fetch_assoc($resultuserdel)) {
            $lista .= $rowuserdel['usuario'] . "\n";
        }
        $arquivo = fopen('limiter.txt', 'w');
        fwrite($arquivo, $lista);
        fclose($arquivo);
        $criado = true;
    }

    $killlimiter = "SELECT * FROM limiter";
    $resultkilllimiter = mysqli_query($conn, $killlimiter);
    if (mysqli_num_rows($resultkilllimiter) > 0) {
        $userskill = array();
        $killuser = true;
        while ($rowkilllimiter = mysqli_fetch_assoc($resultkilllimiter)) {
            $userskill[] = $rowkilllimiter['usuario'];
        }
    }
    }
    
    
    #tempo maximo que o servidor tem para responder
$sql = "SELECT id, ip, porta, usuario, senha FROM servidores";
$result = mysqli_query($conn, $sql);
$loop = Factory::create();
$senha = $_SESSION['token'];
$senha = md5($senha);
while ($user_data = mysqli_fetch_assoc($result)) {
    $conectado = false;
    $ipeporta = $user_data['ip'] . ':6969';
    $timeout = 3;
    $socket = @fsockopen($user_data['ip'], 6969, $errno, $errstr, $timeout);

    if ($socket) {
        fclose($socket);
        $loop->addTimer(0.001, function () use ($user_data, $conn, $criado, $userskill, $ipeporta, $senha) {
           // $comando = 'sudo ps -ef | grep -oP "sshd: \K\w+(?= \[priv\])" || true && sed "/^10.8.0./d" /etc/openvpn/openvpn-status.log | grep 127.0.0.1 | awk -F"," \'{print $1}\' && nc -q0 127.0.0.1 7505 echo "status" | grep -oP ".*?,\K.*?(?=,)" | sort | uniq | grep -v :';
            $comando = 'sudo ps -ef | grep -oP "sshd: \K\w+(?= \[priv\])" || true && sed "/^10.8.0./d" /etc/openvpn/openvpn-status.log | grep 127.0.0.1 | awk -F"," \'{print $1}\' && nc -q0 127.0.0.1 7505 echo "status" | grep -oP ".*?,\K.*?(?=,)" | sort | uniq | grep -v : || true && awk -v date="$(date -d \'60 seconds ago\' +\'%Y/%m/%d %H:%M:%S\')" \'$0 > date && /email:/ { sub(/.*email: /, "", $0); sub(/@gmail\.com$/, "", $0); if (!seen[$0]++) print }\' /var/log/v2ray/access.log';

            $write = fopen("onlines.txt", "a");
            $headers = array(
                'Senha: ' . $senha
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://' . $ipeporta);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('comando' => $comando)));
            $output = curl_exec($ch);
            //se conter sed: can't read /etc/openvpn/openvpn-status.log: No such file or directory remove
            $output = str_replace("sed: can't read /etc/openvpn/openvpn-status.log: No such file or directory", "", $output);
            fwrite($write, $output);
            fclose($write);
            if ($criado == true) {
                $local_file = 'limiter.txt';
                $nome = md5(uniqid(rand(), true));
                $nome = substr($nome, 0, 10);
                $nome = $nome . ".txt";
                $limiter_content = file_get_contents($local_file);

                $ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_URL, 'http://' . $ipeporta);
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch1, CURLOPT_POST, 1);
                curl_setopt($ch1, CURLOPT_POSTFIELDS, http_build_query(array('comando' => 'echo "' . $limiter_content . '" > /root/' . $nome)));
                curl_exec($ch1);
                curl_close($ch1);

                $ch2 = curl_init();
                curl_setopt($ch2, CURLOPT_URL, 'http://' . $ipeporta);
                curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch2, CURLOPT_POST, 1);
                curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query(array('comando' => 'sudo python3 /root/delete.py ' . $nome . ' > /dev/null 2>/dev/null &')));
                curl_exec($ch2);
                curl_close($ch2);
            }
            if (!empty($userskill)) {
                $killstring = implode("|", $userskill);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'http://' . $ipeporta);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('comando' => 'pgrep -f "' . $killstring . '" | xargs kill > /dev/null 2>/dev/null &')));
                curl_exec($ch);
                curl_close($ch);
            }
            $comando1 = 'ps -x | grep sshd | grep -v root | grep priv | wc -l';
            $comando2 = 'sed \'/^10.8.0./d\' /etc/openvpn/openvpn-status.log | grep 127.0.0.1 | awk -F\',\' \'{print $1}\' | wc -l';
            $headers = array(
                'Senha: ' . $senha
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://' . $ipeporta);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('comando' => $comando1)));
            $output = curl_exec($ch);
            curl_close($ch);
            $onlineserver = intval(trim($output));

            $ch2 = curl_init();
            curl_setopt($ch2, CURLOPT_URL, 'http://' . $ipeporta);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch2, CURLOPT_POST, 1);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query(array('comando' => $comando2)));
            $output2 = curl_exec($ch2);
            curl_close($ch2);
            $onlineserver += intval(trim($output2));
            echo $onlineserver;
            $sql_update = "UPDATE servidores SET onlines = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql_update);
            mysqli_stmt_bind_param($stmt, 'ii', $onlineserver, $user_data['id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        });

        $conectado = true;
        $sucess_servers[] = $user_data['ip'];
        $sucess = true;
    }

    if (!$conectado) {
        $servidores_com_erro[] = $user_data['ip'];
    }
}

$loop->run();
$sql22 = "DELETE FROM onlines";
mysqli_query($conn, $sql22);

function ler()
    {
        $read = fopen("onlines.txt", "r");
        $onlines = fread($read, filesize("onlines.txt"));
        fclose($read);
        return $onlines;
    }
    $var = ler();
    $var = trim($var);
    $var = explode("\n", $var);

    foreach ($var as $value) {
        $value = trim($value);
        $value = mysqli_real_escape_string($conn, $value);
        $values[] = "('$value')";
    }

    $values = implode(',', $values);


    $sql = "UPDATE ssh_accounts SET status = 'Online' WHERE login IN ($values)";
    $result = mysqli_query($conn, $sql);
    $sql2 = "UPDATE ssh_accounts SET status = 'Offline' WHERE login NOT IN ($values)";
    $result2 = mysqli_query($conn, $sql2);
    $sql213 = "ALTER TABLE onlines MODIFY quantidade INT DEFAULT 0;";
    $result213 = mysqli_query($conn, $sql213);

    $sql = "INSERT INTO onlines (usuario) VALUES $values";
    mysqli_query($conn, $sql);
    if (mysqli_error($conn)) {
        echo mysqli_error($conn);
    }
    
    $sql = "SELECT * FROM onlines";
    $result = mysqli_query($conn, $sql);
    while ($user_data = mysqli_fetch_assoc($result)) {
        $sql = "UPDATE onlines SET quantidade = quantidade + 1 WHERE usuario = '$user_data[usuario]'";
        mysqli_query($conn, $sql);
    }
    $sql = "DELETE FROM onlines WHERE id NOT IN (SELECT * FROM (SELECT MIN(id) FROM onlines GROUP BY usuario) AS t)";
    $result = mysqli_query($conn, $sql);


if ($limiterativo == 1) {
    $delete = "DELETE FROM limiter WHERE tempo = 'Deletado'";
    mysqli_query($conn, $delete);
    }

    if ($limiterativo == 1) {
        $sql = "SELECT onlines.usuario, ssh_accounts.limite, onlines.quantidade FROM onlines JOIN ssh_accounts ON onlines.usuario = ssh_accounts.login WHERE onlines.quantidade > ssh_accounts.limite";
        $result = mysqli_query($conn, $sql);
        $usuarios = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $usuarios[] = $row['usuario'];
        }
        
        $sqllimiter = "SELECT * FROM limiter";
        $resultlimiter = mysqli_query($conn, $sqllimiter);
        
        # Create a dictionary to keep track of the time a user has been exceeding their limit
        $exceeded_users = array();
        
        date_default_timezone_set('America/Sao_Paulo');
        $now = date('Y-m-d H:i:s');

        while ($rowlimiter = mysqli_fetch_assoc($resultlimiter)) {
            $usuario_limite = $rowlimiter['usuario'];
            $tempo = $rowlimiter['tempo'];
            if ($usuario_limite == 'root') {
                continue;
            }
            $sqlcheck = "SELECT onlines.quantidade FROM onlines JOIN ssh_accounts ON onlines.usuario = ssh_accounts.login WHERE onlines.usuario = '$usuario_limite' AND onlines.quantidade > ssh_accounts.limite";
            $resultcheck = mysqli_query($conn, $sqlcheck);
            
            if (mysqli_num_rows($resultcheck) > 0) {
                $diff = 0;
                $tempoTimestamp = strtotime($tempo);
                if ($tempoTimestamp !== false) {
                    $diff = strtotime($now) - $tempoTimestamp;
                }
            
                if ($diff > $limitertempo) {
                    unset($exceeded_users[$usuario_limite]);
                } else {
                    if (!array_key_exists($usuario_limite, $exceeded_users)) {
                        $exceeded_users[$usuario_limite] = date('Y-m-d H:i:s');
                    }
                }
            } else {
               $sqldel = "DELETE FROM limiter WHERE usuario = '$usuario_limite'";
                mysqli_query($conn, $sqldel);
                unset($exceeded_users[$usuario_limite]);
            }
        }
        
        
        $sqlcheckdel = "SELECT * FROM limiter";
        $resultcheckdel = mysqli_query($conn, $sqlcheckdel);
        while ($rowcheck = mysqli_fetch_assoc($resultcheckdel)) {
            date_default_timezone_set('America/Sao_Paulo');
            $timestamp = strtotime($rowcheck['tempo']);
            if ($timestamp === false) {
                continue;
            }
            $temporestante = $timestamp - time();
            $temporestante = $temporestante + $limitertempo;
            if ($temporestante < 0) {
               $sqldel = "UPDATE ssh_accounts SET mainid = 'Limite Ultrapassado' WHERE login = '$rowcheck[usuario]'";
                mysqli_query($conn, $sqldel);
                
                $sqlli = "UPDATE limiter SET tempo = 'Deletado' WHERE usuario = '$rowcheck[usuario]'";
                mysqli_query($conn, $sqlli);
                //salva em um txt
                
            }
        }
        
        # Faça um loop pelos usuários que estão excedendo o limite
        foreach ($usuarios as $usuario) {
            # Verifica se o usuário já está na tabela "limiter" ou no dicionário excedeu_usuários
            if (array_key_exists($usuario, $exceeded_users)) {
                continue;
            }
            
            $sqlinsert = "INSERT INTO limiter (usuario, tempo) VALUES ('$usuario', '$now')";
        mysqli_query($conn, $sqlinsert);
        }
            }
            unlink('limiter.txt');
$end_time = microtime(true);

// Calcula a diferença entre os tempos
$time_diff = $end_time - $start_time;

echo "O código levou " . $time_diff . " segundos para ser executado";
// Libera o bloqueio e fecha o arquivo de bloqueio
flock($handle, LOCK_UN);
fclose($handle);

// Remove o arquivo de bloqueio
unlink($lockfile);

?>
