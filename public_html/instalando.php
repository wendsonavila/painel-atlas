<?php
session_start();
include('atlas/conexao.php');
try {
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
} catch (mysqli_sql_exception $e) {
    echo "<script>window.location.href = 'install.php';</script>";
}
//criar tabelas
$sql = "CREATE TABLE IF NOT EXISTS `accounts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(255) DEFAULT NULL,
    `contato` varchar(255) DEFAULT NULL,
    `login` varchar(50) NOT NULL DEFAULT '0',
    `token` varchar(330) NOT NULL DEFAULT '0',
    `mb` varchar(50) NOT NULL DEFAULT '0',
    `senha` varchar(50) NOT NULL DEFAULT '0',
    `byid` varchar(50) NOT NULL DEFAULT '0',
    `mainid` varchar(50) NOT NULL DEFAULT '0',
    `accesstoken` text DEFAULT NULL,
    `valorusuario` varchar(50) DEFAULT NULL,
    `valorrevenda` varchar(50) DEFAULT NULL,
    `idtelegram` text DEFAULT NULL,
    `tempo` text DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";

$result = mysqli_query($conn, $sql);

//adicionar a conta admin
$sql2 = "INSERT INTO `accounts` (`id`, `nome`, `contato`, `login`, `token`, `mb`, `senha`, `byid`, `mainid`, `accesstoken`, `valorusuario`, `valorrevenda`) VALUES
(1, 'admin', 'admin', 'admin', 'admin', '60', '12345', '0', '0', NULL, '0', '0');";
$result2 = mysqli_query($conn, $sql2);

$sql44 = "CREATE TABLE IF NOT EXISTS `atlasdeviceid` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome_user` varchar(255) DEFAULT NULL,
    `deviceid` varchar(255) DEFAULT NULL,
    `byid` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$result44 = mysqli_query($conn, $sql44);

$sql3 = "CREATE TABLE IF NOT EXISTS `atribuidos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `valor` varchar(255) DEFAULT NULL,
    `categoriaid` int(11) NOT NULL DEFAULT 0,
    `userid` int(11) NOT NULL DEFAULT 0,
    `byid` int(11) NOT NULL DEFAULT 0,
    `limite` int(11) NOT NULL DEFAULT 0,
    `limitetest` int(11) DEFAULT NULL,
    `tipo` text NOT NULL,
    `expira` text DEFAULT NULL,
    `subrev` int(11) NOT NULL DEFAULT 0,
    `suspenso` int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
    ";
    $result3 = mysqli_query($conn, $sql3);

$sql4 = "CREATE TABLE IF NOT EXISTS `categorias` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `subid` int(11) DEFAULT NULL,
    `nome` varchar(150) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
    $result4 = mysqli_query($conn, $sql4);

$sql55 = "CREATE TABLE IF NOT EXISTS `configs` (
    `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
    `nomepainel` text DEFAULT NULL,
    `logo` text DEFAULT NULL,
    `icon` text DEFAULT NULL,
    `corborder` text DEFAULT NULL,
    `corletranav` text DEFAULT NULL,
    `deviceativo` text DEFAULT NULL,
    `imglogin` text DEFAULT NULL,
    `corbarranav` text DEFAULT NULL,
    `corfundologo` text DEFAULT NULL,
    `corcard` text DEFAULT NULL,
    `cortextcard` text DEFAULT NULL,
    `cornavsuperior` text DEFAULT NULL,
    `minimocompra` text DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  ";
    $result55 = mysqli_query($conn, $sql55);

$sql45 = "CREATE TABLE IF NOT EXISTS `cupons` (
    `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
    `nome` varchar(30) NOT NULL,
    `cupom` varchar(50) NOT NULL,
    `desconto` varchar(50) NOT NULL,
    `usado` varchar(50) NOT NULL,
    `byid` varchar(50) NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
  ";
    $result45 = mysqli_query($conn, $sql45);



    $sql22 = "INSERT INTO `configs` (`id`, `nomepainel`, `logo`, `icon`) VALUES
    (1, 'Atlas Painel', 'https://cdn.discordapp.com/attachments/1051302877987086437/1070581060821340250/logo.png?ex=65e954cf&is=65d6dfcf&hm=3a1ac71b2bfd8ebe60bb1be2c7f5ff932792db6d3302df6dd24ada53b5bce024&', 'https://cdn.discordapp.com/attachments/1051302877987086437/1070581061014274088/logo-mini.png?ex=65e954cf&is=65d6dfcf&hm=0101e733116e025664cd6d0baa1c0b29e31590bd4e465b8b0e1b74a90351bb12&');";
    $result22 = mysqli_query($conn, $sql22);

$sql5 = "CREATE TABLE IF NOT EXISTS `logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `userid` int(11) DEFAULT 0,
    `texto` text DEFAULT NULL,
    `validade` text DEFAULT NULL,
    `revenda` text DEFAULT NULL,
    `byid` text DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
    $result5 = mysqli_query($conn, $sql5);

$sql98 = "CREATE TABLE IF NOT EXISTS `onlines` (
    `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
    `usuario` text NOT NULL,
    `quantidade` text NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    $result98 = mysqli_query($conn, $sql98);

$sql6 = "CREATE TABLE IF NOT EXISTS `pagamentos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `idpagamento` varchar(50) DEFAULT NULL,
    `valor` varchar(50) DEFAULT NULL,
    `texto` text DEFAULT NULL,
    `iduser` varchar(50) DEFAULT NULL,
    `data` text DEFAULT NULL,
    `status` text DEFAULT NULL,
    `login` text DEFAULT NULL,
    `byid` varchar(50) DEFAULT NULL,
    `access_token` text DEFAULT NULL,
    `tipo` text DEFAULT NULL,
    `addlimite` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    $result6 = mysqli_query($conn, $sql6);

$sql7 = "CREATE TABLE IF NOT EXISTS `servidores` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `subid` int(11) NOT NULL DEFAULT 0,
    `nome` varchar(150) NOT NULL DEFAULT '0',
    `porta` int(11) NOT NULL DEFAULT 0,
    `usuario` varchar(150) NOT NULL DEFAULT '0',
    `senha` varchar(150) NOT NULL DEFAULT '0',
    `ip` varchar(150) NOT NULL DEFAULT '0',
    `servercpu` varchar(150) NOT NULL DEFAULT '0',
    `serverram` varchar(150) NOT NULL DEFAULT '0',
    `onlines` varchar(150) NOT NULL DEFAULT '0',
    `lastview` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
    $result7 = mysqli_query($conn, $sql7);

$sql32 = "CREATE TABLE IF NOT EXISTS `userlimiter` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome_user` varchar(255) DEFAULT NULL,
    `limiter` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
    $result32 = mysqli_query($conn, $sql32);

$sql8 = "CREATE TABLE IF NOT EXISTS `ssh_accounts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `byid` int(11) NOT NULL DEFAULT 0,
    `categoriaid` int(11) NOT NULL DEFAULT 0,
    `limite` int(11) NOT NULL DEFAULT 0,
    `bycredit` int(11) NOT NULL DEFAULT 0,
    `login` varchar(50) NOT NULL DEFAULT '0',
    `senha` varchar(50) NOT NULL DEFAULT '0',
    `mainid` text NOT NULL,
    `expira` text DEFAULT NULL,
    `lastview` text DEFAULT NULL,
    `status` text DEFAULT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
    if ($result8 = mysqli_query($conn, $sql8)) {
        echo "<script>alert('Instalado com sucesso!');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Erro ao instalar!');</script>";
    }

?>
 