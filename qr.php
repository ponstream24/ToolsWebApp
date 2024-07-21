<?php
require 'vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Color\Color;

// パラメータの取得
$type = isset($_GET['type']) ? $_GET['type'] : null;
$size = isset($_GET['size']) ? (int)$_GET['size'] : 300;
$errorCorrectionLevel = isset($_GET['ecc']) ? strtoupper($_GET['ecc']) : 'L';
$color = isset($_GET['color']) ? $_GET['color'] : '#000000';
$rounding = isset($_GET['rounding']) ? (int)$_GET['rounding'] : 0;

// データの組み立て
$data = null;
switch ($type) {
    case 'text':
        $data = isset($_GET['text-data']) ? $_GET['text-data'] : '';
        break;
    case 'url':
        $data = isset($_GET['url-data']) ? $_GET['url-data'] : '';
        break;
    case 'tel':
        $data = isset($_GET['tel-data']) ? "tel:" . $_GET['tel-data'] : '';
        break;
    case 'wifi':
        $ssid = isset($_GET['ssid']) ? $_GET['ssid'] : null;
        $password = isset($_GET['password']) ? $_GET['password'] : null;
        $encryption = isset($_GET['encryption']) ? $_GET['encryption'] : 'WPA';
        if ($ssid && $password) {
            $data = "WIFI:T:$encryption;S:$ssid;P:$password;;";
        }
        break;
    case 'mailto':
        $email = isset($_GET['email']) ? $_GET['email'] : null;
        if ($email) {
            $data = "mailto:$email";
        }
        break;
    case 'geo':
        $lat = isset($_GET['lat']) ? $_GET['lat'] : null;
        $lon = isset($_GET['lon']) ? $_GET['lon'] : null;
        if ($lat && $lon) {
            $data = "geo:$lat,$lon";
        }
        break;
    default:
        break;
}

// 誤り訂正レベルの設定
switch ($errorCorrectionLevel) {
    case 'M':
        $ecc = new ErrorCorrectionLevelMedium();
        break;
    case 'Q':
        $ecc = new ErrorCorrectionLevelQuartile();
        break;
    case 'H':
        $ecc = new ErrorCorrectionLevelHigh();
        break;
    case 'L':
    default:
        $ecc = new ErrorCorrectionLevelLow();
        break;
}

// データが指定されていない場合は、ジェネレーターを表示
if (!$data) {
    echo '<!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>QRコードジェネレーター</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const typeSelector = document.getElementById("type");
                const inputFields = document.querySelectorAll(".input-field");

                // デフォルトのデータタイプをtextに設定
                typeSelector.value = "text";
                document.getElementById("text").style.display = "block";

                typeSelector.addEventListener("change", function() {
                    inputFields.forEach(field => field.style.display = "none");
                    const selectedType = typeSelector.value;
                    if (selectedType) {
                        document.getElementById(selectedType).style.display = "block";
                    }
                });

                document.querySelector("form").addEventListener("submit", function(event) {
                    // 空のパラメータを削除
                    const formElements = event.target.elements;
                    for (let i = 0; i < formElements.length; i++) {
                        const element = formElements[i];
                        if (element.type !== "submit" && !element.value) {
                            element.name = "";  // 空の名前を設定してパラメータを送信しないようにする
                        }
                    }
                });
            });
        </script>
    </head>
    <body>
        <section class="section">
            <div class="container">
                <h1 class="title">QRコードジェネレーター</h1>
                <form action="" method="get">
                    <div class="field">
                        <label class="label" for="type">データタイプ</label>
                        <div class="control">
                            <div class="select">
                                <select id="type" name="type" required>
                                    <option value="text">テキスト</option>
                                    <option value="url">URL</option>
                                    <option value="tel">電話番号</option>
                                    <option value="wifi">Wi-Fi</option>
                                    <option value="mailto">メールアドレス</option>
                                    <option value="geo">地図</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="url" class="field input-field" style="display:none;">
                        <label class="label" for="url-input">URL</label>
                        <div class="control">
                            <input class="input" type="url" id="url-input" name="url-data" placeholder="https://example.com">
                        </div>
                    </div>
                    <div id="text" class="field input-field" style="display:block;">
                        <label class="label" for="text-input">テキスト</label>
                        <div class="control">
                            <input class="input" type="text" id="text-input" name="text-data" placeholder="テキストを入力">
                        </div>
                    </div>
                    <div id="tel" class="field input-field" style="display:none;">
                        <label class="label" for="tel-input">電話番号</label>
                        <div class="control">
                            <input class="input" type="tel" id="tel-input" name="tel-data" placeholder="090-1234-5678">
                        </div>
                    </div>
                    <div id="wifi" class="input-field" style="display:none;">
                        <div class="field">
                            <label class="label" for="ssid-input">SSID</label>
                            <div class="control">
                                <input class="input" type="text" id="ssid-input" name="ssid" placeholder="Wi-Fi SSIDを入力">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label" for="password-input">パスワード</label>
                            <div class="control">
                                <input class="input" type="text" id="password-input" name="password" placeholder="Wi-Fiパスワードを入力">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label" for="encryption-input">暗号化タイプ</label>
                            <div class="control">
                                <div class="select">
                                    <select id="encryption-input" name="encryption">
                                        <option value="WPA">WPA/WPA2</option>
                                        <option value="WEP">WEP</option>
                                        <option value="nopass">なし</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="mailto" class="field input-field" style="display:none;">
                        <label class="label" for="email-input">メールアドレス</label>
                        <div class="control">
                            <input class="input" type="email" id="email-input" name="email" placeholder="example@example.com">
                        </div>
                    </div>
                    <div id="geo" class="input-field" style="display:none;">
                        <div class="field">
                            <label class="label" for="lat-input">緯度</label>
                            <div class="control">
                                <input class="input" type="text" id="lat-input" name="lat" placeholder="35.6895">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label" for="lon-input">経度</label>
                            <div class="control">
                                <input class="input" type="text" id="lon-input" name="lon" placeholder="139.6917">
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="size">サイズ</label>
                        <div class="control">
                            <input class="input" type="number" id="size" name="size" value="300" min="100" max="1000">
                        </div>
                    </div>
                    <div class="field">
                        <label class="label" for="color">色</label>
                        <div class="control">
                            <input class="input" type="color" id="color" name="color" value="#000000">
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button class="button is-link" type="submit">生成</button>
                        </div>
                    </div>
                </form>
                <a href="https://tools.yukitetsuka.com" class="button is-light">トップに戻る</a>
            </div>
        </section>
        <footer class="footer">
            <div class="content has-text-centered">
                <p>&copy; 2024 <a href="https://yukitetsuka.com">YukiTetsuka</a>. All rights reserved.</p>
            </div>
        </footer>
    </body>
    </html>';
    exit;
}

// QRコードの生成
$result = Builder::create()
    ->data($data)
    ->size($size)
    ->margin(10)
    ->errorCorrectionLevel($ecc)
    ->roundBlockSizeMode(new RoundBlockSizeModeMargin($rounding))
    ->foregroundColor(new Color(hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2))))
    ->build();

header('Content-Type: ' . $result->getMimeType());
echo $result->getString();
?>
