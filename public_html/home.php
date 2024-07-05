<script src="app-assets/sweetalert.min.js"></script>
   <?php

    include_once("atlas/conexao.php");
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "SELECT * FROM configs";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $nomepainel = $row['nomepainel'];
        $logo = $row['logo'];
        $icon = $row['icon'];
        $csspersonali = $row["corfundologo"];

    }

error_reporting(0);
session_start();
// Verifica e atualiza a atividade da sessão
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1200)) {
  echo "<script>alert('Sessão expirada por inatividade!');</script>";
  session_unset();
  session_destroy(); 
  echo "<script>setTimeout(function(){ window.location.href='../index.php'; }, 500);</script>";
  exit();
}

$_SESSION['last_activity'] = time();
if(!isset($_SESSION['login'])){
  header('Location: index.php');
  exit();
}
//se o login for admin, redireciona para o painel
if($_SESSION['login'] == 'admin'){
  header('Location: admin/home.php');
  exit();
}

$token = $_SESSION['token'];
$dominio = $_SERVER['HTTP_HOST'];
$sql1 = "SELECT * FROM atribuidos WHERE userid = '".$_SESSION['iduser']."'";
$result1 = mysqli_query($conn, $sql1);
//pegar vencimento da conta
while ($row1 = mysqli_fetch_assoc($result1)) {
    $vencimento = $row1['expira'];
    $vencimento = date('d/m/Y', strtotime($vencimento));
    $vencimentocheck = $row1['expira'];
    $_SESSION['expira'] = $vencimento;
    $_SESSION['limite'] = $row1['limite'];
    $_SESSION['tipo'] = $row1['tipo'];
    $suspenso = $row1['suspenso'];
    $tipo = $row1['tipo'];
    $_SESSION['byid'] = $row1['byid'];
  }
  if ($_SESSION['tipo'] == 'Credito') {
    $_SESSION['tipo'] = 'Seus Créditos';
    $_SESSION['expira'] = 'Nunca';
  }else{
    $_SESSION['tipo'] = 'Seu Limite';
    if ($_SESSION['byid'] == '1') {
      
    }else{
    $sql_suspenso = "SELECT * FROM atribuidos WHERE userid = '".$_SESSION['byid']."'";
    $result_suspenso = mysqli_query($conn, $sql_suspenso);
    while ($row_suspenso = mysqli_fetch_assoc($result_suspenso)) {
      $dataadmin = $row_suspenso['expira'];
    }
    if ($dataadmin < date('Y-m-d H:i:s')) {
      echo "<script>alert('Suspenso, entre em contato com o suporte para mais informações!')</script>";
      echo "<script>window.location.href = 'logout.php';</script>";
    }
  }

  }
if ($suspenso == '1') {
  echo "<script>alert('Suspenso, entre em contato com o suporte para mais informações!')</script>";
  echo "<script>window.location.href = 'logout.php';</script>";
  exit();
}  
 


  

$tokenvb = "SELECT * FROM accounts WHERE id = '".$_SESSION['iduser']."'";
$resultvb = mysqli_query($conn, $tokenvb);
$rowvb = mysqli_fetch_assoc($resultvb);
$tokenvenda = $rowvb['tokenvenda'];
$accesstoken = $rowvb['accesstoken'];
$acesstokenpaghiper = $rowvb['acesstokenpaghiper'];

//se estiver com menos de 2 dias para expirar

$sql2 = "SELECT * FROM atribuidos WHERE byid = '".$_SESSION['iduser']."'";
//contar quantos atribuidos tem
$result2 = mysqli_query($conn, $sql2);
$totalrevenda = mysqli_num_rows($result2);

$sql3 = "SELECT * FROM ssh_accounts WHERE byid = '".$_SESSION['iduser']."'";
//contar quantos ssh tem
$result3 = mysqli_query($conn, $sql3);
$totalusuarios = mysqli_num_rows($result3);

$slq3 = "SELECT sum(valor) AS valor  FROM pagamentos where byid='".$_SESSION['iduser']."' and status='Aprovado'";
$result3 = mysqli_query($conn, $slq3);
$row3 = mysqli_fetch_assoc($result3);
$totalvendido = $row3['valor'];
$totalvendido = number_format($totalvendido, 2, ',', '.');


