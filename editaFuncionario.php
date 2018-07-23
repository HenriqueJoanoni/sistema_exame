<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';

$funcId = isset($_POST['id_funcao']) ? $_POST['id_funcao'] : '';
$setorId = isset($_POST['id_setor']) ? $_POST['id_setor'] : '';
$ativo = isset($_POST['ativo']) ? $_POST['ativo'] : null;
$idFuncionario = 0; 

$acao = isset($_POST['btCancelar']) ? ACAO_CONSULTAR : (isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR);
@$idFuncionario = $_GET['id_funcionario'];
@$idCidade = $_GET['id_cidade'];

if (Alterar()) {
    if (isset($_POST['btCarregaFuncao'])) {
        $paramInsert = $_POST;
    }
    try {
        $idFuncionario = $_REQUEST['id_funcionario'];
        if (Gravar()) {
            $cpf = limpaString($_POST['cpf']);
            $tel = limpaString($_POST['telefone']);
            $admissao = date("Y-m-d", strtotime(str_replace('/', '-', $_POST['dt_admissao'])));
            $demissao = date("Y-m-d", strtotime(str_replace('/', '-', $_POST['dt_demissao'])));
            $nascimento = date("Y-m-d", strtotime(str_replace('/', '-', $_POST['dt_nascimento'])));

            $transac = 0;
            $sql = pg_query(ConnectPG(), 'begin');
            if (!$sql) {
                throw new Exception("Não foi possível iniciar a transação");
            }
            $transac = 1;

            $sql = sprintf("UPDATE endereco SET (nome_cidade,logradouro,cep,rua,estado) = (%s,%s,%s,%s,%s) "
                    . "WHERE id_cidade =" . $_POST['id_cidade'], 
                    QuotedStr($_POST['cidade']), QuotedStr($_POST['bairro']), QuotedStr($_POST['cep']), 
                    QuotedStr($_POST['rua']), QuotedStr($_POST['uf']));
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {
                throw new Exception("Não foi possível Editar o registro do endereço");
            }

            $sql = sprintf("UPDATE funcionario SET (id_funcao, id_cidade, id_setor, nome, dt_nascimento, telefone, rg, cpf,email, "
                    . "dt_admissao,dt_demissao,ativo) "
                    . "= (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s) WHERE id_funcionario = " 
                    . $_POST['id_funcionario'], QuotedStr($_POST['id_funcao']), QuotedStr($_POST['id_cidade']), 
                    QuotedStr($_POST['id_setor']), QuotedStr($_POST['nome']), QuotedStr($nascimento), QuotedStr($tel), 
                    QuotedStr($_POST['rg']), QuotedStr($cpf), QuotedStr($_POST['email']), QuotedStr($admissao), 
                    QuotedStr($demissao), QuotedStr(@$_POST['ativo']));
            @$result = pg_query(ConnectPG(), $sql);

            $sql = pg_query(ConnectPG(), 'commit');
            if (!$sql) {
                throw new Exception("Não foi possível finalizar a transação!");
            }

            if (!$result) {
                foreach ($_POST as $campo => $valor) {
                    $paramInsert[$campo] = $valor;
                }
                throw new Exception("Não foi possível Editar o registro do funcionário");
            }
            Alert("Registro do Funcionário Editado com Sucesso!");
        } else {
            $sql = "SELECT a.id_cidade,a.id_funcionario,a.nome,to_char(a.dt_nascimento, 'dd/mm/yyyy') as dt_nascimento,a.rg,a.cpf,a.email,
            to_char(a.dt_admissao, 'dd/mm/yyyy') as dt_admissao,to_char(a.dt_demissao, 'dd/mm/yyyy') as dt_demissao,a.telefone,a.ativo, 
            b.nome_cidade,b.logradouro,b.cep,b.rua,b.estado,c.descricao,d.nome_setor
                    FROM funcionario a
                    INNER JOIN endereco b ON a.id_cidade = b.id_cidade
                    INNER JOIN funcao c ON a.id_funcao = c.id_funcao
                    INNER JOIN setor d ON a.id_setor = d.id_setor
                    WHERE id_funcionario =  $idFuncionario";

            $result = pg_query(ConnectPG(), $sql);

            if (!$result) {
                foreach ($_POST as $campo => $valor) {
                    $paramInsert[$campo] = $valor;
                }
                throw new Exception("Não foi possível consultar o registro deste funcionário");
            }
        }
    } catch (Exception $ex) {
        $msg = $ex->getMessage();
        Alert($msg);
    }
}
$sql = "SELECT c.id_funcao,d.id_setor,a.id_cidade,a.id_funcionario,a.nome,to_char(a.dt_nascimento, 'dd/mm/yyyy') as dt_nascimento,a.rg,a.cpf,a.email,
            to_char(a.dt_admissao, 'dd/mm/yyyy') as dt_admissao,to_char(a.dt_demissao, 'dd/mm/yyyy') as dt_demissao,a.telefone,a.ativo, 
            b.nome_cidade,b.logradouro,b.cep,b.rua,b.estado,c.descricao,d.nome_setor
                    FROM funcionario a
                    INNER JOIN endereco b ON a.id_cidade = b.id_cidade
                    INNER JOIN funcao c ON a.id_funcao = c.id_funcao
                    INNER JOIN setor d ON a.id_setor = d.id_setor
                    WHERE id_funcionario =  $idFuncionario";

    @$result = pg_query(ConnectPG(), $sql);
    @$funcionario = pg_fetch_array($result,NULL,PGSQL_ASSOC);
    
    //print_r($funcionario);
    
