<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';
$id_funcionario = 0;
$id_cidade = 0;

$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR;
try {
    $campoConsultar = isset($_POST['btConsultar']) ? $_POST['campoConsultar'] : '';

    if (Apagar()) {

        try {
            $transac = 0;
            $id_funcionario = $_GET['id_funcionario'];
            $id_cidade = $_GET['id_cidade'];

            $result = pg_query(ConnectPG(), 'begin');
            if (!$result) {throw new Exception("Não foi possível iniciar a transação");}
            $transac = 1;

            $sql = sprintf("DELETE FROM funcionario WHERE id_funcionario = %s ", $id_funcionario);
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possivel excluir este funcionário");}
            
            $sql = sprintf("DELETE FROM endereco WHERE id_cidade = %s",$id_cidade);
            $result = pg_query(ConnectPG(),$sql);
            if(!$result){throw new Exception("Não foi possível excluir este endereço");}

            $result = pg_query(ConnectPG(), 'commit');
            if (!$result) {throw new Exception("Não foi possivel concluir a transação");}
            
            echo "<SCRIPT type='text/javascript'> 
                        alert('Funcionário Excluído!');
                        window.location.replace(\"listaFuncionario.php\");
                    </SCRIPT>";
        } catch (Exception $e) {
            if ($transac) {
                pg_query(ConnectPG(), 'rollback');
            }
            $msg = $e->getMessage();
            Alert($msg);
        }
    }

    $consultar = '';
    if (isset($_POST['btConsultar'])) {
        $acao = ACAO_CONSULTAR;
        $param = trim($_POST['campoConsultar']);
        $consultar = sprintf(" AND a.nome LIKE %s OR b.descricao LIKE %s ", PrepararLike($param), PrepararLike($param));
    }

    if (isset($_POST['btConsultar'])) {
        $sql = "SELECT  a.id_funcionario,a.id_cidade,a.nome, a.dt_nascimento AS \"data de nascimento\", a.email, a.dt_admissao AS admissao, b.descricao AS funcao, c.nome_cidade AS cidade
                FROM funcionario a
                INNER JOIN funcao b ON a.id_funcao = b.id_funcao
                INNER JOIN endereco c ON a.id_cidade = c.id_cidade
                WHERE id_funcionario = $id_funcionario";
    }

    $sql = "SELECT a.id_funcionario,a.id_cidade,a.nome, a.dt_nascimento AS \"data de nascimento\", a.email, a.dt_admissao AS admissao, b.descricao AS funcao, c.nome_cidade AS cidade
            FROM funcionario a
            INNER JOIN funcao b ON a.id_funcao = b.id_funcao
            INNER JOIN endereco c ON a.id_cidade = c.id_cidade
            WHERE 1=1 $consultar ORDER BY a.nome";

    $result = pg_query(ConnectPG(), $sql);

    $funcionarioLista = '';
    while ($row = pg_fetch_array($result,NULL,PGSQL_ASSOC)) {
        //print_r($row);
        $editar = sprintf('<a href="editaFuncionario.php?acao=%s&id_funcionario=%s&id_cidade=%s" title="Editar Funcionário"><img src="img/gear.png"/></a>', ACAO_ALTERAR, $row['id_funcionario'],$row['id_cidade']);

        $apagar = sprintf('<a href="listaFuncionario.php?acao=%s&id_funcionario=%s&id_cidade=%s"'
                . 'onclick="return confirm(\'Tem certeza que deseja excluir o funcionário %s?\')" title="Excluir Funcionário"><img src="img/x-button.png"/></a>', ACAO_APAGAR, $row['id_funcionario'], $row['id_cidade'],$row['nome']);
        
        $novo = sprintf('<a href="cadastraFuncionario.php?acao=%s" title="Novo Funcionário"><img src="img/add.png"/></a>',ACAO_INSERT);

        $funcionarioLista .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", 
        $row['nome'], date('d/m/Y', strtotime($row['data de nascimento'])), $row['email'], date('d/m/Y', strtotime($row['admissao'])), $row['funcao'], $row['cidade'], $novo, $apagar, $editar);
    }
} catch (Exception $e) {
    $mensagem = $e->getMessage();
    Alert($mensagem);
    //file_put_contents($fileLog, $mensagem . "\r\n", FILE_APPEND);
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <div class="main">
        <h2>Lista de Funcionários</h2>
        <form method="POST" action="listaFuncionario.php">
            <input type="hidden" name="acao" value="<?php echo $acao; ?>">
            <input type="hidden" name="id_usuario" value="<?php echo $id_funcionario; ?>">   
            <div class="campoPesquisa">
                Buscar Funcionário:
                <input type="text" name="campoConsultar" value="<?php echo $campoConsultar; ?>">
                <input type="submit" name="btConsultar" value="Consultar">
            </div>
            <table class="tabelaUsuario" border="3" cellspacing="4" cellpadding="4">
                <thead>
                    <tr>
                        <th>Nome</th><th>Data de Nascimento</th><th>email</th>
                        <th>Data de Admissão</th><th>Função</th><th>Cidade</th><th colspan="3">Controles</th>
                    </tr>
                </thead>
                <?php echo $funcionarioLista; ?>
            </table>
        </form>
        <br><hr>
        <input type="button" name="btVoltar" value="Menu Principal" onclick="javascript: location.href='index.php';">
        <!--<input type="button" name="btInserir" value="Novo Funcionário" onclick="javascript: location.href='cadastraFuncionario.php';">-->
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>

