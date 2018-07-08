<?php
include_once 'apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('email', 'senha', 'ativo');
$paramEntrar = isset($_POST['paramLogin']) ? $_POST['paramLogin'] : '';
$paramSenha = isset($_POST['paramSenha']) ? $_POST['paramSenha'] : '';
$paramInsert = ValorInicio($valorInicial);

try {
    if (isset($_POST['btLogin']) && empty($_POST['paramLogin']) && empty($_POST['paramSenha'])) {

        foreach ($_POST as $campo => $valor) {
            $paramInsert[$campo] = $valor;
        }

        throw new Exception("Todos os campos devem ser preenchidos!");
        
    } elseif (isset($_POST['btLogin']) && empty($_POST['paramLogin'])) {
        
        throw new Exception("Campo Login deve ser preenchido");
        
    } elseif (isset($_POST['btLogin']) && empty($_POST['paramSenha'])) {
        
        throw new Exception("Campo Senha deve ser preenchido");
    } else{
        
        $sql = sprintf("SELECT email,senha FROM login WHERE email = %s AND senha = %s", 
                       QuotedStr($paramEntrar), QuotedStr(md5($paramSenha)));

        $result = pg_query(ConnectPG(), $sql);

        if (!pg_fetch_row($result)) {

            foreach ($_POST as $campo => $valor) {
                $paramInsert[$campo] = $valor;
            }

            throw new Exception("Login ou senha inválidos!");
        } elseif ($_POST['ativo'] == 'n') {

            throw new Exception("Perfil Desativado!");
        } else {
            header("Location: index.php");
        }
    }
} catch (Exception $ex) {
    $msg = $ex->getMessage();
    Alert($msg);
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <link rel="stylesheet" href="css/login.css">
    <div class="bodyLogin">
        <h3>Bem-Vindo</h3>
        <body>
            <div class="formulario">
                <form action="login.php" method="POST">
                    Email:<br />
                    <input type="text" name="paramLogin" placeholder="Login" value="<?php echo @$paramInsert['paramLogin']; ?>"><br />

                    Senha:<br />
                    <input type="password" name="paramSenha" placeholder="*****" value="<?php echo @$paramInsert['paramSenha']; ?>"><br /><br />

                    <input type="submit" name="btLogin" value="Entrar" class="login"><br>
                </form>
                <a href="redefineSenha.php" class="redSenha">Redefinir Senha</a>
            </div>
        </body>
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>

