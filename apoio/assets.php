<?php
//Constantes para as ações do sistema.
define("ACAO_INSERT", 'INSERIR');
define("ACAO_ALTERAR", 'ALTERAR');
define("ACAO_APAGAR", 'APAGAR');
define("ACAO_CONSULTAR", 'CONSULTAR');
define("ACAO_CANCELAR", 'CANCELAR');

//CAMINHO DO ARQUIVO DE LOG
$fileLog = '/var/log/log_exame.log';

/**
 * Conecta com o banco de dados
 * 
 * @return String retorna a string de conexão com o banco de dados.
 */
function ConnectPG() {
    $dbPort = "5432";
    $dbHost = "127.0.0.1";
    $dbName = "exame";
    $dbUser = "henrique";
    $dbPassword = "root";
    $str = "host='$dbHost' port='$dbPort' dbname='$dbName' user='$dbUser' password='$dbPassword'";
    return pg_connect($str);
}

/**
 * </b>Define um valor inicial para os campos dos formulários</b>
 * 
 * @param string $arFields Valores iniciais dos campos em branco
 * @return string $arInput valores que foram digitados nos campos 
 * 
 */
function ValorInicio($arFields) {
    $arInput = array();

    foreach ($arFields as $campo) {
        $arInput[$campo] = "";
    }
    return $arInput;
}

/**
 * <b>Valida os campos do formulario de cadastro</b>
 * 
 * @global String $obrigatorio
 * @global String $nome
 * @global Integer $cpf
 * @global Integer $rg
 * @global String $endereco
 * @throws Exception
 */
function validaForm() {
    global $obrigatorio, $nome, $cpf, $email, $cnpj;

    foreach ($obrigatorio as $campo) {

        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            throw new Exception(sprintf("Informe um valor válido para o campo %s", $campo));
        }
    }

    foreach ($email as $campo) {
        if (!validaEmail($_POST[$campo])) {
            throw new Exception(sprintf("%s inválido", $campo));
        }
    }

    foreach ($cpf as $campo) {
        if (!validaCpf($_POST[$campo])) {
            throw new Exception(sprintf(" %s inválido", $campo));
        }
    }
    
    foreach ($cnpj as $campo) {
        if (!validaCnpj($_POST[$campo])) {
            throw new Exception(sprintf("%s inválido", $campo));
        }
    }
}

/**
 * <b>Valida o email do funcionario inserido no formulario.</b>
 * 
 * @param String $string
 * @return boolean
 */
function validaEmail($string) {
    $string = trim($string);

    $ret = preg_match(
            '/^([a-z0-9_]|\\-|\\.)+' .
            '@' .
            '(([a-z0-9_]|\\-)+\\.)+' .
            '[a-z]{2,4}$/', $string);

    return($ret);
}

function GetSetor($setorId = '') {
    $sql = "SELECT id_setor,nome_setor FROM setor ORDER BY 1";
    $result = @pg_query(ConnectPG(), $sql);

    $sel = $setorId == '' ? 'selected="selected"' : '';

    $listaSetor = "<option value=\"\"$sel>-------</option>";
    while ($row = @pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $funcBanco = $row['id_setor'];
        $sel = ($setorId == $funcBanco) ? 'selected="selected"' : '';
        $listaSetor .= sprintf("<option value=\"%s\"%s>%s</option>", $row['id_setor'], $sel, $row['nome_setor']);
    }
    return $listaSetor;
}

function GetFuncao($setorId, $funcId = '') {
    
    if(!isset($setorId) || !$setorId){
        return "<option value=\"\" selected=\"selected\">--</option>";
    }
    
    $sql = sprintf("SELECT id_funcao,descricao FROM funcao WHERE id_setor = %s ORDER BY 1", $setorId);
    $result = @pg_query(ConnectPG(), $sql);

    $sel = $funcId == '' ? 'selected="selected"' : '';
    $funcaoLista = "<option value=\"\"$sel>-------</option>";
    while ($row = @pg_fetch_array($result, null, PGSQL_ASSOC)) {
        $funcBanco = $row['id_funcao'];
        $sel = ($funcId == $funcBanco) ? 'selected="selected"' : '';
        $funcaoLista .= sprintf("<option value=\"%s\"%s>%s</option>", $row['id_funcao'], $sel, $row['descricao']);
    }
    return $funcaoLista;
}

