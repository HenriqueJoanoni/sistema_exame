<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';

$valorInicial = array('nome','data_de_nascimento','telefone','rg','cpf','email','dt_admissao','cidade','bairro','cep','uf','rua');

$funcId = isset($_POST['id_funcao']) ? $_POST['id_funcao'] : '';
$setorId = isset($_POST['id_setor']) ? $_POST['id_setor'] : '';
$cidadeId = isset($_POST['id_cidade']) ? $_POST['id_cidade'] : '';
$ativo = isset($_POST['ativo']) ? $_POST['ativo'] : null;

$acao = isset($_POST['btCancelar']) ? ACAO_CONSULTAR : (isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR);
$paramInsert = ValorInicio($valorInicial);

try {
    if (isset($_POST['btCarregaFuncao'])) {
        $paramInsert = $_POST;
    }

    if (Gravar()) {
        try {
            $paramInsert = @pg_escape_string($_POST);
            
            $funcao = explode("|", $_POST['id_funcao']);
            $cpf = limpaString($_POST['cpf']);
            $tel = limpaString($_POST['telefone']);
            $admissao = date("Y-m-d", strtotime(str_replace('/', '-', $_POST['dt_admissao'])));
            $nascimento = date("Y-m-d", strtotime(str_replace('/', '-', $_POST['data_de_nascimento'])));

            $transac = 0;
            //validaForm();
            $result = pg_query(ConnectPG(), 'begin');
            if (!$result) {throw new Exception("Não foi possível iniciar a Transação!");}
            $transac = 1;

            $sql = sprintf("INSERT INTO endereco (nome_cidade,logradouro,cep,rua,estado) VALUES (%s,%s,%s,%s,%s)RETURNING id_cidade", 
                            QuotedStr($_POST['cidade']), QuotedStr($_POST['bairro']), QuotedStr($_POST['cep']), 
                            QuotedStr($_POST['rua']), QuotedStr($_POST['uf']));
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possível cadastrar o endereço");}
            $id_cidade = pg_fetch_array($result,NULL,PGSQL_NUM);
            
            $sql = sprintf("INSERT INTO funcionario (id_funcao, id_cidade, id_setor, nome, dt_nascimento, telefone, rg, cpf,email, dt_admissao,ativo) "
                    . "VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",QuotedStr($funcao[0]), 
                    QuotedStr($id_cidade[0]), QuotedStr($_POST['id_setor']), QuotedStr($_POST['nome']), 
                    QuotedStr($nascimento), QuotedStr($tel), QuotedStr($_POST['rg']), QuotedStr($cpf), QuotedStr($_POST['email']), 
                    QuotedStr($admissao), QuotedStr(@$_POST['ativo']));
            $result = pg_query(ConnectPG(), $sql);
            
            if ($ativo == 's') {
                
                $sql = sprintf("INSERT INTO login (email, senha, ativo) VALUES (%s,%s,%s)", 
                        QuotedStr($_POST['email']), QuotedStr(md5('1234')), QuotedStr($ativo));
                $result = pg_query(ConnectPG(), $sql);

                if (!$result) {
                    foreach ($_POST as $campo => $valor) {
                        $paramInsert[$campo] = $valor;
                    }
                    throw new Exception("Não foi possível Incluir o Funcionário à permissão de login!");
                }
            }

            $result = pg_query(ConnectPG(),'commit');
            if(!$result){throw new Exception("Não foi possível finalizar a transação");}
            
            if (!$result) {
                foreach ($_POST as $campo => $valor) {
                    $paramInsert[$campo] = $valor;
                }

                throw new Exception("Não foi possível cadastrar o usuário!!");
            } else {
                echo "<SCRIPT type='text/javascript'> 
                        alert('Funcionário cadastrado com Sucesso!');
                        window.location.replace(\"listaFuncionario.php\");
                    </SCRIPT>";
            }
        } catch (Exception $ex) {
            if ($transac) {
                pg_query(ConnectPG(), 'rollback');
            }
            $msg = $ex->getMessage();
            Alert($msg);
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
            <h2 style="text-align: center">Cadastro de Funcionários</h2>
            <fieldset>
                <form action="cadastraFuncionario.php" method="POST" id="cadFuncionario">
                    <input type="hidden" name="acao" value="<?php echo $acao; ?>">
                    <input type="hidden" name="id_funcionario" value="<?php echo $funcId; ?>">
                    <!--DADOS PESSOAIS --> 
                    <fieldset>
                        <legend>Dados Pessoais</legend>
                        <table cellspacing="10">
                            <tr>
                                <td><label for="nome">Nome: </label></td>
                                <td align="left">
                                    <input type="text" name="nome" size="26" id="nome" placeholder="Nome do Funcionario" value="<?php echo $paramInsert['nome']; ?>">
                                </td>
                                <td><label>Nascimento: </label></td>
                                <td align="left">
                                    <input type="text" name="data_de_nascimento" id="data_nascimento" onkeypress="mascaraData(this)" size="16" placeholder="dd/mm/aa" value="<?php echo $paramInsert['data_de_nascimento']; ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>Email: </label></td>
                                <td align="left">
                                    <input type="text" name="email"  size="26" id="email" placeholder="email do Funcionario" value="<?php echo $paramInsert['email']; ?>"> 
                                </td>
                                <td><label for="rg">RG: </label></td>
                                <td align="left">
                                    <input type="text" name="rg" size="16" id="rg" placeholder="RG" maxlength="13" value="<?php echo $paramInsert['rg']; ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>CPF:</label></td>
                                <td align="left">
                                    <input type="text" maxlength="14" id="cpf" onkeydown="mascara(this,cpfMask)" size="16" name="cpf" placeholder="000.000.000-00" value="<?php echo $paramInsert['cpf']; ?>">
                                </td>
                                <td><label>Telefone: </label></td>
                                <td align="left">
                                    <input type="text" name="telefone" id="telefone" size="16" placeholder="(xx)xxxxx-xxxx" onkeyup="telmask(this)" value="<?php echo $paramInsert['telefone']; ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>Data de Admissão: </label></td>
                                <td align="left">
                                    <input type="text" name="dt_admissao" id="data_admissao" onkeypress="mascaraData(this)" size="16" placeholder="dd/mm/aa" value="<?php echo $paramInsert['dt_admissao']; ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>Cep: </label></td>
                                <td><input name="cep" type="text" id="cep" value="<?php echo $paramInsert['cep']; ?>" placeholder="00000-000" size="10" maxlength="9" onblur="pesquisacep(this.value);"/></td>
                                <td><label>Rua:</label></td>
                                <td><input name="rua" type="text" id="rua" placeholder="Nome da Rua" size="16" value="<?php echo $paramInsert['rua'];?>"/></td>
                            </tr>
                            <tr>
                                <td><label>Bairro:</label></td>
                                <td><input name="bairro" type="text" id="bairro" placeholder="Logradouro" size="16" value="<?php echo $paramInsert['bairro'];?>"/></td>
                                <td><label>Cidade:</label></td>
                                <td><input name="cidade" type="text" id="cidade" placeholder="Nome da Cidade" size="16" value="<?php echo $paramInsert['cidade'];?>"/></td>
                            </tr>
                            <tr>
                                <td><label>Estado:</label></td>
                                <td><input name="uf" type="text" id="uf" placeholder="UF" size="2" value="<?php echo $paramInsert['uf'];?>"/></td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                    <!-- ENDEREÇO -->
                    <fieldset>
                        <legend>Dados de Função</legend>
                        <table cellspacing="10">
                            <tr>
                                <td><label for="id_setor">Setor</label></td>
                                <td align="left">
                                    <select size="1" name="id_setor">
                                        <?php echo GetSetor($setorId,null) ?>
                                    </select>
                                    <input type="submit" name="btCarregaFuncao" value="..."/>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="id_funcao">Função:</label></td>
                                <td align="left">
                                    <select size="1" name="id_funcao"> 
                                    <?php echo GetFuncao($setorId, $funcId) ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Permitir Login<input type="checkbox" name="ativo" value="s"></td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                    <input type="button" name="btCancelar" value="Cancelar" onclick="javascript: window.history.back();">
                    <input type="submit" name="btEnviar" value="Cadastrar">
                </form>
            </fieldset>
            <br>
        </div>
    </div>
    <script>
        $("#cadFuncionario").submit(function() {
            if($("#nome").val()== null || $("#nome").val() ==""){
                alert('Campo Nome está em branco!');     
                return false;
            }
            
            if($("#data_nascimento").val()== null || $("#data_nascimento").val() ==""){
                alert('Campo Data de Nascimento está em branco!');     
                return false;
            }
            
            if($("#email").val()== null || $("#email").val() ==""){
                alert('Campo Email está em branco!');     
                return false;
            }
            
            if($("#rg").val()== null || $("#rg").val() ==""){
                alert('Campo RG está em branco!');     
                return false;
            }
            
            if($("#cpf").val()== null || $("#cpf").val() ==""){
                alert('Campo CPF está em branco!');     
                return false;
            }
            
            if($("#telefone").val()== null || $("#telefone").val() ==""){
                alert('Campo Telefone está em branco!');     
                return false;
            }
            
            if($("#data_admissao").val()== null || $("#data_admissao").val() ==""){
                alert('Campo Data de Admissão está em branco!');     
                return false;
            }
            
            if($("#cep").val()== null || $("#cep").val() ==""){
                alert('Campo CEP está em branco!');     
                return false;
            }
            
            if($("#rua").val()== null || $("#rua").val() ==""){
                alert('Campo Rua está em branco!');     
                return false;
            }
            
            if($("#bairro").val()== null || $("#bairro").val() ==""){
                alert('Campo Bairro está em branco!');     
                return false;
            }
            
            if($("#cidade").val()== null || $("#cidade").val() ==""){
                alert('Campo Cidade está em branco!');     
                return false;
            }
            
            if($("#uf").val()== null || $("#uf").val() ==""){
                alert('Campo UF está em branco!');     
                return false;
            }
        });
    </script>
    <?php include 'apoio/footer.php'; ?>
</html>

