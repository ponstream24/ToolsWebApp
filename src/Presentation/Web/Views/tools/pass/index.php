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
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypt - Decrypt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
</head>
<body>
    <nav class="navbar is-dark">
        <div class="navbar-brand">
            <a class="navbar-item" href="#">
                <h1 class="title has-text-white">Encrypt - Decrypt</h1>
            </a>
            <div class="navbar-burger" data-target="navMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="navMenu" class="navbar-menu">
            <div class="navbar-end">
                <a class="navbar-item has-text-white" href="https://aaaa.com">トップに戻る</a>
            </div>
        </div>
    </nav>

    <main class="section">
        <div class="container">
            <div class="columns">
                <div class="column is-half is-offset-one-quarter">
                    <div class="box">
                        <h2 class="title is-4">暗号化</h2>
                        <form action="" method="post">
                            <div class="field">
                                <label class="label" for="pass">テキスト</label>
                                <div class="control">
                                    <input id="pass" name="pass" class="input" type="text" value="<?= htmlspecialchars($pass ?? "") ?>">
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="solt">鍵</label>
                                <div class="control">
                                    <input id="solt" name="solt" class="input" type="text" value="<?= htmlspecialchars($solt ?? "") ?>">
                                </div>
                            </div>
                            <div class="field">
                                <div class="control">
                                    <button class="button is-link" type="submit">暗号化</button>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="encrypt">暗号文</label>
                                <div class="control">
                                    <input id="encrypt" name="encrypt" class="input" type="text" readonly value="<?= htmlspecialchars($encrypt ?? "") ?>">
                                </div>
                            </div>
                        </form>
                        <h2 class="title is-4">復号化</h2>
                        <form action="" method="post">
                            <div class="field">
                                <label class="label" for="encrypt">暗号文</label>
                                <div class="control">
                                    <input id="encrypt" name="encrypt" class="input" type="text" value="<?= htmlspecialchars($encrypt ?? "") ?>">
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="solt">鍵</label>
                                <div class="control">
                                    <input id="solt" name="solt" class="input" type="text" value="<?= htmlspecialchars($solt ?? "") ?>">
                                </div>
                            </div>
                            <div class="field">
                                <div class="control">
                                    <button class="button is-link" type="submit">復号化</button>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="pass">平文</label>
                                <div class="control">
                                    <input id="pass" name="pass" class="input" type="text" readonly value="<?= htmlspecialchars($pass ?? "") ?>">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer has-background-dark">
        <div class="content has-text-centered has-text-white-ter">
            <p>&copy; 2024 <a href="https://yukiytetsuka.com" class="has-text-white">YukiTetsuka</a>. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
            if ($navbarBurgers.length > 0) {
                $navbarBurgers.forEach(el => {
                    el.addEventListener('click', () => {
                        const target = el.dataset.target;
                        const $target = document.getElementById(target);
                        el.classList.toggle('is-active');
                        $target.classList.toggle('is-active');
                    });
                });
            }
        });
    </script>
</body>
</html>
