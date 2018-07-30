<?php
include_once 'apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('email', 'senha', 'ativo');
$paramInsert = ValorInicio($valorInicial);

// VERIFICA SE FOI ENVIADO AS VARIÁVEIS ATRAVÉS DO POST
if (isset($_POST['paramLogin']) && $_POST['paramLogin'] != "" && $_POST['paramSenha'] != "") {

    // RECEBE AS VARIÁVEIS VIA POST E TRATA O SQL INJECTION FINALIZANDO COM A CODIFICAÇÃO MD5
        $LoginPost = pg_escape_string($_POST['paramLogin']);
        $senhaPost = pg_escape_string($_POST['paramSenha']);

    // VERIFICA SE EXISTE USUÁRIOS CADASTRADOS COM O LOGIN E SENHA INFORMADO
    $sql = sprintf("SELECT id_login,email,senha,ativo FROM login WHERE email = %s AND senha = %s", 
                    QuotedStr($LoginPost), QuotedStr(md5($senhaPost)));
    $result = pg_query(ConnectPG(), $sql);
    $Res = pg_fetch_assoc($result);

    // VERIFICA SE ACHOU ALGUM USUÁRIO CADASTRADO CASO CONTRÁRIO DÁ UM ALERTA PARA O USUÁRIO
    if (!$Res) {
        foreach($_POST as $campo=>$valor){
            $paramInsert[$campo] = $valor;
        }
        Alert("Login ou senha inválidos!");
    }elseif($Res['ativo'] != 's'){
        
        foreach($_POST as $campo=>$valor){
            $paramInsert[$campo] = $valor;
        }
        
        Alert("Este usuário não tem permissão de login!");
    }else {
        // CRIA AS SESSÕES DE VALIDAÇÃO DAS PAGINAS
        session_start();
        $_SESSION['IDlogin'] = $Res['id_login'];
        $_SESSION['paramLogin'] = $Res['email'];
        $_SESSION['paramSenha'] = $Res['senha'];
        
        header("Location: index.php");
    }
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <link rel="stylesheet" href="css/login.css">
    <div class="bodyLogin">
        <h3>Bem-Vindo</h3>
        <body>
            <div class="formulario">
                <form id="login" name="login" action="login.php" method="POST" onsubmit="return check_form()">
                    Email:<br />
                    <input type="text" name="paramLogin" id="paramLogin" placeholder="Login" value="<?php echo @$paramInsert['paramLogin']; ?>"><br />

                    Senha:<br />
                    <input type="password" name="paramSenha" id="paramSenha" placeholder="*****" value="<?php echo @$paramInsert['paramSenha']; ?>"><br /><br />

                    <input type="submit" name="btLogin" value="Entrar" class="login"><br>
                </form>
                <a href="redefineSenha.php" class="redSenha">Redefinir Senha</a>
            </div>
        </body>
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>

