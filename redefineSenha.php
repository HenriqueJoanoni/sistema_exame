<?php
include './apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('email', 'senha', 'ativo');
$paramEmail = isset($_POST['paramEmail']) ? $_POST['paramEmail'] : '';
$paramSenha = isset($_POST['paramSenha']) ? $_POST['paramSenha'] : '';
$confirmaSenha = isset($_POST['repeteSenha']) ? $_POST['repeteSenha'] : '';
$paramInsert = ValorInicio($valorInicial);

$acao = isset($_POST['btCancelar']) ? ACAO_CANCELAR : isset($_REQUEST['acao']) ? isset($_REQUEST['acao']) : ACAO_ALTERAR;
try {
    if (Gravar()) {
        $sql = sprintf("SELECT email FROM login WHERE email = %s", QuotedStr(pg_escape_string($paramEmail)));
        $result = pg_query(ConnectPG(), $sql);

        if (!pg_num_rows($result)) {

            throw new Exception("Email Inválido");
        } elseif (md5 ($_POST['paramSenha']) !== md5 ($_POST['repeteSenha'])) {

            foreach ($_POST as $campo => $valor) {
                $paramInsert[$campo] = $valor;
            }
            throw new Exception("As senhas não conferem!");
        }elseif ($_POST['paramEmail'] && !$_POST['paramSenha'] || !$_POST['repeteSenha']) {
            
            foreach ($_POST as $campo => $valor) {
                $paramInsert[$campo] = $valor;
            }
            throw new Exception("Existem campos vazios !");
            
        }else {
            $sql = sprintf("UPDATE login SET senha = %s WHERE email = %s", QuotedStr(md5($paramSenha)), QuotedStr(pg_escape_string($paramEmail)));
            $result = pg_query(ConnectPG(), $sql);

            Alert("Senha Alterada com sucesso!");
        }
    }
} catch (Exception $ex) {
    $msg = $ex->getMessage();
    Alert($msg);
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <link rel="stylesheet" href="css/redSenha.css">
    <div class="body">
        <h3>Redefinir Senha</h3>
        <body>
            <div class="formularioRedefine">
                <input type="hidden" name="acao" value="<?php echo $acao; ?>"/>
                    <form action="redefineSenha.php" method="POST">
                    Email:<br />
                    <input type="text" name="paramEmail" placeholder="Informe seu Email" value="<?php echo @$paramInsert['paramEmail']; ?>"><br />

                    Nova Senha:<br />
                    <input type="password" name="paramSenha" placeholder="Digite uma nova senha" value="<?php echo @$paramInsert['paramSenha']; ?>"><br />

                    Repita a Senha:<br />
                    <input type="password" name="repeteSenha" placeholder="Repita a nova senha" value="<?php echo @$paramInsert['repeteSenha']; ?>"><br /><br />

                    <input type="submit" name="btEnviar" value="Redefinir"><br><br>
                </form>
                <a href="login.php" class="retornaLogin">Cancelar</a>
            </div>
        </body>
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>

