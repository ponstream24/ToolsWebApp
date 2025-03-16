<?php
$pageTitle = 'TOTP Generator';
$additionalScripts = '
// タブ切り替え
document.querySelectorAll(".tabs li").forEach(tab => {
    tab.addEventListener("click", () => {
        document.querySelectorAll(".tabs li").forEach(t => t.classList.remove("is-active"));
        document.querySelectorAll(".tabs-content > div").forEach(c => c.classList.remove("is-active"));
        
        tab.classList.add("is-active");
        document.getElementById(`${tab.dataset.tab}-tab`).classList.add("is-active");
    });
});

// 新規TOTP生成
document.getElementById("generate-btn").addEventListener("click", async () => {
    const issuer = document.getElementById("issuer").value || "WebTools";
    const account = document.getElementById("account").value || "user@example.com";

    try {
        // TOTPを生成
        const response = await fetch("/ToolsWebApp/public/api/tool/totp", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `issuer=${encodeURIComponent(issuer)}&account=${encodeURIComponent(account)}`
        });

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.error);
        }

        // シークレットキーを表示
        document.getElementById("secret").value = data.secret;

        // QRコードを生成
        const qrResponse = await fetch("/ToolsWebApp/public/api/tool/totp/qr", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `uri=${encodeURIComponent(data.uri)}`
        });

        const qrData = await qrResponse.json();
        if (!qrData.success) {
            throw new Error(qrData.error);
        }

        // QRコードを表示
        document.getElementById("qr-code").src = qrData.data;
        document.getElementById("result").classList.remove("is-hidden");
    } catch (error) {
        alert("エラーが発生しました: " + error.message);
    }
});

// 既存のTOTP検証
let verifyIntervalId;

async function fetchTotpCode() {
    const secret = document.getElementById("existing-secret").value;
    if (!secret) {
        document.getElementById("totp-code").textContent = "------";
        return;
    }

    try {
        const response = await fetch("/ToolsWebApp/public/api/tool/totp/verify", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `secret=${encodeURIComponent(secret)}`
        });

        const data = await response.json();
        if (!data.success) {
            throw new Error(data.error);
        }

        document.getElementById("totp-code").textContent = data.data.code;
    } catch (error) {
        document.getElementById("totp-code").textContent = "エラー";
        console.error("エラーが発生しました: " + error.message);
    }
}

function updateTimer() {
    const now = Math.floor(Date.now() / 1000);
    const timeLeft = 30 - (now % 30);
    document.getElementById("timer").textContent = timeLeft;

    if (timeLeft === 30 || timeLeft === 0) {
        fetchTotpCode();
    }
}

function startTimer() {
    if (verifyIntervalId) {
        clearInterval(verifyIntervalId);
    }

    updateTimer(); // 即時実行
    verifyIntervalId = setInterval(updateTimer, 1000);
}

document.getElementById("existing-secret").addEventListener("input", () => {
    fetchTotpCode();
    if (!verifyIntervalId) {
        startTimer();
    }
});

document.getElementById("verify-btn").addEventListener("click", () => {
    fetchTotpCode();
    startTimer();
});

document.getElementById("copy-btn").addEventListener("click", () => {
    const code = document.getElementById("totp-code").textContent;
    navigator.clipboard.writeText(code).then(() => {
        alert("TOTPコードをコピーしました");
    });
});

// アニメーション
document.addEventListener("DOMContentLoaded", function() {
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
';

include __DIR__ . '/../../layouts/header.php';
?>

<div class="content-wrapper">
    <div class="container">
        <h1 class="title section-title animate-on-scroll" data-animation="animate__fadeInUp">TOTP Generator</h1>

        <div class="tabs animate-on-scroll" data-animation="animate__fadeInUp">
            <ul>
                <li class="is-active" data-tab="generate"><a>新規生成</a></li>
                <li data-tab="verify"><a>既存のTOTP</a></li>
            </ul>
        </div>

        <div class="tabs-content">
            <div id="generate-tab" class="is-active animate-on-scroll" data-animation="animate__fadeInUp">
                <div class="content">
                    <p>2段階認証用のTOTPを生成します。生成されたQRコードをGoogle AuthenticatorやMicrosoft Authenticatorなどの認証アプリで読み取ってください。</p>
                </div>

                <div id="totp-container" class="box">
                    <div class="field-group">
                        <div class="field">
                            <label class="label">発行者名</label>
                            <div class="control">
                                <input class="input" type="text" id="issuer" placeholder="WebTools">
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">アカウント名</label>
                            <div class="control">
                                <input class="input" type="text" id="account" placeholder="user@example.com">
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <button class="button is-primary" id="generate-btn">TOTPを生成</button>
                        </div>
                    </div>

                    <div id="result" class="is-hidden">
                        <div class="field">
                            <label class="label">シークレットキー</label>
                            <div class="control">
                                <input class="input" type="text" id="secret" readonly>
                            </div>
                        </div>

                        <div class="qr-code">
                            <img id="qr-code" src="" alt="QR Code">
                        </div>
                    </div>
                </div>
            </div>

            <div id="verify-tab" class="animate-on-scroll" data-animation="animate__fadeInUp">
                <div class="content">
                    <p>既存のシークレットキーからTOTPコードを生成します。</p>
                </div>

                <div class="box">
                    <div class="field">
                        <label class="label">シークレットキー</label>
                        <div class="control">
                            <input class="input" type="text" id="existing-secret" placeholder="シークレットキーを入力">
                        </div>
                    </div>

                    <div class="field">
                        <div class="control">
                            <button class="button is-primary" id="verify-btn">TOTPを表示</button>
                        </div>
                    </div>

                    <div id="totp-result">
                        <div class="field">
                            <label class="label">現在のTOTPコード</label>
                            <div class="totp-container">
                                <p class="title is-2" id="totp-code">------</p>
                                <button class="button is-info" id="copy-btn">コピー</button>
                            </div>
                        </div>
                        <div class="field">
                            <p class="subtitle">次のコードまでの残り時間: <span id="timer">30</span>秒</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>
