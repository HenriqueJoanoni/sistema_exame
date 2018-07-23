<?php
@session_start();
// VERIFICA SE TODAS AS SESSÕES ESTÃO SETADAS
if (isset($_SESSION['IDlogin']) && isset($_SESSION['paramLogin']) && isset($_SESSION['paramSenha'])) {
    // COLOCA AS SESSÕES EM VARIAVEIS PARA SEREM CONSULTADAS EM QUALQUER LUGAR
    $IDlogin = $_SESSION['IDlogin'];
    $Login = $_SESSION['paramLogin'];
    $Senha = $_SESSION['paramSenha'];
} else {
    session_unset();
    session_destroy();
    header("Location: login.php");
}