if(!$result){throw new Exception("Não foi possível buscar o registro do Funcionário!!");}
?>
<html>
<?php include 'apoio/header.php'; ?>
    <div class="body">
        <h2 style="text-align: center">Edição de Funcionários</h2>
        <fieldset>
            <form action="editaFuncionario.php" method="POST">
                <input type="hidden" name="acao" value="<?php echo $acao; ?>">
                <input type="hidden" name="id_funcionario" value="<?php echo $idFuncionario; ?>">
                <input type="hidden" name="id_cidade" value="<?php echo $idCidade; ?>"
                <!--DADOS PESSOAIS --> 
                    <fieldset>
                        <legend>Dados Pessoais</legend>
                        <table cellspacing="10">
                            <tr>
                                <td><label for="nome">Nome: </label></td>
                                <td align="left">
                                    <input type="text" name="nome" size="26" placeholder="Nome do Funcionario" value="<?php echo $funcionario['nome']; ?>">
                                </td>
                                <td><label>Nascimento: </label></td>
                                <td align="left">
                                    <input type="text" name="dt_nascimento" onkeypress="mascaraData(this)" size="16" placeholder="dd/mm/aa" value="<?php echo $funcionario['dt_nascimento']; ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>Email: </label></td>
                                <td align="left">
                                    <input type="text" name="email"  size="26" placeholder="email do Funcionario" value="<?php echo $funcionario['email']; ?>"> 
                                </td>
                                <td><label for="rg">RG: </label></td>
                                <td align="left">
                                    <input type="text" name="rg" size="16" placeholder="RG" maxlength="13" value="<?php echo $funcionario['rg']; ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>CPF:</label></td>
                                <td align="left">
                                    <input type="text" maxlength="14" onkeydown="mascara(this,cpfMask)" size="16" name="cpf" placeholder="000.000.000-00" value="<?php echo inputCpf($funcionario['cpf']); ?>">
                                </td>
                                <td><label>Telefone: </label></td>
                                <td align="left">
                                    <input type="text" name="telefone" size="16" placeholder="(xx)xxxxx-xxxx" onkeyup="telmask(this)" value="<?php echo inputFone($funcionario['telefone']); ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>Data de Admissão: </label></td>
                                <td align="left">
                                    <input type="text" name="dt_admissao" onkeypress="mascaraData(this)" size="16" placeholder="dd/mm/aa" value="<?php echo $funcionario['dt_admissao']; ?>"> 
                                </td>
                            </tr>
                            <tr>
                                <td><label>Cep: </label></td>
                                <td><input name="cep" type="text" id="cep" value="<?php echo $funcionario['cep']; ?>" placeholder="00000-000" size="10" 
                                           maxlength="9" onblur="pesquisacep(this.value);"/></td>
                                <td><label>Rua:</label></td>
                                <td><input name="rua" type="text" id="rua" placeholder="Nome da Rua" size="16" value="<?php echo $funcionario['rua']; ?>"/></td>
                            </tr>
                            <tr>
                                <td><label>Bairro:</label></td>
                                <td><input name="bairro" type="text" id="bairro" placeholder="Logradouro" size="16" value="<?php echo $funcionario['logradouro']; ?>"/></td>
                                <td><label>Cidade:</label></td>
                                <td><input name="cidade" type="text" id="cidade" placeholder="Nome da Cidade" size="16" value="<?php echo $funcionario['nome_cidade']; ?>"/></td>
                            </tr>
                            <tr>
                                <td><label>Estado:</label></td>
                                <td><input name="uf" type="text" id="uf" placeholder="UF" size="2" value="<?php echo $funcionario['estado']; ?>"/></td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                    <!-- ENDEREÇO -->
                    <fieldset>
                        <legend>Dados de Função</legend>
                        <table cellspacing="10">
                            <tr>
                                <td><label for="id_Setor">Setor</label></td>
                                <td align="left">
                                    <select size="1" name="id_setor">
                                        <?php echo GetSetor($setorId) ?>
                                    </select>
                                    <input type="submit" name="btCarregaFuncao" value="..."/>
                                </td>
                                <td><label>Data de Demissão:</label></td>
                                <td><input type="text" name="dt_demissao" placeholder="dd/mm/aa" size="16" 
                                           onkeypress="mascaraData(this)" value="<?php echo $funcionario['dt_demissao']?>"</td>
                            </tr>
                            <tr>
                                <td><label for="id_funcao">Função:</label></td>
                                <td align="left">
                                    <select size="1" name="id_funcao">
                                        <?php echo GetFuncao($setorId,$funcId) ?>
                                    </select>
                                </td>
                            </tr>
                            <tr><td>Permitir Login<input type="checkbox" name="ativo" value="s" 
                                    <?php echo $funcionario['ativo'] == ' ' || !$funcionario['ativo']  ? '' : 'checked=""'?>></td></tr>
                        </table>
                    </fieldset>
                    <br />
            <input type="button" name="btCancelar" value="Cancelar" onclick="javascript: location.href = 'listaFuncionario.php';">
            <input type="submit" name="btEnviar" value="Alterar"/>
        </div>
    <?php include 'apoio/footer.php'; ?>
</html>

