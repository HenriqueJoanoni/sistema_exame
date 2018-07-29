<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';

$historico = 0; 

$acao = isset($_POST['btCancelar']) ? ACAO_CONSULTAR : (isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR);
$historico = $_GET['id_historico'];

if (Alterar()) {
    if (isset($_POST['btCarregaFuncao'])) {
        $paramInsert = $_POST;
    }
    try {
        $historico = $_REQUEST['id_historico'];
        if (Gravar()) {
            
            $transac = 0;
            $sql = pg_query(ConnectPG(), 'begin');
            if (!$sql) {throw new Exception("Não foi possível iniciar a transação");}
            $transac = 1;
            
            $sql = sprintf("UPDATE historico_funcional SET id_funcionario = %s, "
                    . "id_funcao = %s, id_exame = %s, dt_inicio = %s, dt_fim = %s, "
                    . "id_laboratorio = %s WHERE id_historico_funcional = %s", 
                    QuotedStr($str),QuotedStr($str),QuotedStr($str),QuotedStr($str),
                    QuotedStr($str),QuotedStr($str),QuotedStr($str));
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possível editar este histórico!");}

            $sql = pg_query(ConnectPG(), 'commit');
            if (!$sql) {throw new Exception("Não foi possível finalizar a transação!");}

            if (!$result) {
                foreach ($_POST as $campo => $valor) {
                    $paramInsert[$campo] = $valor;
                }
                throw new Exception("Não foi possível editar o registro.............");
            }else{
                
                echo "<SCRIPT type='text/javascript'> 
                        alert('Registro do Funcionário Editado com Sucesso!');
                        window.location.replace(\"listaFuncionario.php\");
                    </SCRIPT>";
            }
            
        } 
    } catch (Exception $ex) {
        $msg = $ex->getMessage();
        Alert($msg);
    }
}
$sql = "SELECT a.id_historico_funcional AS historico, b.id_funcionario, c.id_funcao, d.id_exame, f.id_setor, e.id_laboratorio, f.nome_setor as setor, 
	to_char(a.dt_inicio, 'dd/mm/yyyy') AS data_pedido, to_char(a.dt_fim, 'dd/mm/yyyy') AS data_realizado,
        b.nome,c.descricao AS funcao,d.tipo_exame, d.exame_descricao, e.nome_lab
            FROM historico_funcional a
            LEFT JOIN funcionario b ON a.id_funcionario = b.id_funcionario
            LEFT JOIN funcao c ON a.id_funcao = c.id_funcao
            RIGHT JOIN exame d ON a.id_exame = d.id_exame
            INNER JOIN laboratorio e ON a.id_laboratorio = e.id_laboratorio
            LEFT JOIN setor f ON b.id_setor = f.id_setor
                    WHERE a.id_historico_funcional = $historico";

    $result = pg_query(ConnectPG(), $sql);
    $historico = pg_fetch_array($result,NULL,PGSQL_ASSOC);
    
    //print_r($laboratorio);
    
if(!$result){throw new Exception("Não foi possível consultar este histórico");}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <h2>Gerar de  Exame Admissional/Demissional</h2>
    <div class="body">
        <div class="geraExame">
            <form action="historicoFuncional.php" method="POST">
                <input type="hidden" name="acao" value="<?php $acao; ?>">
                <input type="hidden" name="id_historico" value="<?php @$historico; ?>">   
                <fieldset style="margin-left: 250px;">
                    <table>
                        <tr>
                            <td><label for="nome">Nome: </label></td>
                            <td align="left">
                                <input type="text" name="nome" size="26" placeholder="Nome do Funcionario" value="<?php echo $historico['nome']; ?>">
                            </td>
                            <td><label>Setor: </label></td>
                            <td align="left">
                                <select size="1" name="id_setor">
                                    <?php echo GetSetor($historico['id_setor'], $historico['id_setor']) ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label>Função: </label></td>
                            <td align="left">
                                <select size="1" name="id_funcao">
                                    <?php echo GetFuncao($historico['id_setor'], null, $historico['id_funcionario']) ?>
                                </select>
                            </td>
                            <td><label for="id_laboratorio">Clínica</label></td>
                            <td align="left">
                                <select size="1" name="id_laboratorio">
                                    <?php echo GetClinica($historico['id_laboratorio']) ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="id_exame">Tipo de Exame</label></td>
                            <td align="left">
                                <select size="1" name="id_exame">
                                    <?php echo GetExame($historico['id_exame']) ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="data_pedido">Data do Pedido: </label></td>
                            <td align="left">
                                <input type="text" name="data_pedido" size="26" placeholder="dd/mm/yyyy" value="<?php echo $historico['data_pedido']; ?>">
                            </td>   
                        </tr>
                        <tr>
                            <td><label for="data_realizado">Data da Realização: </label></td>
                            <td align="left">
                                <input type="text" name="data_realizado" size="26" placeholder="dd/mm/yyyy" value="<?php echo $historico['data_realizado']; ?>">
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