function GetAcao() {
    global $acao;
    return isset($acao) ? $acao : ACAO_CONSULTAR;
}

function Inserir() {
    return (GetAcao() == ACAO_INSERT);
}

function Alterar() {
    return (GetAcao() == ACAO_ALTERAR);
}

function Consulta() {
    return (GetAcao() == ACAO_CONSULTAR) || isset($_REQUEST['btConsultar']);
}

function Apagar() {
    return (GetAcao() == ACAO_APAGAR);
}

function Cadastrar() {
    return !Consulta() && (Inserir() || Alterar());
}

function Cancelar() {
    //return (GetAcao() == ACAO_CANCELAR);    
    return isset($_POST['btCancelar']);
}

function Gravar() {
    return isset($_POST['btEnviar']); 
}

/**
 * <b>Recebe um valor do campo de CPF e faz a verificação se o insert é um número 
 * válido</b>
 * 
 * @param String $cpf Valor inserido no campo de cpf 
 * @return boolean Retorna TRUE caso válido ou FALSE caso inválido
 */
function validaCpf($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

    if (strlen($cpf) != 11) {
        return false;
    } else if
    ($cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999') {
        return false;
    } else {
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
        return true;
    }
}

/**
 * <b>Recebe um valor do campo e faz a validação, verificando se o valor inserido
 * é um CPF válido</b>
 * 
 * @param String $cnpj valor inserido no campo
 * @return boolean retorna TRUE caso o CPF seja válido ou FALSE caso inválido
 */
function validaCnpj($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
    // Valida tamanho
    if (strlen($cnpj) != 14) {
        return false;
    }
    // Valida primeiro dígito verificador
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj{$i} * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    if ($cnpj{12} != ($resto < 2 ? 0 : 11 - $resto)) {
        return false;
    }
    // Valida segundo dígito verificador
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj{$i} * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    return $cnpj{13} == ($resto < 2 ? 0 : 11 - $resto);
}


function LimparDados() {
    $ar = count($_POST) ? $_POST : (count($_GET) ? $_GET : array());
    foreach ($ar as $name => $value) {
        $_POST[$name] = trim($value);
    }
}

/**
 * <b>Recebe uma string e adiciona aspas simples para utilizar nas querys SQL</b>
 * 
 * @param Char $str String Curinga 
 * @return String Retorna a string envolta em aspas
 * 
 */
function QuotedStr($str) {
    return sprintf("'%s'", str_replace("'", "''", $str));
}

/**
 * <b>Recebe uma string e a prepara para ser utilizada na função "LIKE" do SQL</b>
 * 
 * @param Char $str valor inserido no campo de busca
 * @return String Retorna a string Curinga pronta para ser adicionada na cláusula <b>Like</b>
 */
function PrepararLike($str) {
    return QuotedStr(sprintf("%%%s%%", ucwords($str)));
}
/**
 * <b>Função para limpar as string recebidas com máscara do form.</b>
 * 
 * @param String $str Recebe a string com máscara
 * 
 * @return String Retorna a String sem os caracteres especiais
 */
function limpaString($str) {

    $clear = preg_replace("/\D+/", "", $str);
    return $clear;
}

/**
 * <b>Função que recebe uma string de CPF vinda do banco de dados e insere a máscara
 * para apresentação no campo.</b>
 * 
 * @param String $param Recebe a string vinda do banco sem mascara. 
 * @return String Retorna a String de CPF formatada.
 */
function inputCpf($param) {
    
    $pattern = '/^([[:digit:]]{3})([[:digit:]]{3})([[:digit:]]{3})([[:digit:]]{2})$/';
    $replacement = '$1.$2.$3-$4';
    $format =  preg_replace($pattern, $replacement, $param);
    
    return $format;
}

/**
 * <b>Função que recebe uma string de Telefone vinda do banco de dados e insere a máscara
 * para apresentação no campo</b>
 * 
 * @param String $param Recebe a string vinda do banco sem máscara.
 * @return String Retorna a String de Telefone formatada.
 */
function inputFone($param) {
    
    $pattern = '/^([[:digit:]]{2})([[:digit:]]{5})([[:digit:]]{4})$/';
    $replacement = '($1)$2-$3';
    $format =  preg_replace($pattern, $replacement, $param);
    
    return $format;
}
