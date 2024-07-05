<?php
error_reporting(0);
session_start();
include('../atlas/conexao.php');
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
mysqli_set_charset($conn, "utf8mb4");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

include_once 'headeradmin2.php';

$dominio = $_SERVER['HTTP_HOST'];

$sql = "SELECT * FROM accounts WHERE id = '" . $_SESSION['iduser'] . "'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $accesstoken = $row['accesstoken'];
        $acesstokenpaghiper = $row['acesstokenpaghiper'];
    }
}

$sql = "SELECT * FROM configs WHERE id = '1'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$applink = $row['cortextcard'];
$validade = date('d/m/Y', strtotime($_SESSION['validadefin']));

$sucess_servers = isset($_GET['sucess']) ? explode(", ", $_GET['sucess']) : array();
$failed_servers = isset($_GET['failed']) ? explode(", ", $_GET['failed']) : array();

$sqlwhats = "SELECT * FROM whatsapp WHERE byid = '" . $_SESSION['iduser'] . "'";
$resultwhats = mysqli_query($conn, $sqlwhats);
$rowwhats = mysqli_fetch_assoc($resultwhats);
$tokenwpp = $rowwhats['token'];
$sessaowpp = $rowwhats['sessao'];
$ativewpp = $rowwhats['ativo'];

if ($tokenwpp != '' || $sessaowpp != '') {
    $mensagens = "SELECT * FROM mensagens WHERE ativo = 'ativada' AND funcao = 'criarusuario' AND byid = '" . $_SESSION['iduser'] . "'";
    $resultmensagens = mysqli_query($conn, $mensagens);
    $rowmensagens = mysqli_fetch_assoc($resultmensagens);
    $mensagem = $rowmensagens['mensagem'];

    // Remove os <br> da mensagem para enviar no WhatsApp
    if (!empty($mensagem)) {
        $mensagem = strip_tags($mensagem);
        $mensagem = str_replace("<br>", "\n", $mensagem);
        $mensagem = str_replace("<br><br>", "\n", $mensagem);

        // Substitui variáveis na mensagem
        $mensagem = str_replace("{login}", $_SESSION['usuariofin'], $mensagem);
        $mensagem = str_replace("{usuario}", $_SESSION['usuariofin'], $mensagem);
        $mensagem = str_replace("{senha}", $_SESSION['senhafin'], $mensagem);
        $mensagem = str_replace("{validade}", $validade, $mensagem);
        $mensagem = str_replace("{limite}", $_SESSION['limitefin'], $mensagem);
        $mensagem = str_replace("{dominio}", $dominio, $mensagem);

        $mensagem = mysqli_real_escape_string($conn, $mensagem);

        echo "<script>
            var enviado = false;
            var phoneNumber = '" . str_replace("+", "", $_SESSION['whatsapp']) . "';
            const message = '" . $mensagem . "';

            const data = {
                number: phoneNumber,
                textMessage: {
                    text: message
                },
                options: {
                    delay: 0,
                    presence: 'composing'
                }
            };

            const urlsend = 'https://" . $apiserver . "/message/sendText/" . $sessaowpp . "';
            const headerssend = {
                accept: '*/*',
                Authorization: 'Bearer " . $tokenwpp . "',
                'Content-Type': 'application/json'
            };

            const enviar = () => {
                if (!enviado) {
                    enviado = true;

                    $.ajax({
                        url: urlsend,
                        type: 'POST',
                        data: JSON.stringify(data),
                        headers: headerssend,
                        success: function(response) {
                            console.log(response);
                            if (response.status == 'success') {
                                // Ação de sucesso
                            } else {
                                // Tratamento de erro
                            }
                        },
                        error: function(error) {
                            console.error('Erro ao enviar mensagem:', error);
                        }
                    });
                }
            };

            enviar();
        </script>";

        $_SESSION['mensagem_enviada'] = true;
    }
}
?>

