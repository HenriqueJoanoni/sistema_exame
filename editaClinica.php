<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';

$id_laboratorio = 0;

$acao = isset($_POST['btCancelar']) ? ACAO_CONSULTAR : (isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR);
@$id_laboratorio = $_GET['id_laboratorio'];
@$idCidade = $_GET['id_cidade'];

if (Alterar()) {
    try {
        $id_laboratorio = $_REQUEST['id_laboratorio'];
        if (Gravar()) {
            $cep = limpaString($_POST['cep']);
            $tel = limpaString($_POST['telefone']);
            $cnpj = limpaString($_POST['cnpj']);

            $transac = 0;
            $sql = pg_query(ConnectPG(), 'begin');
            if (!$sql) {throw new Exception("Não foi possível iniciar a transação");}
            $transac = 1;

            $sql = sprintf("UPDATE endereco SET (nome_cidade,logradouro,cep,rua,estado) = (%s,%s,%s,%s,%s) "
                    . "WHERE id_cidade = ".$_POST['id_cidade'], QuotedStr($_POST['cidade']), QuotedStr($_POST['bairro']), 
                    QuotedStr($cep), QuotedStr($_POST['rua']), QuotedStr($_POST['uf']));
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possível Editar o registro do endereço");}

            $sql = sprintf("UPDATE laboratorio SET nome_lab = %s, telefone = %s, cnpj = %s, id_endereco = %s WHERE id_laboratorio = %s",
                            QuotedStr($_POST['nome_lab']), QuotedStr($tel), QuotedStr($cnpj),$_POST['id_cidade'],$id_laboratorio);
            $result = pg_query(ConnectPG(), $sql);

            $sql = pg_query(ConnectPG(), 'commit');
            if (!$sql) {throw new Exception("Não foi possível finalizar a transação!");}

            if (!$result) {
                foreach ($_POST as $campo => $valor) {
                    $laboratorio[$campo] = $valor;
                }
                throw new Exception("Não foi possível Editar o registro da Clínica");
            } else {

                echo "<SCRIPT type='text/javascript'> 
                        alert('Registro da Clínica Editado com Sucesso!');
                        window.location.replace(\"listaClinica.php\");
                    </SCRIPT>";
            }
        }
    } catch (Exception $ex) {
        $msg = $ex->getMessage();
        Alert($msg);
    }
}
$sql = "SELECT a.id_laboratorio, b.id_cidade, a.nome_lab, a.telefone, a.cnpj, b.nome_cidade, b.logradouro, b.cep, b.rua, b.estado
	FROM laboratorio a
	INNER JOIN endereco b ON a.id_endereco = b.id_cidade
	WHERE a.id_laboratorio = $id_laboratorio";

$result = pg_query(ConnectPG(), $sql);
$laboratorio = pg_fetch_array($result, NULL, PGSQL_ASSOC);

//print_r($laboratorio);

if (!$result) {throw new Exception("Não foi possível buscar o registro do Funcionário!!");}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <div class="body">
        <div class="formcadastro">
            <h2 style="text-align: center">Edição de Clínicas</h2>
            <fieldset>
                <form action="editaClinica.php" method="POST">
                    <input type="hidden" name="acao" value="<?php echo $acao; ?>">
                    <input type="hidden" name="id_laboratorio" value="<?php echo $id_laboratorio; ?>">
                    <input type="hidden" name="id_cidade" value="<?php echo $idCidade; ?>">
                    <!--DADOS PESSOAIS --> 
                    <fieldset>
                        <legend>Dados do Laboratorio</legend>
                        <table cellspacing="10">
                            <tr>
                                <td><label for="Laboratorio">Nome: </label></td>
                                <td align="left">
                                    <input type="text" name="nome_lab" size="26" placeholder="Laboratorio" value="<?php echo $laboratorio['nome_lab']; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="cnpj">CNPJ: </label></td>
                                <td align="left">
                                    <input type="text" name="cnpj" size="16" onkeydown="mascara(this,cnpjMask)" placeholder="CNPJ" maxlength="18" value="<?php echo inputCnpj($laboratorio['cnpj']); ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>Telefone:</label></td>
                                <td align="left">
                                    <input type="text" name="telefone" size="16" onkeyup="telmask(this)" maxlength="15" placeholder="Telefone" value="<?php echo inputFone($laboratorio['telefone']); ?>">
                                </td>
                            </tr>
                            <tr>
                                <td><label>Cep: </label></td>
                                <td><input name="cep" type="text" id="cep" value="<?php echo $laboratorio['cep']?>" placeholder="00000-000" size="10" maxlength="9"onblur="pesquisacep(this.value);"/></td>
                                <td><label>Rua:</label></td>
                                <td><input name="rua" type="text" id="rua" placeholder="Nome da Rua" size="16" value="<?php echo $laboratorio['rua']?>"/></td>
                            </tr>
                            <tr>
                                <td><label>Bairro:</label></td>
                                <td><input name="bairro" type="text" id="bairro" placeholder="Logradouro" size="16" value="<?php echo $laboratorio['logradouro']?>"/></td>
                                <td><label>Cidade:</label></td>
                                <td><input name="cidade" type="text" id="cidade" placeholder="Nome da Cidade" size="16" value="<?php echo $laboratorio['nome_cidade']?>"/></td>
                            </tr>
                            <tr>
                                <td><label>Estado:</label></td>
                                <td><input name="uf" type="text" id="uf" placeholder="UF" size="2" value="<?php echo $laboratorio['estado']?>"/></td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                    <input type="button" name="btCancelar" value="Cancelar" onclick="javascript: location.href='listaClinica.php';">
                    <!--<input type="button" name="btCancelar" value="Cancelar" onclick="javascript: window.history.back();">-->
                    <input type="submit" name="btEnviar" value="Cadastrar">
                </form>
            </fieldset>
            <br>
        </div>
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>

