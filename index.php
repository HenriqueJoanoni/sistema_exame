<?php
require_once 'apoio/valida.php';
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <div class="body">
        <h2>Escolha uma Opção</h2>
        <div class="content">
            <div class="opcao">
                <a href="cadastraFuncionario.php"><img src="img/add-user.png" border="0" width="100%" height="200"/></a><br /><br />
                <strong><a href="cadastraFuncionario.php" style="text-decoration: none;">Cadastrar Funcionário</a></strong>
            </div>
            <div class="opcao">
                <a href="listaFuncionario.php"><img src="img/employees.png" border="0" width="100%" height="200"/></a><br /><br />
                <strong><a href="listaFuncionario.php" style="text-decoration: none;"> Listar Funcionários</a></strong>
            </div>
            <div class="opcao">
                <a href="cadastraClinica.php"><img src="img/hospital-antigo.png" border="0" width="100%" height="200"/></a><br /><br />
                <strong><a href="cadastraClinica.php" style="text-decoration: none;">Cadastrar Clínica</a></strong>
            </div>
            <div class="opcao">
                <a href="geraExame.php"><img src="img/examination.png" border="0" width="100%" height="200"/></a><br /><br />
                <strong><a href="geraExame.php" style="text-decoration: none;">Gerar Exame</a></strong>
            </div>
            <div class="opcao">
                <a href="historicoFuncional.php"><img src="img/medical-history.png" border="0" width="200" height="200"/></a><br /><br />
                <strong><a href="historicoFuncional.php" style="text-decoration: none;">Histórico Funcional</a></strong>
            </div>
            <div class="opcao">
                <a href="listaClinica.php"><img src="img/hospital.png" border="0" width="200" height="200"/></a><br /><br />
                <strong><a href="listaClinica.php" style="text-decoration: none;">Listar Clínicas</a></strong>
            </div>
        </div>
        <input type="button" name="btSair" value="sair" onclick="javascript: location.href='apoio/logoff.php'">
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>
