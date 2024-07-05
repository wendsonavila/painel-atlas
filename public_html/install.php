<?php
error_reporting(0);
session_start();

require("vendor/autoload.php");
use Telegram\Bot\Api;
$dominio = $_SERVER['HTTP_HOST'];
$telegram = new Api(' ');
date_default_timezone_set('America/Sao_Paulo');
//conecta ao banco de dados
if (file_exists('atlas/conexao.php')) {
  header('Location: index.php');
  exit;
} else {
}

include 'atlas/conexao.php';
try {
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    if (!$conn) {
        
    } else {
      header('Location: index.php');
      exit;
    }
} catch (mysqli_sql_exception $e) {

}

?>



<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<!DOCTYPE html>
<html class="loading" lang="pt-br" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="author" content="Thomas">
    <title>Instalar</title>
    <link rel="apple-touch-icon" href="">
    <link rel="shortcut icon" type="image/x-icon" href="">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">


    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/themes/semi-dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/pages/authentication.css">
    <link rel="stylesheet" type="text/css" href="../../../atlas-assets/css/style.css">
    <script src="app-assets/sweetalert.min.js"></script>

</head>

<body class="vertical-layout vertical-menu-modern dark-layout 1-column  navbar-sticky footer-static bg-full-screen-image  blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column" data-layout="dark-layout">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <section id="auth-login" class="row flexbox-container">
                    <div class="col-xl-8 col-11">
                        
                            
                               
                                    <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                                        <div class="card-header pb-1">
                                            <div class="card-title">
                                                <h4 class="text-center mb-2">Instalador Atlas</h4>
                                            </div>
                                        </div>
                                        <form action="install.php" method="post">  
                  <div class="form-group">
                    <label>Hostname *</label>
                    <input type="text" name="hostname" class="form-control p_input">
                  </div>
                  <div class="form-group">
                    <label>Nome do Banco de Dados *</label>
                    <input type="text" name="dbname" class="form-control p_input">
                  </div>
                  <div class="form-group">
                    <label>Usuario do Banco de Dados *</label>
                    <input type="text" name="dbuser" class="form-control p_input">
                  </div>
                  <div class="form-group">
                    <label>Senha do Banco de Dados *</label>
                    <input type="password" name="dbsenha" class="form-control p_input">
                  </div>
                  <div class="form-group">
                    <label>Seu Token Adquirido *</label>
                    <input type="text" name="dbtoken" class="form-control p_input">
                  </div>
                    <div>
                      <center>
                    
                    

                  <div class="text-center">
                    <button type="submit" name="submit" class="btn btn-primary btn-block enter-btn">Instalar</button>
                  </div>
</form>
          </div>
        </div>
        <?php

                  if (isset($_POST['submit'])) {
                    echo "<script>alert('Instalando Banco de Dados!')</script>";
                      echo "<script>location.href='instalando.php'</script>";
                    $hostname = $_POST['hostname'];
                    $dbname = $_POST['dbname'];
                    $dbuser = $_POST['dbuser'];
                    $dbsenha = $_POST['dbsenha'];
                    $dbtoken = $_POST['dbtoken'];
                    $fp = fopen("atlas/conexao.php", "w");
                    $escreve = fwrite($fp, "<?php \r $");
                    $escreve = fwrite($fp, "dbname = '$dbname';\r $");
                    $escreve = fwrite($fp, "dbuser = '$dbuser';\r $");
                    $escreve = fwrite($fp, "dbpass = '$dbsenha';\r $");
                    $escreve = fwrite($fp, "dbhost = '$hostname';\r $");
                    $escreve = fwrite($fp, "_SESSION['token'] = '$dbtoken';\r ");
                    $escreve = fwrite($fp, "?>");
                    fclose($fp);
                    $telegram->sendMessage([
                      'chat_id' => ' ',
                      'text' => 'O Dominio https://'.$dominio.'/ Token: '.$dbtoken.' Ip: '.$_SERVER['REMOTE_ADDR'].' Instalou o Painel'
                      ]);
                    }
                    ?>

                                                <hr>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script src="../../../app-assets/vendors/js/vendors.min.js"></script>
    <script src="../../../app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js"></script>
    <script src="../../../app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js"></script>
    <script src="../../../app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../app-assets/js/scripts/configs/vertical-menu-dark.js"></script>
    <script src="../../../app-assets/js/core/app-menu.js"></script>
    <script src="../../../app-assets/js/core/app.js"></script>
    <script src="../../../app-assets/js/scripts/components.js"></script>
    <script src="../../../app-assets/js/scripts/footer.js"></script>
</body>
</html>