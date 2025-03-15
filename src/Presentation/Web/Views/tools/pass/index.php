<?php
$pageTitle = 'パスワードジェネレーター | ' . \Infrastructure\Config\Config::get('app.title');
$additionalStyles = '';

$additionalScripts = '
document.addEventListener("DOMContentLoaded", function() {
    const generateBtn = document.getElementById("generate-btn");
    const passwordInput = document.getElementById("password");
    const lengthInput = document.getElementById("length");
    const lengthValue = document.getElementById("length-value");
    const copyBtn = document.getElementById("copy-btn");
    const strengthMeter = document.getElementById("strength-meter");
    const strengthText = document.getElementById("strength-text");
    
    // 長さスライダーの値を表示
    lengthInput.addEventListener("input", function() {
        lengthValue.textContent = this.value;
    });
    
    // パスワード生成
    generateBtn.addEventListener("click", async function() {
        const length = lengthInput.value;
        const uppercase = document.getElementById("uppercase").checked;
        const lowercase = document.getElementById("lowercase").checked;
        const numbers = document.getElementById("numbers").checked;
        const symbols = document.getElementById("symbols").checked;
        
        try {
            const response = await fetch("/ToolsWebApp/public/api/tool/password", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `length=${length}&uppercase=${uppercase}&lowercase=${lowercase}&numbers=${numbers}&symbols=${symbols}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                passwordInput.value = data.data;
                updatePasswordStrength(data.data);
            } else {
                alert(data.error);
            }
        } catch (error) {
            console.error("エラーが発生しました:", error);
            alert("パスワードの生成中にエラーが発生しました。");
        }
    });
    
    // パスワードの強度を評価
    function updatePasswordStrength(password) {
        let strength = 0;
        
        // 長さによる評価
        if (password.length >= 8) strength += 1;
        if (password.length >= 12) strength += 1;
        if (password.length >= 16) strength += 1;
        
        // 文字種による評価
        if (/[A-Z]/.test(password)) strength += 1;
        if (/[a-z]/.test(password)) strength += 1;
        if (/[0-9]/.test(password)) strength += 1;
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;
        
        // 強度に応じてメーターとテキストを更新
        let strengthClass = "";
        let strengthLabel = "";
        
        if (strength < 3) {
            strengthClass = "is-danger";
            strengthLabel = "弱い";
        } else if (strength < 5) {
            strengthClass = "is-warning";
            strengthLabel = "普通";
        } else if (strength < 7) {
            strengthClass = "is-success";
            strengthLabel = "強い";
        } else {
            strengthClass = "is-primary";
            strengthLabel = "非常に強い";
        }
        
        // クラスをリセットしてから新しいクラスを追加
        strengthMeter.className = "progress";
        strengthMeter.classList.add(strengthClass);
        
        // 値を設定
        strengthMeter.value = strength;
        strengthMeter.max = 7;
        strengthText.textContent = strengthLabel;
    }
    
    // コピーボタン
    copyBtn.addEventListener("click", function() {
        passwordInput.select();
        document.execCommand("copy");
        
        // コピー成功のフィードバック
        const originalText = this.textContent;
        this.textContent = "コピーしました！";
        this.classList.add("is-success");
        this.classList.remove("is-info");
        
        setTimeout(() => {
            this.textContent = originalText;
            this.classList.remove("is-success");
            this.classList.add("is-info");
        }, 2000);
    });
    
    // 初期パスワード生成
    generateBtn.click();
});
';

include __DIR__ . '/../../layouts/header.php';
?>

<div class="content-wrapper">
    <div class="container">
        <h1 class="title section-title animate-on-scroll" data-animation="animate__fadeInUp">パスワードジェネレーター</h1>
        
        <div class="columns">
            <div class="column is-8 is-offset-2">
                <div class="card animate-on-scroll" data-animation="animate__fadeInUp">
                    <div class="card-content">
                        <div class="content">
                            <p>安全なランダムパスワードを生成します。長さや使用する文字種を指定できます。</p>
                        </div>
                        
                        <div class="field">
                            <label class="label">生成されたパスワード</label>
                            <div class="field has-addons">
                                <div class="control is-expanded">
                                    <input id="password" class="input is-medium" type="text" readonly>
                                </div>
                                <div class="control">
                                    <button id="copy-btn" class="button is-info is-medium">
                                        <span class="icon">
                                            <i class="fas fa-copy"></i>
                                        </span>
                                        <span>コピー</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="field">
                            <label class="label">パスワードの強度</label>
                            <progress id="strength-meter" class="progress is-primary" value="0" max="7"></progress>
                            <p class="help">強度: <span id="strength-text">-</span></p>
                        </div>
                        
                        <div class="field">
                            <label class="label">パスワードの長さ: <span id="length-value">16</span>文字</label>
                            <div class="control">
                                <input id="length" class="slider is-fullwidth" step="1" min="8" max="128" value="16" type="range">
                            </div>
                        </div>
                        
                        <div class="field">
                            <label class="label">使用する文字</label>
                            <div class="control">
                                <label class="checkbox">
                                    <input id="uppercase" type="checkbox" checked>
                                    大文字 (A-Z)
                                </label>
                            </div>
                            <div class="control">
                                <label class="checkbox">
                                    <input id="lowercase" type="checkbox" checked>
                                    小文字 (a-z)
                                </label>
                            </div>
                            <div class="control">
                                <label class="checkbox">
                                    <input id="numbers" type="checkbox" checked>
                                    数字 (0-9)
                                </label>
                            </div>
                            <div class="control">
                                <label class="checkbox">
                                    <input id="symbols" type="checkbox" checked>
                                    記号 (!@#$%^&*()_+-=[]{}|;:,.<>?)
                                </label>
                            </div>
                        </div>
                        
                        <div class="field">
                            <div class="control">
                                <button id="generate-btn" class="button is-primary is-fullwidth">
                                    <span class="icon">
                                        <i class="fas fa-sync-alt"></i>
                                    </span>
                                    <span>パスワードを生成</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-5 animate-on-scroll" data-animation="animate__fadeInUp">
                    <div class="card-content">
                        <h2 class="title is-4">安全なパスワードのヒント</h2>
                        <div class="content">
                            <ul>
                                <li>パスワードは少なくとも12文字以上にしましょう。</li>
                                <li>大文字、小文字、数字、記号をすべて含めるとより強力になります。</li>
                                <li>個人情報（名前、誕生日など）を含めないようにしましょう。</li>
                                <li>同じパスワードを複数のサイトで使い回さないようにしましょう。</li>
                                <li>定期的にパスワードを変更することをお勧めします。</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
