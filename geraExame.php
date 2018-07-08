<?php
include 'apoio/assets.php';
include 'apoio/mensagens.php';



?>
<html>
    <?php include 'apoio/header.php'; ?>
    <h2>Gerador de  Exame</h2>
    <div class="body">
        <div class="geraExame">
            <div class="mapa">
                <iframe width='500' scrolling='no' height='500' frameborder='0' id='map' 
                        marginheight='0' marginwidth='0' src='https://maps.google.com/maps?saddr=Edificio Mirante do Coxim, 901, Av. Isaac P%C3%B3voas
                        &daddr=HISMET&output=embed'></iframe>
            </div>
            <div class="formExame">
                <table>
                    <tr>
                        <td>Testando</td>
                        <td><input type="text" size="16" name="teste"/></td>
                    </tr>
                    <tr>
                        <td>Testando</td>
                        <td><input type="text" size="16" name="teste"/></td>
                    </tr>
                    <tr>
                        <td>Testando</td>
                        <td><input type="text" size="16" name="teste"/></td>
                    </tr>
                    <tr>
                        <td>Testando</td>
                        <td><input type="text" size="16" name="teste"/></td>
                    </tr>
                    <tr>
                        <td>Testando</td>
                        <td><input type="text" size="16" name="teste"/></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="botoes">
            <input type="button" name="btCancelar" value="Cancelar" onclick="javascript: location.href = 'index.php';">
            <input type="submit" name="btEnviar" value="Gerar Exame">
        </div>
    </div>        
    <?php include 'apoio/footer.php'; ?>
</html>