date_default_timezone_set('America/Sao_Paulo');
$data = date('d/m/Y');
if(isset($_SESSION['login']) === 'admin'){
  header('location: ../admin/home.php');
}

//consulta todos pagamentos de hoje d/m/y h:m:s
$slq4 = "SELECT sum(valor) AS valor  FROM pagamentos where byid='".$_SESSION['iduser']."' and status='Aprovado' and data >= '$data 00:00:00' and data <= '$data 23:59:59'";
$result4 = mysqli_query($conn, $slq4);
$row4 = mysqli_fetch_assoc($result4);
$totalvendidohoje = $row4['valor'];
/* if ($totalvendidohoje == null) {
  $totalvendidohoje = 0;
} */
//consulta usuarios expirados
date_default_timezone_set('America/Sao_Paulo');
$data = date('Y-m-d H:i:s');
$slq5 = "SELECT * FROM ssh_accounts WHERE byid = '".$_SESSION['iduser']."' and expira < '$data'";
$result5 = mysqli_query($conn, $slq5);
$totalvencidos = mysqli_num_rows($result5);
//comparar usuarios de 2 tabelas
$sqlRevendedores = "SELECT * FROM accounts WHERE byid = '".$_SESSION['iduser']."'";
$resultRevendedores = mysqli_query($conn, $sqlRevendedores);

// Array para armazenar os IDs dos revendedores
$revendedoresIDs = array();

// Percorra os resultados dos revendedores e armazene os IDs
while ($rowRevendedor = mysqli_fetch_assoc($resultRevendedores)) {
    $revendedoresIDs[] = $rowRevendedor['id'];
}

// Verifique se há revendedores antes de executar a consulta
if (!empty($revendedoresIDs)) {
    // Consulta para buscar todos os online dos revendedores
    $sqlOnlineRevendedores = "SELECT * FROM ssh_accounts WHERE status = 'Online' AND byid IN (".implode(",", $revendedoresIDs).")";
    $resultOnlineRevendedores = mysqli_query($conn, $sqlOnlineRevendedores);

    // Obtenha o número total de contas online dos revendedores
    $totalOnlineRevendedores = mysqli_num_rows($resultOnlineRevendedores);
} else {
    $totalOnlineRevendedores = 0; // Define como zero se não houver revendedores
}

$sql16 = "SELECT * FROM ssh_accounts WHERE status = 'Online' and byid = '".$_SESSION['iduser']."'";
$result16 = mysqli_query($conn, $sql16);
$totalonline = mysqli_num_rows($result16);
$seusonlines = $totalonline;




if ($totalonline == null) {
  $totalonline = 0;
}
if ($totalvendido == null) {
  $totalvendido = 0;
}

$sql_logs = "SELECT * FROM logs WHERE byid = '".$_SESSION['iduser']."'";
$result_logs = mysqli_query($conn, $sql_logs);
$total_logs = mysqli_num_rows($result_logs);


?>
<?php
$token = $_SESSION['token'];
$dominio = $_SERVER['HTTP_HOST'];

