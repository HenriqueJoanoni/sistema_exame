<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('id_funcionario', 'id_setor', 'id_funcao', 'nome', 'nome_setor', 'descricao', 'id_laboratorio', 'nome_lab', 'tipo_exame', 'exame_descricao');
$clinicaId = isset($_POST['id_laboratorio']) ? $_POST['id_laboratorio'] : '';
$exameId = isset($_POST['id_exame']) ? $_POST['id_exame'] : '';
$funcId = isset($_POST['id_funcao']) ? $_POST['id_funcao'] : '';
$setorId = isset($_POST['id_setor']) ? $_POST['id_setor'] : '';
$id_funcionario = 0;

$regFunc = ValorInicio($valorInicial);
$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR;
try {
    $campoConsultar = isset($_POST['btConsultar']) ? $_POST['campoConsultar'] : '';

    if (isset($_POST['btConsultar']) && $campoConsultar == "") {
        
        throw new Exception("Por favor informe um funcionário!");
        
    }elseif (isset ($_POST['btConsultar'])) {
        $sql = sprintf("SELECT a.id_funcionario,b.id_setor,c.id_funcao,a.nome, b.nome_setor, c.descricao
                            FROM funcionario a
                            JOIN setor b ON a.id_setor = b.id_setor
                            JOIN funcao c ON a.id_funcao = c.id_funcao
                            WHERE a.nome LIKE %s", PrepararLike($campoConsultar));
        $result = pg_query(ConnectPG(), $sql);
        $regFunc = pg_fetch_assoc($result);
    }

    if (isset($_POST['btEnviar'])) {

        foreach ($_POST as $campo => $valor) {
            $regFunc[$campo] = $valor;
        }
    }
} catch (Exception $ex) {
    $msg = $ex->getMessage();
    Alert($msg);
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <h2>Gerar de  Exame Admissional/Demissional</h2>
    <div class="body">
        <div class="geraExame">
            <div class="formExame">
                <form action="geraExame.php" method="POST">
                    <table align="center">
                        <tr>
                            <td>Selecionar Funcionário:</td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" name="campoConsultar" placeholder="Buscar Funcionário..." value="<?php echo $campoConsultar; ?>"/>
                            </td>
                            <td>
                                <input type="submit" name="btConsultar" value="Consultar"/>
                            </td>
                        </tr>
                    </table>
                </form>
                <form action="examePDF.php" method="POST">
                    <input type="hidden" name="acao" value="<?php echo $acao; ?>">
                    <input type="hidden" name="id_funcionario" value="<?php echo $regFunc['id_funcionario']; ?>">   
                    <fieldset style="margin-left: 250px;">
                        <table>
                            <tr>
                                <td><label for="nome">Nome: </label></td>
                                <td align="left">
                                    <input type="text" name="nome" size="26" placeholder="Nome do Funcionario" value="<?php echo $regFunc['nome']; ?>">
                                </td>
                                <td><label>Setor: </label></td>
                                <td align="left">
                                    <select size="1" name="id_setor">
                                        <?php echo GetSetor($regFunc['id_setor'],$regFunc['id_setor']) ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Função: </label></td>
                                <td align="left">
                                    <select size="1" name="id_funcao">
                                        <?php echo GetFuncao($regFunc['id_setor'],null,$regFunc['id_funcionario']) ?>
                                    </select>
                                </td>
                                <td><label for="id_laboratorio">Clínica</label></td>
                                <td align="left">
                                    <select size="1" name="id_laboratorio">
                                        <?php echo GetClinica($clinicaId) ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="id_exame">Tipo de Exame</label></td>
                                <td align="left">
                                    <select size="1" name="id_exame">
                                        <?php echo GetExame($exameId) ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <br />
                        <input type="button" name="btCancelar" value="Cancelar" onclick="javascript: location.href='index.php';">
                        <input type="submit" name="btEnviar" value="Gerar exame">
                    </fieldset>
                </form>
            </div>
        </div>
    </div>        
    <?php include 'apoio/footer.php'; ?>
</html>
