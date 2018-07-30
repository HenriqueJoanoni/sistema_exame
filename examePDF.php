<?php
require_once 'vendor/autoload.php';
include_once 'apoio/assets.php';
include_once 'apoio/mensagens.php';

ob_start();
$acao = isset($_POST['btCancelar']) ? ACAO_CONSULTAR : (isset($_REQUEST['acao']) ? $_REQUEST['acao'] : ACAO_CONSULTAR);
$idFuncionario = 0;

@$idFuncionario = $_POST['id_funcionario'];

$idLab = explode("|",$_POST['id_laboratorio']);
$exame = explode("|", $_POST['id_exame']);
$funcao = explode("|", $_POST['id_funcao']);

 if (Gravar()) {
    try {
        $dataIni = date('Y-m-d');
        
        $transac = 0;
        $result = pg_query(ConnectPG(), 'begin');
        if (!$result) {throw new Exception("Não foi possível iniciar a Transação!");}
        $transac = 1;
        
        $sql = sprintf("UPDATE exame SET id_laboratorio = %s ,dt_solicitacao = %s WHERE id_exame = %s;",$idLab[0], QuotedStr($dataIni),$exame[0]);
        $result = pg_query(ConnectPG(),$sql);
        if(!$result){throw new Exception("Não foi possível cadastrar o registro do exame!");}
        
        $sql = sprintf("INSERT INTO historico_funcional (id_funcionario,id_funcao,id_exame,dt_inicio,id_laboratorio) 
                        VALUES (%s,%s,%s,%s,%s);", $idFuncionario, $funcao[0], $exame[0],QuotedStr($dataIni),$idLab[0]);
        $result = pg_query(ConnectPG(),$sql);
        if(!$result){throw new Exception("Não foi possível registrar este histórico!");}

        $result = pg_query(ConnectPG(), 'commit');
        if (!$result) {throw new Exception("Não foi possível finalizar a transação");}

        if (!$result) {
            throw new Exception("Não foi possível fazer a geração deste exame!");
        }
        echo "<SCRIPT type='text/javascript'> 
                        alert('Exame Gerado com Sucesso!');
                        window.location.replace(\"listaFuncionario.php\");
                  </SCRIPT>";
    } catch (Exception $ex) {
        if ($transac) {
            pg_query(ConnectPG(), 'rollback');
        }
        $msg = $ex->getMessage();
        Alert($msg);
        exit();
    }
}
?>
<head>
    <link rel="stylesheet" href="css/stylePdf.css">
</head>
<body>
    <div class="topo">
        <div class="imagem">
            <img src="img/logo_simplesip.png" alt="Simples IP">
        </div>
        <div class="toporight">
            <b>Cuiabá - <?php echo date('d M Y')?></b>
        </div>
    </div>
    <div class="titulo">
        <h1 align="center">ENCAMINHAMENTO PARA EXAME</h1>
    </div>
    <div class="titulo2">
        <h2 align="left">Empregador: Simples IP Comércio e serviços de Tecnologia da Informação ltda.</h2>
        <h2>CNPJ: 13.157.305/0001-53</h2>
    </div>
    <div class="corposolicita">
        <b>Solicitação de Exame <?php echo $exame[1]; ?>.<br><br>
        <b>Empregado(a): <?php echo $_POST['nome'];?></b><br><br>
        <b>Na função de: <?php echo $funcao[1]; ?></b>
    </div>
    <div class="corposolicita2">
        <b>O exame deve ser realizado em:</b><br>
        <b><?php echo $idLab[1]; ?></b>
    </div>
</body>
<?php
// armazena o html no cache
$html = ob_get_contents();
ob_end_clean();

// define um nome randomico para o arquivo pdf
//$arquivo = md5(time().rand(0, 999)).'.pdf';
$arquivo = $_POST['nome']."_".$exame[1].'.pdf';

// define uma pasta para os arquivos temporários (necessário permissão de escrita no linux)
$mpdf = new Mpdf\Mpdf(['tempDir' => __DIR__.'/tmp/custom']);
$mpdf->WriteHTML($html); // prepara a escrita do html no arquivo
$mpdf->Output($arquivo,'D'); // transforma o html e apresenta o arquivo de acordo com as diretivas descritas abaixo.

// I = abre no browser
// D = faz download do arquivo
// F = Salva no servidor (necessário permissão no linux)
?>


