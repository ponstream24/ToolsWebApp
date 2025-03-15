<?php
$pageTitle = 'QRコードジェネレーター | ' . \Infrastructure\Config\Config::get('app.title');
$additionalStyles = '
/* QRコードジェネレーター用スタイル */
';

$additionalScripts = '
document.addEventListener("DOMContentLoaded", function() {
    const typeSelector = document.getElementById("type");
    const inputGroups = {
        "text": document.getElementById("text-input"),
        "url": document.getElementById("url-input"),
        "tel": document.getElementById("tel-input"),
        "wifi": document.getElementById("wifi-input"),
        "mailto": document.getElementById("mailto-input"),
        "geo": document.getElementById("geo-input")
    };

    function showSelectedInput(selectedType) {
        Object.values(inputGroups).forEach(group => {
            if (group) group.style.display = "none";
        });
        if (inputGroups[selectedType]) {
            inputGroups[selectedType].style.display = "block";
        }
    }

    typeSelector.addEventListener("change", function() {
        showSelectedInput(this.value);
    });

    showSelectedInput("text");
    
    // アニメーション
    const animateElements = document.querySelectorAll(".animate-on-scroll");
    
    function checkIfInView() {
        animateElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add("animate__animated", element.dataset.animation);
            }
        });
    }
    
    window.addEventListener("scroll", checkIfInView);
    checkIfInView();
});

document.getElementById("qrForm").addEventListener("submit", async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const resultDiv = document.getElementById("qrResult");
    
    try {
        resultDiv.innerHTML = \'<div class="has-text-centered"><button class="button is-loading">生成中...</button></div>\';
        
        const response = await fetch("/ToolsWebApp/public/api/tool/qr", {
            method: "POST",
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            resultDiv.innerHTML = `
                <div class="card">
                    <div class="card-content has-text-centered">
                        <img src="${data.data}" alt="生成されたQRコード" class="image is-fullwidth" style="max-width: 300px; margin: 0 auto;">
                        <div class="mt-3">
                            <a href="${data.data}" download="qrcode.png" class="button is-success mt-4">
                                <span class="icon">
                                    <i class="fas fa-download"></i>
                                </span>
                                <span>ダウンロード</span>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        } else {
            throw new Error(data.error);
        }
    } catch (error) {
        resultDiv.innerHTML = `
            <div class="notification is-danger">
                <span class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </span>
                エラー: ${error.message}
            </div>
        `;
    }
});
';

include __DIR__ . '/../../layouts/header.php';
?>

<div class="content-wrapper">
    <div class="container">
        <h1 class="title section-title animate-on-scroll" data-animation="animate__fadeInUp">QRコードジェネレーター</h1>

        <div class="columns">
            <div class="column is-half animate-on-scroll" data-animation="animate__fadeInLeft">
                <div class="card">
                    <div class="card-content">
                        <form id="qrForm" class="mb-3">
                            <div class="field">
                                <label for="type" class="label">データタイプ</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="type" name="type">
                                            <option value="text" selected>テキスト</option>
                                            <option value="url">URL</option>
                                            <option value="tel">電話番号</option>
                                            <option value="wifi">Wi-Fi</option>
                                            <option value="mailto">メールアドレス</option>
                                            <option value="geo">地図</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="text-input" class="field">
                                <div class="control">
                                    <textarea class="textarea" name="text-data" placeholder="テキストを入力" rows="3"></textarea>
                                </div>
                            </div>

                            <div id="url-input" class="field" style="display: none;">
                                <div class="control">
                                    <input type="url" class="input" name="url-data" placeholder="https://example.com">
                                </div>
                            </div>

                            <div id="tel-input" class="field" style="display: none;">
                                <div class="control">
                                    <input type="tel" class="input" name="tel-data" placeholder="090-1234-5678">
                                </div>
                            </div>

                            <div id="wifi-input" class="field" style="display: none;">
                                <div class="field">
                                    <div class="control">
                                        <input type="text" class="input" name="ssid" placeholder="Wi-Fi SSID">
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <input type="text" class="input" name="password" placeholder="パスワード">
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <div class="select is-fullwidth">
                                            <select name="encryption">
                                                <option value="WPA">WPA/WPA2</option>
                                                <option value="WEP">WEP</option>
                                                <option value="nopass">暗号化なし</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="mailto-input" class="field" style="display: none;">
                                <div class="control">
                                    <input type="email" class="input" name="email" placeholder="example@example.com">
                                </div>
                            </div>

                            <div id="geo-input" class="field" style="display: none;">
                                <div class="field">
                                    <div class="control">
                                        <input type="text" class="input" name="lat" placeholder="緯度 (例: 35.6895)">
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control">
                                        <input type="text" class="input" name="lon" placeholder="経度 (例: 139.6917)">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="field">
                                <label for="size" class="label">サイズ (px)</label>
                                <div class="control">
                                    <input type="number" class="input" id="size" name="size" value="300" min="100" max="1000">
                                </div>
                            </div>
                            
                            <div class="field">
                                <label for="margin" class="label">マージン (px)</label>
                                <div class="control">
                                    <input type="number" class="input" id="margin" name="margin" value="10" min="0" max="50">
                                </div>
                            </div>
                            
                            <div class="field">
                                <label for="errorCorrection" class="label">エラー訂正レベル</label>
                                <div class="control">
                                    <div class="select is-fullwidth">
                                        <select id="errorCorrection" name="errorCorrection">
                                            <option value="low">低 (L)</option>
                                            <option value="medium" selected>中 (M)</option>
                                            <option value="quartile">高 (Q)</option>
                                            <option value="high">最高 (H)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="field">
                                <label for="color" class="label">QRコードの色</label>
                                <div class="control">
                                    <input type="color" class="input" id="color" name="color" value="#000000">
                                </div>
                            </div>
                            
                            <div class="field">
                                <div class="control">
                                    <button type="submit" class="button is-primary">QRコードを生成</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="column is-half animate-on-scroll" data-animation="animate__fadeInRight">
                <div id="qrResult"></div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>