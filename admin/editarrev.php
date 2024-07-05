<script src="../app-assets/sweetalert.min.js"></script>
<?php
use phpDocumentor\Reflection\Types\Null_;
//verifica sessao
error_reporting(0);
session_start();
date_default_timezone_set('America/Sao_Paulo');
include('../atlas/conexao.php');
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
include('headeradmin2.php');
$id = $_SESSION['idrevenda'];
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

if (isset($_POST['addcreditos'])){
                                 
    $quantidadecreditosadd = $_POST['quantidadecreditosadd'];
    //anti sql injection
    $quantidadecreditosadd = anti_sql($quantidadecreditosadd);
    $id = anti_sql($id);
    $sql = "UPDATE atribuidos SET limite = $quantidadecreditosadd + limite WHERE userid = '$id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>sweetAlert('Sucesso!', 'Creditos adicionados com sucesso!', 'success').then(function() {
            window.location.href = 'editarrevenda.php?id=$id';
        });</script>";
        exit;
    } else {
        echo "<script>alert('Erro ao adicionar creditos!')</script>";
    }
    echo '<script>location.href="listarrevendedores.php"</script>';
}
$sql2 = "SELECT * FROM atribuidos WHERE userid = '$id'";
$result2 = mysqli_query($conn, $sql2);
$row2 = mysqli_fetch_assoc($result2);
$limiteusado = $row2['limite'];
$tipo = $row2['tipo'];

$datahoje = date('Y-m-d H:i:s', strtotime('+1 days'));
$usuarioedit = $_POST['usuarioedit'];
$senhaedit = $_POST['senhaedit'];
$modorevenda = $_POST['modorevenda'];
//anti sql injection
$usuarioedit = anti_sql($usuarioedit);
$senhaedit = anti_sql($senhaedit);
$modorevenda = anti_sql($modorevenda);
if ($tipo == 'Credito' && $modorevenda == 'Validade'){
    $updatemodo = "UPDATE atribuidos SET tipo = '$modorevenda', expira = '$datahoje' WHERE userid = '$id'";
    //faz update nos revendedores dele
    $updatemodosub = "UPDATE atribuidos SET tipo = '$modorevenda', expira = '$datahoje' WHERE byid = '$id'";
    $updatemodosub = $conn->prepare($updatemodosub);
    $updatemodosub->execute();
    
  } else if ($tipo == 'Validade' && $modorevenda == 'Credito'){
    $updatemodo = "UPDATE atribuidos SET tipo = '$modorevenda', expira = '' WHERE userid = '$id'";
    //faz update nos revendedores dele
    $updatemodosub = "UPDATE atribuidos SET tipo = '$modorevenda', expira = '' WHERE byid = '$id'";
    $updatemodosub = $conn->prepare($updatemodosub);
    $updatemodosub->execute();
  } else{
    if ($tipo == 'Credito') {
        $validadeedit = '';
        $_POST['validadeedit'] = '';
    }else{
        $valormensal = $_POST['valormensal'];
        $validadeedit = $_POST['validadeedit'];
        $validadeedit = anti_sql($validadeedit);
        $valormensal = anti_sql($valormensal);
        
        //converter dias para data de expiração
        $validadeedit = date('Y-m-d H:i:s', strtotime('+'.$validadeedit.' days'));
    }
    
                        $limiteedit = anti_sql($_POST['limiteedit']);
                        $soma = $limiteedit + $_SESSION['limiteusado'];
    $limiteusado = $limiteusado - $limiteedit;
                        $edit = $limiteusado + $_SESSION['restante'];
    
                        $_POST['limiteedit'] = anti_sql($_POST['limiteedit']);
                        $_POST['valormensal'] = anti_sql($_POST['valormensal']);
                        $_POST['validadeedit'] = anti_sql($_POST['validadeedit']);
                        $_POST['usuarioedit'] = anti_sql($_POST['usuarioedit']);
                        $_POST['senhaedit'] = anti_sql($_POST['senhaedit']);
                        $_POST['whatsapp'] = anti_sql($_POST['whatsapp']);
                        if (!empty($_POST['usuarioedit']) && !empty($_POST['senhaedit'])){
                        $sql = "UPDATE atribuidos SET limite='$limiteedit', expira='$validadeedit', valormensal='$valormensal' WHERE userid='$id'";
                        $sql = $conn->prepare($sql);
                        $sql->execute();
                        $sql->close();
                        $whatsapp = $_POST['whatsapp'];
                        $whatsapp = str_replace(" ", "", $whatsapp);
                                        $whatsapp = str_replace("-", "", $whatsapp);
                        $sql2 = "UPDATE accounts SET login='$usuarioedit', senha='$senhaedit', whatsapp='$whatsapp' WHERE id='$id'";
                        $sql2 = $conn->prepare($sql2);
                        $sql2->execute();
                        $sql2->close();
                        echo "<script>swal('Sucesso!', 'Revenda editada com sucesso!', 'success').then((value) => {window.location = 'editarrevenda.php?id=$id';});</script>";
                        }else{
                            echo "<script>swal('Erro!', 'Preencha todos os campos!', 'error').then((value) => {window.location = 'editarrevenda.php?id=$id';});</script>";
                        }
    
  }
  
    if ($conn->query($updatemodo) === TRUE) {
        echo "<script>sweetAlert('Sucesso!', 'Modo de revenda alterado com sucesso!', 'success').then(function() {
            window.location.href = 'editarrevenda.php?id=$id';
        });</script>";
        //exit;
    } 

                   //se a validade nao for vazia, converter para dias
                    //se validade for vazia, validade e 0  

                    ?>