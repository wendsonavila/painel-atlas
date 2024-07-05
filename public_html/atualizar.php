<?php
    
    session_start();
    error_reporting(0);
    include('atlas/conexao.php');
ini_set('memory_limit', '-1');

//se senha nao existir
if (!isset($_SESSION['senhaatualizar'])) {
    header('Location: index.php');
    exit;
}else{
    if ($_POST['versao'] == 'ultima') {
        $url = 'https://painel.wrssh.online/atualizacao3.zip';
    }
    $zip = file_get_contents($url);
    file_put_contents('atualizacao3.zip', $zip);

    $zip = new ZipArchive;
    $res = $zip->open('atualizacao3.zip');
     if ($res === TRUE) {
        //extrair no diretorio atual
        $zip->extractTo('./');
      $zip->close();
    } else {
        echo 'failed';
    }
    unlink('atualizacao3.zip'); 
}
echo 'Atualizado com sucesso!';


?>
   
