<?php
include_once 'assets.php';

$idsetor = $_REQUEST['id_setor'];
$sql = sprintf("SELECT id_funcao,descricao FROM funcao WHERE id_setor = %s", $idsetor);
$result = pg_query(ConnectPG(), $sql);
while ($row = pg_fetch_assoc($result)) {
    echo "<option value='" . $row['id_funcao'] . "'>" . $row['descricao'] . "</option>";
}
?>
