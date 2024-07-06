<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>email</title>
</head>

<body>
    <table width="450" border="0" cellpadding="2" cellspacing="1">
        <tr bgcolor="#778899">
            <td colspan="2">
                <font color="#FFFFFF" face="trebuchet ms" size="4">&nbsp;&nbsp;contato</font>
            </td>
        </tr>

        <?php while (list($campo, $conteudo) = each($data)) { ?>
            <tr bgcolor="#BEBEBE">
                <td width="131" bgcolor="#E4E4E4" valign="top">
                    <div align="right">
                        <font color="#000000" face="verdana" size="2"><?php echo $campo?></font>
                    </div>
                </td>
                <td width="304" bgcolor="#F4F4F4">
                    <font color="#000000" face="verdana" size="2"><?php echo $conteudo ?></font>
                </td>
            </tr>
        <?php } ?>

        <tr bgcolor="#708090">
            <td colspan="2">
                <div align="right">
                    <a href='" . SGW . "' target="_blank">
                        <font color="#FFFFFF" face="trebuchet ms" size="1">acesse: " . SGW . "</font>
                    </a>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>