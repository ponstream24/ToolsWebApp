<?php

/**
 * ノーマル暗号化
 */
function encrypt($content, $solt){
    return openssl_encrypt($content, 'AES-128-ECB', $solt);
}

/**
 * ノーマル復号化
 */
function decrypt($content, $solt){
    return openssl_decrypt($content, 'AES-128-ECB', $solt);
}

$pass = null;
$solt = null;
$encrypt = null;

$data = array_merge($_GET, $_POST);

if( $data != null ){

    if( 
        isset($data["pass"]) &&
        isset($data["solt"])
    ){
        $pass = $data["pass"];
        $solt = $data["solt"];

        $encrypt = encrypt($pass, $solt);
    }

    else if( 
        isset($data["encrypt"]) &&
        isset($data["solt"])
    ){

        $encrypt = $data["encrypt"];
        $solt = $data["solt"];
        $pass = decrypt($encrypt, $solt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypt - Decrypt</title>
</head>
<body>
    <h2>暗号化</h2>
    <form action="" method="post">
        <label for="pass">
            テキスト<input id="pass" name="pass" type="text" value="<?=htmlspecialchars($pass ?? "")?>">
        </label>
        <label for="solt">
            鍵<input id="solt" name="solt" type="text" value="<?=htmlspecialchars($solt ?? "")?>">
        </label>
        <p><input type="submit" value="↓"></p>
        <label for="encrypt">
            暗号文<input type="text" readonly value="<?=htmlspecialchars($encrypt ?? "")?>">
        </label>
    </form>
    <h2>復号化</h2>
    <form action="" method="post">
        <label for="encrypt">
            暗号文<input id="encrypt" name="encrypt" type="text" value="<?=htmlspecialchars($encrypt ?? "")?>">
        </label>
        <label for="solt">
            鍵<input id="solt" name="solt" type="text" value="<?=htmlspecialchars($solt ?? "")?>">
        </label>
        <p><input type="submit" value="↓"></p>
        <label for="pass">
            平文<input type="text" readonly value="<?=htmlspecialchars($pass ?? "")?>">
        </label>
    </form>
</body>
</html>