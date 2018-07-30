<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';
$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR;
try {
    $campoConsultar = isset($_POST['btConsultar']) ? $_POST['campoConsultar'] : '';

    if (!isset($_POST['btConsultar'])) {

        $hint = "<strong>Digite o nome de algum funcionário e clique em consultar!</strong>";
        
    } elseif (isset($_POST['btConsultar']) && $campoConsultar == "") {
      
        $hint = "<strong>Por favor, Informe um funcionário para consultar! </strong>";
                
    }else{
        $sql = sprintf("SELECT a.id_historico_funcional AS historico,a.dt_inicio AS data_pedido,a.dt_fim AS data_realizado,b.nome,
                        c.descricao AS funcao,d.tipo_exame, d.exame_descricao, e.nome_lab
                            FROM historico_funcional a
                            LEFT JOIN funcionario b ON a.id_funcionario = b.id_funcionario
                            LEFT JOIN funcao c ON a.id_funcao = c.id_funcao
                            RIGHT JOIN exame d ON a.id_exame = d.id_exame
                            INNER JOIN laboratorio e ON a.id_laboratorio = e.id_laboratorio
                                WHERE b.nome like %s", PrepararLike($campoConsultar));
        $result = pg_query(ConnectPG(), $sql);

        $historicoLista = '';
        while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
            //print_r($row);
            $editar = sprintf('<a href="editaHistoricFunc.php?acao=%s&id_historico=%s" title="Editar Histórico"><img src="img/gear.png"/></a>', ACAO_ALTERAR, $row['historico']);

            $apagar = sprintf('<a href="historicoFuncional.php?acao=%s&id_historico=%s"'
                    . 'onclick="return confirm(\'Tem certeza que deseja excluir este histórico ?\')" title="Excluir Histórico"><img src="img/x-button.png"/></a>', ACAO_APAGAR, $row['historico']);

            $historicoLista .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", 
                    $row['historico'], date('d/m/Y', strtotime($row['data_pedido'])), $row['data_realizado'] == null ? 'Pendente' : date('d/m/Y', strtotime($row['data_realizado'])), 
                    $row['nome'], $row['funcao'], $row['tipo_exame'], $row['exame_descricao'],$row['nome_lab'], $editar, $apagar);
        }
    }
    if(Apagar()){
        try {
            $transac = 0;
            @$id_historico = $_GET['id_historico'];

            $result = pg_query(ConnectPG(), 'begin');
            if (!$result) {throw new Exception("Não foi possível iniciar a transação");}
            $transac = 1;

            $sql = sprintf("DELETE FROM historico_funcional WHERE id_historico_funcional = %s ", @$id_historico);
            $result = pg_query(ConnectPG(), $sql);
            if (!$result) {throw new Exception("Não foi possivel excluir este funcionário");}
            
            $result = pg_query(ConnectPG(), 'commit');
            if (!$result) {throw new Exception("Não foi possivel concluir a transação");}

            Alert("Histórico Excluído!");
            
            $sql = sprintf("SELECT a.id_historico_funcional AS historico,a.dt_inicio AS data_pedido,a.dt_fim AS data_realizado,b.nome,
                        c.descricao AS funcao,d.tipo_exame, d.exame_descricao, e.nome_lab
                            FROM historico_funcional a
                            LEFT JOIN funcionario b ON a.id_funcionario = b.id_funcionario
                            LEFT JOIN funcao c ON a.id_funcao = c.id_funcao
                            RIGHT JOIN exame d ON a.id_exame = d.id_exame
                            INNER JOIN laboratorio e ON a.id_laboratorio = e.id_laboratorio
                                WHERE b.nome like %s", PrepararLike($campoConsultar));
            $result = pg_query(ConnectPG(), $sql);

            $historicoLista = '';
            while ($row = pg_fetch_array($result, null, PGSQL_ASSOC)) {
                //print_r($row);
                $editar = sprintf('<a href="editaHistoricFunc.php?acao=%s&id_historico=%s" title="Editar Histórico"><img src="img/gear.png"/></a>', ACAO_ALTERAR, $row['historico']);

                $apagar = sprintf('<a href="historicoFuncional.php?acao=%s&id_historico=%s"'
                        . 'onclick="return confirm(\'Tem certeza que deseja excluir este histórico ?\')" title="Excluir Histórico"><img src="img/x-button.png"/></a>', ACAO_APAGAR, $row['historico']);

                $historicoLista .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>", 
                $row['historico'], date('d/m/Y', strtotime($row['data_pedido'])), date('d/m/Y', strtotime($row['data_realizado'])), $row['nome'], $row['funcao'], 
                $row['tipo_exame'], $row['exame_descricao'], $row['nome_lab'], $editar, $apagar);
            }
        } catch (Exception $e) {
            if ($transac) {
                pg_query(ConnectPG(), 'rollback');
            }
            $msg = $e->getMessage();
            Alert($msg);
        }
    }
    
} catch (Exception $ex) {
    $msg = $ex->getMessage();
    Alert($msg);
}
?>
<html>
    <?php include 'apoio/header.php'; ?>
    <div class="main">
        <h2>Histórico Funcional de Funcionários</h2>
        <form method="POST" action="historicoFuncional.php">
            <input type="hidden" name="acao" value="<?php echo $acao; ?>">
            <input type="hidden" name="acao" value="<?php echo @$id_historico; ?>">
            <div class="campoPesquisa">
                Buscar Funcionário:
                <input type="text" name="campoConsultar" value="<?php echo $campoConsultar; ?>">
                <input type="submit" name="btConsultar" value="Consultar">
            </div>
            <table class="tabelaUsuario" border="3" cellspacing="4" cellpadding="4">
                <thead>
                    <tr>
                        <th>ID Histórico</th><th>Data do Pedido</th><th>Data de Realização</th><th>Nome</th><th>Função</th><th>Tipo do Exame</th>
                        <th>Descrição do Exame</th><th>Laboratório</th><th colspan="2">Controles</th>
                    </tr>
                </thead>
                <?php 
                    if(!@$historicoLista){
                        echo $hint;
                    }else{
                        echo @$historicoLista;
                    }
                ?>
            </table>
        </form>
        <br><hr>
        <input type="button" name="btVoltar" value="Menu Principal" onclick="javascript: location.href='index.php';">
    </div>
    <?php include 'apoio/footer.php'; ?>
</html>