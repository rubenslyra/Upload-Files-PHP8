<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Arquivos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js">
    </script>
</head>

<body>

    <?php

    include("web-config.php");

    if (isset($_POST['upload'])) {
        
        $arquivo = $_FILES['arquivo'];
        $arquivo = $_FILES['arquivo'] ?? null;
        if ($arquivo == null) {
            die( "Selecione um arquivo");            
            exit;
        }
        if($arquivo['size'] > 300000){
            die( "Arquivo muito grande. Escolha um arquivo com até 3MB de tamanho.");
            exit;
        }
        $nomeOriginal = $arquivo['name'];
        $tmp = $arquivo['tmp_name'];
        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
        
        if (!in_array($extensao, ['jpg', 'jpeg', 'png'])) {
            die( "Escolha arquivos que tenham as extensões: jpg, jpeg ou png.");
            exit;
        }
        
        
        $destino = "destino/";
        $novoNome = uniqid();
        $path = $destino . $novoNome . "." . $extensao;        
        move_uploaded_file($tmp, $destino . $novoNome . "." . $extensao); 
        if (file_exists($destino)) {
            $mysqli->query(        
                "INSERT INTO $tbl(Name, NewName, Path) VALUES ('$nomeOriginal', '$novoNome.$extensao', '$path')") or die($mysqli->error);
               
            // $mysqli->close();
            // $arquivo = null;    
            echo "Arquivo enviado com sucesso!";                     
           
        } else {
            echo "Erro ao enviar o arquivo";
        }       


    }

    ?>

    <h1>Upload de Arquivos com PHP8</h1>
    <form enctype="multipart/form-data" action="" method="POST">       
        <input type="hidden" name="MAX_FILE_SIZE" value="300000">        
        <input type="file" name="arquivo" >
        <label for="">Selecione o arquivo</label>
        <input type="submit" name="upload" value="Enviar">
    </form>


     <h2>Arquivos Enviados</h2>   
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <td>Minuatura</td>
                <th>Nome</th>
                <th>Data de Envio</th>
            </tr>        
        </thead>
        <tbody>
        <?php
            $sql_query = mysqli_query($mysqli, "SELECT * FROM $tbl") or die($mysqli->error);
            while ($row = mysqli_fetch_array($sql_query)) {
        ?>
            <tr>
                <th><img src="<?php echo $row['Path']; ?>" alt="Imagem <?php echo $row['Name']; ?>" height="50"/></th>                    
                <td><a href="<?php echo $row['Path']; ?>" target="_blank"><?php echo $row['Name']; ?> </a> </td>
                <!-- Formatar a data para o formato brasileiro. Ex: 01/01/2021 | Lembrando que strtime só funciona com o formato americano -->
                <td> <?php echo date('d/m/Y H:i', strtotime($row['CreatedAt'])); ?> </td>
                <!-- <td> <?php echo $row['CreatedAt']; ?> </td> -->
            </tr>                    
        <?php  } ?>        
    </table>

    <!--  Fetch Association -->
    <?php
    /*
    $sql_query = mysqli_query($mysqli, "SELECT * FROM $tbl") or die($mysqli->error);
    while ($row = mysqli_fetch_assoc($sql_query)) {
        echo "<img src='" . $row['Path'] . "' width='100' height='100'>";
    }
    */
    ?>
   
</body>

</html>