if(isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)){
  session_unset();
  session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();
//consulta se o token e o dominio estão corretos

$slq2 = "SELECT sum(limite) AS numusuarios  FROM ssh_accounts where byid='".$_SESSION['iduser']."' ";
$result = $conn->prepare($slq2);
$result->execute();
$result->bind_result($numusuarios);
$result->fetch();
$result->close();

$slq2 = "SELECT sum(limite) AS limiteusado  FROM atribuidos where byid='".$_SESSION['iduser']."' ";
$result = $conn->prepare($slq2);
$result->execute();
$result->bind_result($limiteusado);
$result->fetch();
$result->close();

$somalimite = $numusuarios + $limiteusado;

$restante = $_SESSION['limite'] - $somalimite;
?>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title><?php echo $nomepainel; ?> - Painel Administrativo</title>
    <link rel="apple-touch-icon" href="<?php echo $icon; ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $icon; ?>">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/charts/apexcharts.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/extensions/dragula.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/themes/semi-dark-layout.css">
    <!-- END: Theme CSS-->

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css/pages/dashboard-analytics.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../../../atlas-assets/css/style.css">
    <!-- END: Custom CSS-->

</head>
<style>
        <?php echo $csspersonali; ?>
    </style>
    
<body class="vertical-layout vertical-menu-modern dark-layout 2-columns  navbar-sticky footer-static  " data-open="click" data-menu="vertical-menu-modern" data-col="2-columns" data-layout="dark-layout" >
<style>
  .back-button {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #007bff;
  color: #fff;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  z-index: 9999;
  text-decoration: none;
}

.arrow {
  border: solid white;
  border-width: 0 3px 3px 0;
  display: inline-block;
  padding: 3px;
  transform: rotate(135deg);
  -webkit-transform: rotate(135deg);
}
</style>
<?php if (isset($_SESSION['admin564154156'])) { ?>
<form method="post" action="home.php">
  <button type="submit" name="voltaradmin" class="back-button btn btn-outline-primary">
    <span class="arrow"></span>
  </button>
</form>
<?php } ?>

<?php 
//se voltaadmin e admin564154156 existir
if (isset($_POST['voltaradmin']) && isset($_SESSION['admin564154156'])) {
    $sqladmin = "SELECT * FROM accounts WHERE id = '1'";
    $resultadmin = $conn->query($sqladmin);
    $rowadmin = $resultadmin->fetch_assoc();
       //destrói as sessões existentes
       $_SESSION['login'] = $rowadmin['login'];
       $_SESSION['senha'] = $rowadmin['senha'];
       $_SESSION['iduser'] = $rowadmin['id']; 
       echo "<script>window.location.href='admin/home.php';</script>";
} ?>

    <!-- BEGIN: Header-->
    <div class="header-navbar-shadow" id="inicialeditor"></div>
    <nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top navbar-dark">
        <div class="navbar-wrapper">
            <div class="navbar-container content">
                <div class="navbar-collapse" id="navbar-mobile">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        <ul class="nav navbar-nav">
                            <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon bx bx-menu"></i></a></li>
                        </ul>
                       
                    </div>
                    <li class="nav-item dropdown d-none d-lg-block">
                      <!-- botao para voltar pro admin -->
                      

                <a class="btn btn-outline-success" href="atlas/criarteste.php">+ Teste Rapido</a>
              </li>
                    <ul class="nav navbar-nav float-right">
                       
                        
                        
                             
                        </li>
                        <li class="dropdown dropdown-user nav-item"><a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                <div class="user-nav d-sm-flex d-none"><span class="user-name"><?php echo $_SESSION['login'] ?></span></div><span><div class="avatar bg-success mr-1">
                                            <div class="avatar-content">
                                            <?php
                                            $nome = $_SESSION['login'];
                                            $primeira_letra = $nome[0];
                                            echo $primeira_letra;
                                            ?>
                                            </div>
                                        </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right pb-0"><a class="dropdown-item" href="atlas/editconta.php"><i class="bx bx-user mr-50"></i> Conta</a>
                                <div class="dropdown-divider mb-0"></div><a class="dropdown-item" href="../logout.php"><i class="bx bx-power-off mr-50"></i> Sair</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <br>
    <!-- END: Header-->


    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-dark menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto"><a class="navbar-brand" href="../home.php">
                <style>
                    .logo {
                      width: 170px;

                    }
                  </style>
                  <center>
                        <img class="logo" src="<?php echo $logo; ?>" /></center>
                        <!-- <h2 class="brand-text mb-0"><img class="logo" src="<?php echo $logo; ?>" /></h2> -->
                    </a></li>
                <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i><i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary" data-ticon="bx-disc"></i></a></li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">
                <li class=" nav-item"><a href="../home.php"><i class="menu-livicon" data-icon="desktop"></i><span class="menu-title" data-i18n="Dashboard">Pagina Inicial</span></a>

                </li>
                <li class=" navigation-header"><span>Usuarios</span>
                </li>
                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="user"></i><span class="menu-title">Gerenciar Usuarios</span></a>
                <ul class="menu-content">
                        <li><a href="atlas/criarusuario.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Criar Usuario</span></a>
                        </li>
                        <li><a href="atlas/criarteste.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Criar Teste</span></a>
                        </li>
                        <li><a href="atlas/listarusuarios.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Lista de Usuarios</span></a>
                        </li>
                        <li><a href="atlas/listaexpirados.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Lista de Expirados</span></a>
                        </li>
                        <li><a href="atlas/onlines.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Lista de Onlines</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="users"></i><span class="menu-title">Revendedores</span></a>
                    <ul class="menu-content">
                        <li><a href="atlas/criarrevenda.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" >Criar Revenda</span></a>
                        </li>
                        <li><a href="atlas/listarrevendedores.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Listar Revendedores</span></a>
                        </li>
                    </ul>
                </li>
                <li class=" navigation-header"><span>Pagamentos</span>
                </li>
                <li class=" nav-item"><a href="#"><i class="menu-livicon" data-icon="us-dollar"></i><span class="menu-title">Pagamentos</span></a>
                <ul class="menu-content">
                    <li><a href="atlas/formaspag.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item">Configurar Pagamentos</span></a>
                </li>
                <li><a href="atlas/listadepag.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Typography">Listar Seus Pagamentos</span></a>
            </li>
            <li><a href="atlas/cupons.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Syntax Highlighter">Cupom de Desconto</span></a>
        </li>
        <li><a href="atlas/pagamento.php"><i class="bx bx-right-arrow-alt"></i><span class="menu-item" data-i18n="Text Utilities">Pagamento</span></a>
    </li>

</ul>
</li>
<li class=" navigation-header"><span>Whatsapp</span>
</li>
                <li class=" nav-item"><a href="atlas/whatsconect.php"><i class="menu-livicon" data-icon="bell"></i><span class="menu-title">WhatsApp</span></a>
                </li>
<li class=" navigation-header"><span>Logs</span>
                </li>
                <li class=" nav-item"><a href="atlas/logs.php"><i class="menu-livicon" data-icon="priority-low"></i><span class="menu-title">Logs</span></a>
                </li>
                <li class=" navigation-header"><span>Configurações</span>
                <li class=" nav-item"><a href="atlas/editconta.php"><i class="menu-livicon" data-icon="wrench"></i><span class="menu-title">Conta</span></a>
                </li>
                <li class=" nav-item"><a href="../logout.php"><i class="menu-livicon" data-icon="morph-login2"></i><span class="menu-title" data-i18n="Form Validation">Sair</span></a>
                </li>
                
            </ul>
        </div>
    </div>

     <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- Dashboard Ecommerce Starts -->
        
            <div class="row">
              <div class="col-12 grid-margin stretch-card">
                
              </div>
            </div>
            <div class="row">
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card" onclick="window.location='atlas/onlines.php';">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0"><?php echo $totalOnlineRevendedores ?>/<?php echo $seusonlines ?></h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">Onlines</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success ">
                          <a href="onlines.php" class="mdi mdi-arrow-top-right icon-item"></a>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Revendedores / Meus Onlines</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card" onclick="window.location='atlas/listarrevendedores.php';">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0"><?php echo $totalrevenda ?></h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">Revendedores</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <a href="atlas/listarrevendedores.php" class="mdi mdi-arrow-top-right icon-item"></a>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total de Revendedores</h6>
                  </div>
                </div>
              </div>
          
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card" onclick="window.location='atlas/listarusuarios.php';">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0"><?php echo $totalusuarios ?></h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">Usuarios</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <a href="listarrevendedores.php" class="mdi mdi-arrow-top-right icon-item"></a>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total de Usuarios</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card" >
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0"><?php echo $totalvendido ?></h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">R$</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total de Vendas</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0"><?php echo $_SESSION['expira'] ?></h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium"></p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <a href="listarusuarios.php" class="mdi mdi-arrow-top-right icon-item"></a>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Vencimento</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card" onclick="window.location='atlas/listaexpirados.php';">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0"><?php echo $totalvencidos ?></h3>  
                          <p class="text-success ml-2 mb-0 font-weight-medium">Usuarios</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total de Vencidos</h6>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0"><?php echo $_SESSION['limite'] ?></h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">Seu Limite</p>   
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <a href="listarrevendedores.php" class="mdi mdi-arrow-top-right icon-item"></a>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Seu Limite Atual
                  </div>
                </div>
              </div>
              <?php
              if ($_SESSION['tipo'] == 'Seu Limite') {
                echo '<div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0">'.$restante.'</h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">Restante</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Limite Restante</h6>
                  </div>
                </div>
              </div>
              ';
              } else {
                echo '<div class="col-xl-3 col-sm-6 grid-margin stretch-card">
                <div class="card" >
                  <div class="card-body">
                    <div class="row">
                      <div class="col-9">
                        <div class="d-flex align-items-center align-self-start">
                          <h3 class="mb-0">'.$total_logs.'</h3>
                          <p class="text-success ml-2 mb-0 font-weight-medium">Logs</p>
                        </div>
                      </div>
                      <div class="col-3">
                        <div class="icon icon-box-success">
                          <span class="mdi mdi-arrow-top-right icon-item"></span>
                        </div>
                      </div>
                    </div>
                    <h6 class="text-muted font-weight-normal">Total de Logs</h6>
                  </div>
                </div>
                </div>
              </div>';
              
              }
              ?>
              
              <?php if ($accesstoken != '' || $acesstokenpaghiper != '') { ?>
              <div class="content-body" style="width: 100%; margin: 0 auto;">
                    <section id="divider-colors">
                            <div class="col-12">
                            <div class="card"style="border: 2px solid #5A8DEF;">
                                <div class="card-header">
                                    <h4 class="card-title">Link de Compra</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <p>
                                            Use esses Links para seus clientes comprarem seus produtos.
                                        </p>
                                        <div class="divider divider-primary">
                                            <div class="divider-text">Para Novos Revendedores</div>
                                            <input type="text" class="form-control" value="https://<?php echo $_SERVER['HTTP_HOST'];  ?>/revenda.php?token=<?php echo $tokenvenda; ?>" readonly>
                                        </div>
                                        <div class="divider divider-warning">
                                            <div class="divider-text">Link Bot Vendas</div>
                                            <input type="text" class="form-control" value="https://<?php echo $_SERVER['HTTP_HOST'];  ?>/comprar.php?token=<?php echo $tokenvenda; ?>" readonly>
                                        </div>
                                        <div class="divider divider-primary">
                                            <div class="divider-text">Link Teste Automatico</div>
                                            <input type="text" class="form-control" value="https://<?php echo $_SERVER['HTTP_HOST'];  ?>/criarteste.php?token=<?php echo $tokenvenda; ?>" readonly>
                                        </div>
                                        <form action="home.php" method="post">
                                        <div class="divider divider-warning">
                                            <button class="btn btn-warning" type="submit" name="gerarlink" id="gerarlink">Gerar Novo Link</button>
                                        
                                        </form>

                                        </div>
                                        <?php
                                        if(isset($_POST['gerarlink'])){
                                            $codigo = rand(100000000000,999999999999);
                                            $id = $_SESSION['iduser'];
                                            $sql = "UPDATE accounts SET tokenvenda = '$codigo' WHERE id = '$id'";
                                            $result = $conn -> query($sql);
                                            echo "<meta http-equiv='refresh' content='0'>";
                                        }
                                        ?>
                                </div>
                            </div>
                        </div>
                        </div>
                      </section>
                    
                <!-- Divider Colors Ends -->
            </div>
        </div>
    
    
        <?php } ?>
        



        

                    
                        
</div>
                    <div class="content-body">
                        
                <!-- table Transactions start -->
                <section id="table-transactions">
                    <div class="card">
                        <div class="card-header">
                            <!-- head -->
                            <h5 class="card-title">Pagamentos</h5>
                            <!-- Single Date Picker and button -->
                            <div class="heading-elements">
                                <ul class="list-inline mb-0">
                                    <input type="text" class="form-control" placeholder="Pesquisar" aria-label="Pesquisar" aria-describedby="button-addon2" id="pesquisar" onkeyup="pesquisar()">
                                </ul>
                            </div>

                            


                        </div>
                        <!-- datatable start -->
                                <?php
 $sql = "SELECT * FROM pagamentos  where byid = '".$_SESSION['iduser']."' ";
          $result = $conn -> query($sql);
?>
                        <div class="table-responsive">
                            <table id="table-extended-transactions" class="table mb-0">
                                <thead>
                                    <tr>
                                        <th> Login </th>
                            <th> Id do Pagamento </th>
                            <th> Valor </th>
                            <th> Detalhes </th>
                            <th> Data e Hora </th>
                            <th> Status </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                          //result e result2
                          while ($user_data = mysqli_fetch_assoc($result)){
                          //converter expira para data
                          if($user_data['status'] == 'Aprovado'){
                            $status = "<label class='badge badge-success'>Aprovado</label>";
                            }else{
                            $status = "<label class='badge badge-danger'>Pendente</label>";
                            }
                          
                            
                          
                            echo "<td>".$user_data['login']."</td>";
                            echo "<td>".$user_data['idpagamento']."</td>";
                            echo "<td>".$user_data['valor']."</td>";
                            echo "<td>".$user_data['texto']."</td>";
                            echo "<td>".$user_data['data']."</td>";
                            echo "<td>".$status."</td>";
                            
                            echo "</tr>";
                          }
                          
                          ?>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                </div>
                
            </div>
        </div>
    </div>

            </div>
        </div>
    </div>
    <!-- BEGIN: Vendor JS-->
    <script src="../../../app-assets/vendors/js/vendors.min.js"></script>
    <script src="../../../app-assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.js"></script>
    <script src="../../../app-assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.js"></script>
    <script src="../../../app-assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="../../../app-assets/vendors/js/charts/apexcharts.min.js"></script>
    <script src="../../../app-assets/vendors/js/extensions/swiper.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="../../../app-assets/js/scripts/configs/vertical-menu-dark.js"></script>
    <script src="../../../app-assets/js/core/app-menu.js"></script>
    <script src="../../../app-assets/js/core/app.js"></script>
    <script src="../../../app-assets/js/scripts/components.js"></script>
    <script src="../../../app-assets/js/scripts/footer.js"></script>
    <!-- END: Theme JS-->
    <script>
                             $(document).ready(function(){
                            $("#pesquisar").on("keyup", function() {
                            var value = $(this).val().toLowerCase();
                            $("#table-extended-transactions tr").filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                            });
                            });
                            });
                                                    </script>
    <!-- BEGIN: Page JS-->
    <script src="../../../app-assets/js/scripts/pages/dashboard-ecommerce.js"></script>
    <!-- END: Page JS-->

