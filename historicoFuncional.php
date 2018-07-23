<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';
$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR;
try{
    $campoConsultar = isset($_POST['btConsultar']) ? $_POST['campoConsultar'] : '';
    
} catch (Exception $ex) {
    $msg = $ex->getMessage();
    Alert($msg);
}

?>
<html>
    <?php include 'apoio/header.php'; ?>
    <div class="main">
        <h2>Histórico Funcional de Funcionários</h2>
        <form method="POST" action="listaFuncionario.php">
            <input type="hidden" name="acao" value="<?php echo $acao; ?>">
            <!--<input type="hidden" name="id_usuario" value="<?php echo $id_funcionario; ?>">-->   
            <div class="campoPesquisa">
                Buscar Funcionário:
                <input type="text" name="campoConsultar" value="<?php echo $campoConsultar; ?>">
                <input type="submit" name="btConsultar" value="Consultar">
            </div>
            <table class="tabelaUsuario" border="3" cellspacing="4" cellpadding="4">
                <thead>
                    <tr>
                        <th>Nome</th><th>Função</th><th>Exame</th><th>Data</th>
                    </tr>
                </thead>
                <?php echo $historicoLista; ?>
            </table>
        </form>
        <br><hr>
        <input type="button" name="btVoltar" value="Menu Principal" onclick="javascript: location.href='index.php';">
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>