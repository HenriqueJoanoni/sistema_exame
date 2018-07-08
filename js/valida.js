function null_or_empty(str) {
    var v = document.getElementById(str).value;
    return v == null || v == "";
}

function valida_form() {
    if (null_or_empty("paramLogin") && null_or_empty("paramSenha"))
    {
        alert('Todos os campos devem ser preenchidos!');
        return false;
    }
    return true;
}