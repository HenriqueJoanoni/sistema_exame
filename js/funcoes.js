function mascara(o, f) {
    v_obj = o,
            v_fun = f,
            setTimeout("execmascara()", 1);
}

function execmascara() {
    v_obj.value = v_fun(v_obj.value);
}

function cpfMask(v) {
    v = v.replace(/\D/g, ""),
            v = v.replace(/(\d{3})(\d)/, "$1.$2"),
            v = v.replace(/(\d{3})(\d)/, "$1.$2"),
            v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    return v;
}
function cnpjMask(v) {
    v = v.replace(/\D/g, ""),
            v = v.replace(/^(\d{2})(\d)/, "$1.$2"),
            v = v.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3"),
            v = v.replace(/\.(\d{3})(\d)/, ".$1/$2"),
            v = v.replace(/(\d{4})(\d)/, "$1-$2");
    return v;
}

function mascaraData(val) {
    var pass = val.value;
    var expr = /[0123456789]/;

    for (i = 0; i < pass.length; i++) {
        // charAt -> retorna o caractere posicionado no índice especificado
        var lchar = val.value.charAt(i);
        var nchar = val.value.charAt(i + 1);

        if (i == 0) {
            // search -> retorna um valor inteiro, indicando a posição do inicio da primeira
            // ocorrência de expReg dentro de instStr. Se nenhuma ocorrencia for encontrada o método retornara -1
            // instStr.search(expReg);
            if ((lchar.search(expr) != 0) || (lchar > 3)) {
                val.value = "";
            }

        } else if (i == 1) {

            if (lchar.search(expr) != 0) {
                // substring(indice1,indice2)
                // indice1, indice2 -> será usado para delimitar a string
                var tst1 = val.value.substring(0, (i));
                val.value = tst1;
                continue;
            }

            if ((nchar != '/') && (nchar != '')) {
                var tst1 = val.value.substring(0, (i) + 1);

                if (nchar.search(expr) != 0)
                    var tst2 = val.value.substring(i + 2, pass.length);
                else
                    var tst2 = val.value.substring(i + 1, pass.length);

                val.value = tst1 + '/' + tst2;
            }

        } else if (i == 4) {

            if (lchar.search(expr) != 0) {
                var tst1 = val.value.substring(0, (i));
                val.value = tst1;
                continue;
            }

            if ((nchar != '/') && (nchar != '')) {
                var tst1 = val.value.substring(0, (i) + 1);

                if (nchar.search(expr) != 0)
                    var tst2 = val.value.substring(i + 2, pass.length);
                else
                    var tst2 = val.value.substring(i + 1, pass.length);

                val.value = tst1 + '/' + tst2;
            }
        }

        if (i >= 6) {
            if (lchar.search(expr) != 0) {
                var tst1 = val.value.substring(0, (i));
                val.value = tst1;
            }
        }
    }

    if (pass.length > 10)
        val.value = val.value.substring(0, 10);
    return true;
}
stop = '';
function telmask(campo) {
    campo.value = campo.value.replace(/[^\d]/g, '')
            .replace(/^(\d\d)(\d)/, '($1) $2')
            .replace(/(\d{5})(\d)/, '$1-$2');
    if (campo.value.length > 15)
        campo.value = stop;
    else
        stop = campo.value;
}