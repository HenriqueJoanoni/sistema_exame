<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';
$id_laboratorio = 0;
$id_cidade = 0;

$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR;
try {
    $campoConsultar = isset($_POST['btConsultar']) ? $_POST['campoConsultar'] : '';

    if (Apagar()) {

        try {
            $transac = 0;
            $id_laboratorio = $_GET['id_laboratorio'];
            $id_cidade = $_GET['id_cidade'];
            
            $result = pg_query(ConnectPG(), 'begin');
            if (!$result) {throw new Exception("Não foi possível iniciar a transação");}
            $transac = 1;

            $sql = sprintf("DELETE FROM endereco WHERE id_cidade = %s",$id_cidade);
            $result = pg_query(ConnectPG(), $sql);
            if(!$result){throw new Exception("Não foi possível excluir este endereço");}
            
            $sql = sprintf("DELETE FROM laboratorio WHERE id_laboratorio = %s", $id_laboratorio);
            $result = pg_query(ConnectPG(),$sql);
            if (!$result) {throw new Exception("Não foi possivel excluir esta clínica!");}

            $result = pg_query(ConnectPG(), 'commit');
            if (!$result) {throw new Exception("Não foi possivel concluir a transação");}
            
            echo "<SCRIPT type='text/javascript'> 
                        alert('Clínica Excluída!');
                        window.location.replace(\"listaClinica.php\");
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
        $consultar = sprintf(" AND a.nome_lab LIKE %s OR b.cep LIKE %s", PrepararLike($param), str_replace('-', '', PrepararLike($param)));
    }

    if (isset($_POST['btConsultar'])) {
        $sql = "SELECT a.id_laboratorio, b.id_cidade, a.nome_lab, a.telefone, a.cnpj, b.nome_cidade, b.logradouro, b.cep, b.rua, b.estado
	FROM laboratorio a
	INNER JOIN endereco b ON a.id_endereco = b.id_cidade
	WHERE a.id_laboratorio = $id_laboratorio";
    }

    $sql = "SELECT a.id_laboratorio, b.id_cidade, a.nome_lab, a.telefone, a.cnpj, b.nome_cidade, b.logradouro, b.cep, b.rua, b.estado
	FROM laboratorio a
	INNER JOIN endereco b ON a.id_endereco = b.id_cidade
	WHERE 1=1 $consultar ORDER BY 3";

    $result = pg_query(ConnectPG(), $sql);

    $listaLaboratorio = '';
    while ($row = pg_fetch_array($result,NULL,PGSQL_ASSOC)) {
        //print_r($row);exit;
        $editar = sprintf('<a href="editaClinica.php?acao=%s&id_laboratorio=%s&id_cidade=%s" title="Editar Clínica"><img src="img/gear.png"/></a>', ACAO_ALTERAR, $row['id_laboratorio'],$row['id_cidade']);

        $apagar = sprintf('<a href="listaClinica.php?acao=%s&id_laboratorio=%s&id_cidade=%s"'
                . 'onclick="return confirm(\'Tem certeza que deseja excluir a Clínica %s?\')" title="Excluir Clínica"><img src="img/x-button.png"/></a>', ACAO_APAGAR, $row['id_laboratorio'], $row['id_cidade'],$row['nome_lab']);
        
        $novo = sprintf('<a href="cadastraClinica.php?acao=%s" title="Nova Clínica"><img src="img/add.png"/></a>',ACAO_INSERT);

        $listaLaboratorio .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", 
        $row['nome_lab'], $row['nome_cidade'], inputFone($row['telefone']), inputCnpj($row['cnpj']), $row['estado'], $row['logradouro'], inputCep($row['cep']), $novo, $apagar, $editar);
    }
} catch (Exception $e) {
    $mensagem = $e->getMessage();
    Alert($mensagem);
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <div class="main">
        <h2>Lista de Clínicas</h2>
        <form method="POST" action="listaClinica.php">
            <input type="hidden" name="acao" value="<?php echo $acao; ?>">
            <input type="hidden" name="id_laboratorio" value="<?php echo $id_laboratorio; ?>">   
            <div class="campoPesquisa">
                Buscar Clínica:
                <input type="text" name="campoConsultar" value="<?php echo $campoConsultar; ?>">
                <input type="submit" name="btConsultar" value="Consultar">
            </div>
            <table class="tabelaUsuario" border="3" cellspacing="4" cellpadding="4">
                <thead>
                    <tr>
                        <th>Clínica</th><th>Cidade</th><th>Telefone</th>
                        <th>CNPJ</th><th>Estado</th><th>Bairro</th><th>CEP</th><th colspan="3">Controles</th>
                    </tr>
                </thead>
                <?php echo $listaLaboratorio; ?>
            </table>
        </form>
        <br><hr>
        <input type="button" name="btVoltar" value="Menu Principal" onclick="javascript: location.href='index.php';">
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>

