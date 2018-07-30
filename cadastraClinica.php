<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('nome_lab', 'cnpj', 'telefone', 'cep', 'rua', 'bairro', 'cidade', 'uf');
$obrigatorio = $valorInicial;

$labCod = 0;
$acao = isset($_POST['btCancelar']) ? ACAO_CONSULTAR : (isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR);
$paramInsert = ValorInicio($valorInicial);
try {
    if (Gravar()) {
        $paramInsert = @pg_escape_string($_POST);
        
        $telefone = limpaString($_POST['telefone']);
        $cnpj = limpaString($_POST['cnpj']);

        try {
            $transac = 0;
            //validaForm();
            $result = pg_query(ConnectPG(), 'begin');
            if (!$result) {throw new Exception("Não foi possível inciar a transação!");}
            $transac = 1;

            $sql = sprintf("INSERT INTO endereco (nome_cidade,logradouro,cep,rua,estado) VALUES (%s,%s,%s,%s,%s)RETURNING id_cidade", 
                            QuotedStr($_POST['cidade']), QuotedStr($_POST['bairro']), QuotedStr($_POST['cep']), 
                            QuotedStr($_POST['rua']), QuotedStr($_POST['uf']));
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possível cadastrar o endereço");}
            $id_endereco = pg_fetch_array($result,NULL,PGSQL_NUM);

            $sql = sprintf("INSERT INTO laboratorio (nome_lab,telefone,cnpj,id_endereco) VALUES(%s,%s,%s,%s)", 
                            QuotedStr($_POST['nome_lab']), QuotedStr($telefone), QuotedStr($cnpj), QuotedStr($id_endereco[0]));
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possível cadastrar a clínica!");}

            $result = pg_query(ConnectPG(), 'commit');
            if (!$result) {throw new Exception("Não foi possível finalizar a transação");}

            if (!$result) {

                foreach ($_POST as $campo => $valor) {
                    $paramInsert[$campo] = $valor;
                }

                throw new Exception("Não foi possível cadastrar a clínica!!");
            }

            echo "<SCRIPT type='text/javascript'>
                    alert('Clínica cadastrada com Sucesso!');
                    window.location.replace(\"listaClinica.php\");
                    </SCRIPT>";
        } catch (Exception $ex) {
            if ($transac) {
                pg_query(ConnectPG(), 'rollback');
            }
            $mensagem = $ex->getMessage();
            Alert($mensagem);
        }
    }
} catch (Exception $e) {
    $mensagem = $e->getMessage();
    Alert($mensagem);
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <div class="body">
        <div class="formcadastro">
            <h2 style="text-align: center">Cadastro de Clínicas</h2>
            <fieldset>
                <form action="cadastraClinica.php" method="POST" id="cadClinica">
                    <input type="hidden" name="acao" value="<?php echo $acao; ?>">
                    <input type="hidden" name="id_lab" value="<?php echo $labCod; ?>">
                    <!--DADOS PESSOAIS --> 
                    <fieldset>
                        <legend>Dados do Laboratorio</legend>
                        <table cellspacing="10">
                            <tr>
                                <td><label for="Laboratorio">Nome: </label></td>
                                <td align="left">
                                    <input type="text" name="nome_lab" size="26" placeholder="Laboratorio" id="nomeClinica" value="<?php echo $paramInsert['nome_lab']; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="cnpj">CNPJ: </label></td>
                                <td align="left">
                                    <input type="text" name="cnpj" size="16" onkeydown="mascara(this,cnpjMask)" id="cnpj" placeholder="CNPJ" maxlength="18" value="<?php echo $paramInsert['cnpj']; ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>Telefone:</label></td>
                                <td align="left">
                                    <input type="text" name="telefone" size="16" onkeyup="telmask(this)" id="telefone" maxlength="15" placeholder="Telefone" value="<?php echo $paramInsert['telefone']; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td><label>Cep: </label></td>
                                <td><input name="cep" type="text" id="cep" value="" placeholder="00000-000" size="10" maxlength="9"onblur="pesquisacep(this.value);" /></td>
                                <td><label>Rua:</label></td>
                                <td><input name="rua" type="text" id="rua" placeholder="Nome da Rua" size="16" /></td>
                            </tr>
                            <tr>
                                <td><label>Bairro:</label></td>
                                <td><input name="bairro" type="text" id="bairro" placeholder="Logradouro" size="16" /></td>
                                <td><label>Cidade:</label></td>
                                <td><input name="cidade" type="text" id="cidade" placeholder="Nome da Cidade" size="16" /></td>
                            </tr>
                            <tr>
                                <td><label>Estado:</label></td>
                                <td><input name="uf" type="text" id="uf" placeholder="UF" size="2" /></td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                    <!--<input type="button" name="btCancelar" value="Cancelar" onclick="javascript: location.href='index.php';">-->
                    <input type="button" name="btCancelar" value="Cancelar" onclick="javascript: window.history.back();">
                    <input type="submit" id="validar" name="btEnviar" value="Cadastrar">
                </form>
            </fieldset>
            <br>
        </div>
    </div>
    <script>
        $("#cadClinica").submit(function() {
            if($("#nomeClinica").val()== null || $("#nomeClinica").val() ==""){
                alert('Campo Nome está em branco!');     
                return false;
            }
            
            if($("#cnpj").val()== null || $("#cnpj").val() ==""){
                alert('Campo cnpj está em branco!');     
                return false;
            }
            
            if($("#telefone").val()== null || $("#telefone").val() ==""){
                alert('Campo telefone está em branco!');     
                return false;
            }
            
            if($("#cep").val()== null || $("#cep").val() ==""){
                alert('Campo cep está em branco!');     
                return false;
            }
            
            if($("#rua").val()== null || $("#rua").val() ==""){
                alert('Campo rua está em branco!');     
                return false;
            }
            
            if($("#bairro").val()== null || $("#bairro").val() ==""){
                alert('Campo bairro está em branco!');     
                return false;
            }
            
            if($("#cidade").val()== null || $("#cidade").val() ==""){
                alert('Campo cidade está em branco!');     
                return false;
            }
            
            if($("#uf").val()== null || $("#uf").val() ==""){
                alert('Campo uf está em branco!');     
                return false;
            }
        });
    </script>
    <?php include 'apoio/footer.php'; ?>
</html>