</body>
<!-- END: Body-->

</html>
<script>
setInterval(() => {
  fetch('admin/suspenderauto.php', {
    method: 'POST',
  })
    .then(response => {
      // Tratar a resposta, se necessário
    })
    .catch(error => {
      // Tratar o erro, se necessário
    });
}, 10000); // 10000 milissegundos = 10 segundos
</script>
<?php
        $sql = "SELECT * FROM configs";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
                $csspersonali = $row["corfundologo"];
                $textopersonali = $row["textoedit"];
        }
    
    // Recupere o conteúdo da variável $tradutor e converta-o para um array em PHP
    $tradutor = $textopersonali;
    $linhas = explode("\n", $tradutor);
    $substituicoes = array();
    foreach ($linhas as $linha) {
        $par = explode("=", $linha);
        if (count($par) === 2) {
            $textoOriginal = trim($par[0]);
            $textoSubstituto = trim($par[1]);
            $substituicoes[] = array('original' => $textoOriginal, 'substituto' => $textoSubstituto);
        }
    }
    ?>
<script>
window.addEventListener('DOMContentLoaded', function() {
        // Define as substituições desejadas
        var substituicoes = <?php echo json_encode($substituicoes); ?>;

        // Recursivamente percorre os elementos e substitui o texto dentro deles
        function percorrerElementos(elemento) {
            if (elemento.nodeType === Node.TEXT_NODE) {
                substituicoes.forEach(function(substituicao) {
                    elemento.textContent = elemento.textContent.replace(substituicao.original, substituicao.substituto);
                });
            } else {
                for (var i = 0; i < elemento.childNodes.length; i++) {
                    percorrerElementos(elemento.childNodes[i]);
                }
            }
        }

        // Obtém o elemento pai dos elementos onde deseja aplicar as substituições
        var paiElemento = document.getElementById('inicialeditor').parentNode;

        // Percorre os elementos dentro do pai e realiza as substituições
        percorrerElementos(paiElemento);
});
</script>
