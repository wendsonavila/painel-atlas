<?php
session_start();
error_reporting(0);
date_default_timezone_set('America/Sao_Paulo');
require_once '../vendor/pix/autoload.php';
include('../atlas/conexao.php');
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    
}
$sqlvezesuso = "SELECT * FROM cupons";
    $resultvezesuso = $conn->query($sqlvezesuso);
    
    if ($resultvezesuso->num_rows > 0) {
        while ($rowvezesuso = $resultvezesuso->fetch_assoc()) {
            $vezesuso[] = $rowvezesuso;
        }
    
        foreach ($vezesuso as $cupom) {
            if ($cupom['usado'] >= $cupom['vezesuso']) {
                $cupomId = $cupom['id'];
                $sqlDelete = "DELETE FROM cupons WHERE id = $cupomId";
                $conn->query($sqlDelete);
            }
        }
    }

$buscarevenda ="SELECT * FROM accounts WHERE tokenvenda = '$_SESSION[tokenrevenda]'";
$result = mysqli_query($conn, $buscarevenda);
$row = mysqli_fetch_assoc($result);
$valorrevenda = $row['valorrevenda'];
$id = $row['id'];
$_SESSION['byid'] = $row['id'];
$access_token = $row['accesstoken'];
$_SESSION['tokenaccess'] = $row['accesstoken'];
$_SESSION['formadepag'] = $row['formadepag'];
$_SESSION['email'] = $row['contato'];
$_SESSION['nome'] = $row['nome'];
$_SESSION['acesstokenpaghiper'] = $row['acesstokenpaghiper'];
$_SESSION['tokenpaghiper'] = $row['tokenpaghiper'];
$valor = $valorrevenda * $_SESSION['plano'];
$sql3 = "SELECT * FROM cupons WHERE cupom = '$_SESSION[cupom]' AND byid = '$id'";
$result3 = $conn->query($sql3);
//desconto em porcentagem
if ($result3->num_rows > 0) {
    while ($row = $result3->fetch_assoc()) {
        $desconto = $row['desconto'];
        $valor = $valor - ($valor * $desconto / 100);
        $valor = number_format($valor, 2, '.', '');
    }
}
$dt = new DateTime();
$valor = number_format($valor, 2, '.', '');
$_SESSION['valor'] = $valor;
// Adiciona um intervalo de 30 minutos
$interval = date_interval_create_from_date_string('10 minutes');
$dt->add($interval);
$formatted_date = $dt->format('Y-m-d\TH:i:s.000O');

if ($_SESSION['formadepag'] == 1){
MercadoPago\SDK::setAccessToken($access_token);

$payment = new MercadoPago\Payment();
 $payment->transaction_amount = $valor;
 $payment->description = "Compra de plano";
 $payment->payment_method_id = "pix";
 $payment->date_of_expiration = $formatted_date;
 $payment->payer = array(
    "email" => $email
 );

 $payment->save();

$_SESSION['expiracaopix'] = $payment->date_of_expiration;
$_SESSION['payment_id'] = $payment->id;
$_SESSION['qr_code_base64'] = $payment->point_of_interaction->transaction_data->qr_code_base64;
$_SESSION['qr_code'] = $payment->point_of_interaction->transaction_data->qr_code;
if ($_SESSION['qr_code'] == ""){
    echo "<script>alert('Erro ao gerar QR Code')</script>";
    echo "<script>window.location = ('../revenda.php?token=$_SESSION[tokenrevenda]')</script>";
    exit;
}
}
elseif ($_SESSION['formadepag'] == 2){
    $idpedido = rand(1000000000, 9999999999);
    //valoradd para centavos
$valor_em_centavos = round($valor * 100);
//se valoremcentavos for menor que 3 reais ele vai cobrar 3 reais
if ($valor_em_centavos < 300) {
    echo "<script>alert('Valor minimo para pagamento e de R$3,00')</script>";
    echo "<script>window.location = ('../revenda.php')</script>";
    exit;
}
$data = array(
  'apiKey' => $_SESSION['acesstokenpaghiper'], // sua apiKey
  'order_id' => $idpedido,
  'payer_email' => $_SESSION['email'],
  'payer_name' => $_SESSION['nome'],    
  'payer_cpf_cnpj' => '74293930043', // cpf ou cnpj
  'payer_phone' => '1140638785', // fixou ou móvel
  'notification_url' => 'https://mysite.com/notification/paghiper/',
  'shipping_methods' => 'PAC',
  'number_ntfiscal' => $idpedido,
  'fixed_description' => true,
  'days_due_date' => '1', // dias para vencimento do Pix
  'items' => array(
      array ('description' => "Compra de plano Revenda",
      'quantity' => '1',
'item_id' => '1',
'price_cents' => $valor_em_centavos),
  ),
);
$data_post = json_encode( $data );
$url = "https://pix.paghiper.com/invoice/create/";
$mediaType = "application/json"; // formato da requisição
$charSet = "UTF-8";
$headers = array();
$headers[] = "Accept: ".$mediaType;
$headers[] = "Accept-Charset: ".$charSet;
$headers[] = "Accept-Encoding: ".$mediaType;
$headers[] = "Content-Type: ".$mediaType.";charset=".$charSet;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
$json = json_decode($result, true);
// captura o http code
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if($httpCode == 201):
$_SESSION['payment_id'] = $json['pix_create_request']['transaction_id'];
$_SESSION['qr_code_base64'] = $json['pix_create_request']['pix_code']['qrcode_base64'];
$_SESSION['qr_code'] = $json['pix_create_request']['pix_code']['emv'];
//converter para variavel
//echo $result;
else:
echo $result;
endif;
}
$login = $_SESSION['usuario'];
$texto = "Compra de plano Revenda";
$data = date('d-m-Y H:i:s');
$tipo = "Compra de Plano";
$data = date('d-m-Y H:i:s');    
$sql10 = "INSERT INTO pagamentos SET valor = '$valor', login = '$login', texto = '$texto', iduser = '1', byid = '$_SESSION[byid]', data = '$data', idpagamento = '$_SESSION[payment_id]', status = 'Aguardando Pagamento', tipo = '$tipo', addlimite = '0', access_token = '0'";
$result10 = $conn->query($sql10);
echo "<script>window.location = ('pagamento.php')</script>";
?>