<!-- Modal de Sucesso -->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="col-md-6 col-12">
            <script>
                $(document).ready(function(){
                    $("#criado").modal('show');
                });
            </script>

            <!-- Modal Criado -->
            <div class="modal fade" id="criado" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="bg-alert modal-header">
                            <h5 class="modal-title" id="exampleModalScrollableTitle"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>

                        <div class="modal-body" id="divToCopy">
                            <div class="alert alert-alert" role="alert" style="text-align: center; font-size: 18px;">
                                <div class="divider divider-success">
                                    <strong class="divider-text" style="font-size: 20px;">🎉 Usuario Criado 🎉</strong>
                                </div>

                                <p>🔎 Usuario: <?php echo $_SESSION['usuariofin']; ?></p>
                                <p>🔑 Senha: <?php echo $_SESSION['senhafin']; ?></p>
                                <p>🎯 Validade: <?php echo $validade; ?></p>
                                <p> Limite: <?php echo $_SESSION['limitefin']; ?></p>

                                <?php
                                if (!empty($_SESSION['uuid'])) {
                                    echo "<p>🔑 UUID V2ray: " . $_SESSION['uuid'] . "</p>";
                                }
                                ?>

                                <p><?php echo $applink; ?></p>

                                <?php
                                if ($accesstoken != '' || $acesstokenpaghiper != '') {
                                    echo '
                                    <p>🌍 Link de Renovação: https://' . $dominio . '/renovar.php</p>
                                    <p>Esse link servirá para você fazer as suas renovações.</p>
                                    ';
                                }
                                ?>

                                <div class="divider divider-success">
                                    <p><strong class="divider-text" style="font-size: 20px;"></strong></p>
                                </div>
                            </div>
                        </div>

                        <p style="text-align: center;">✔️ Criado: <?php echo implode(", ", $sucess_servers); ?></p>

                        <?php
                        if ($failed_servers[0] != "") {
                            echo '
                            <p style="text-align: center;">❌ Falha: ' . implode(", ", $failed_servers) . '</p>
                            ';
                        }
                        ?>

                        <div class="modal-footer">
                            <div class="btn-group dropup mr-1 mb-1">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Copiar
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" onclick="copyDivToClipboard()">Copiar</a>
                                    <a class="dropdown-item" onclick="shareOnWhatsApp()">Compartilhar no Whatsapp</a>
                                    <a class="dropdown-item" onclick="copytotelegram()">Compartilhar no Telegram</a>
                                </div>
                                <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                                    <i class="bx bx-x d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Lista de Usuarios</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fim Modal Criado -->

            <script>
                // Mostra toast
                $(document).ready(function() {
                    $("#toast-toggler").click();
                });

                // Se o usuário fechar o modal, ele volta para a lista de usuários
                $(document).ready(function() {
                    $("#criado").on('hidden.bs.modal', function() {
                        window.location.href = "listarusuarios.php";
                    });
                });

                function copyDivToClipboard() {
                    var range = document.createRange();
                    range.selectNode(document.getElementById("divToCopy"));
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);
                    document.execCommand("copy");
                    window.getSelection().removeAllRanges();
                    swal("Copiado!", "", "success");
                }

                function shareOnWhatsApp() {
                    var text = "🎉 Conta Criada com Sucesso! 🎉\\n" +
                        "🔎 Usuario: <?php echo $_SESSION['usuariofin']; ?>\\n" +
                        "🔑 Senha: <?php echo $_SESSION['senhafin']; ?>\\n" +
                        "🎯 Validade: <?php echo $validade; ?>\\n" +
                        " Limite: <?php echo $_SESSION['limitefin']; ?>\\n" +
                        <?php
                        '' . $applink . '\\n\\n';
                        if ($accesstoken == '') {
                            echo '" "';
                        } else {
                            echo '
                            "🌍 Link de Renovação: https://' . $dominio . '/renovar.php\\n" +
                            "Esse link servirá para você fazer as suas renovações.\\n\\n";
                            ';
                        }
                        ?>
                    
                    var encodedText = encodeURIComponent(text);
                    var whatsappUrl = "https://api.whatsapp.com/send?text=" + encodedText;
                    window.open(whatsappUrl);
                }

                function copytotelegram() {
                    var text = "🎉 Conta Criada com Sucesso! 🎉\\n" +
                        "🔎 Usuario: <?php echo $_SESSION['usuariofin']; ?>\\n" +
                        "🔑 Senha: <?php echo $_SESSION['senhafin']; ?>\\n" +
                        "🎯 Validade: <?php echo $validade; ?>\\n" +
                        " Limite: <?php echo $_SESSION['limitefin']; ?>\\n" +
                        <?php
                        '' . $applink . '\\n\\n';
                        if ($accesstoken == '') {
                            echo '" "';
                        } else {
                            echo '
                            "🌍 Link de Renovação: https://' . $dominio . '/renovar.php\\n" +
                            "Esse link servirá para você fazer as suas renovações.\\n\\n";
                            ';
                        }
                        ?>

                    var encodedText = encodeURIComponent(text);
                    var telegramUrl = "https://t.me/share/url?url=" + encodedText;
                    window.open(telegramUrl);
                }
            </script>

            <script src="../../../app-assets/js/scripts/pages/bootstrap-toast.js"></script>
            <script src="../app-assets/sweetalert.min.js"></script>
        </div>
    </div>
</div>
