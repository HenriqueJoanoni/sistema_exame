<?php
/*session_start();
if (isset($_SESSION['paramLogin']) && !empty($_SESSION['paramLogin'])) {
    echo "bem-vindo " . $_SESSION['paramLogin'] . "!";
} else {
    header("Location: login.php");
}
*/
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <!--<div class="topousuario">
        <h3>Bem-Vindo <?php echo $_SESSION['user'] ?>!</h3>
    </div>-->
    <div class="body">
        <div class="content">
            <h2>Escolha uma Opção</h2>
            <div class="opcao">
                <a href="cadastraFuncionario.php"><img src="img/add-user.png" border="0" width="100%" height="200"/></a><br /><br />
                <strong><a href="cadastraFuncionario.php" style="text-decoration: none;">Cadastrar Funcionário</a></strong>
            </div>
            <div class="opcao">
                <a href="listaFuncionario.php"><img src="img/employees.png" border="0" width="100%" height="200"/></a><br /><br />
                <strong><a href="listaFuncionario.php" style="text-decoration: none;"> Listar Funcionários</a></strong>
            </div>
            <div class="opcao">
                <a href="cadastraClinica.php"><img src="img/hospital.png" border="0" width="100%" height="200"/></a><br /><br />
                <strong><a href="cadastraClinica.php" style="text-decoration: none;">Cadastrar Clínica</a></strong>
            </div>
            <div class="opcao">
                <a href="geraExame.php"><img src="img/examination.png" border="0" width="100%" height="200"/></a><br /><br />
                <strong><a href="geraExame.php" style="text-decoration: none;">Gerar Exame</a></strong>
            </div>
            <input type="button" name="btSair" value="sair" onclick="javascript: location.href='login.php';">
        </div>
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>
