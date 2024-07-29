<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOTP Generator</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <style>
        .totp-box {
            margin-top: 50px;
        }
        .seed-box {
            margin-bottom: 15px;
        }
        .totp-container {
            display: flex;
            align-items: center;
        }
        .remove-seed-btn {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="title">TOTP Generator</h1>

            <button class="button is-primary" id="add-seed-btn">シード値を追加</button>
            
            <div id="seeds-container"></div>
        </div>
    </section>

    <template id="totp-template">
        <div class="box totp-box">
            <div class="field seed-box">
                <div class="control">
                    <input class="input seed-input" type="text" placeholder="シード値を入力">
                </div>
            </div>
            <div class="totp-container">
                <h2 class="subtitle">Your TOTP Code:</h2>
                <p class="title is-2 totp-code">Loading...</p>
                <button class="button is-info copy-btn">コピー</button>
                <button class="button is-danger remove-seed-btn">削除</button>
            </div>
            <p class="subtitle timer">30</p>
        </div>
    </template>

    <script>
        let intervalId;

        async function fetchTotpCode(seedInput, totpCodeElement) {
            const seed = seedInput.value;
            if (seed) {
                const response = await fetch(`api.php?seed=${seed}`);
                const data = await response.json();
                totpCodeElement.textContent = data.totp;
            }
        }

        function startTimer(seedInput, totpCodeElement, timerElement) {
            if (intervalId) {
                clearInterval(intervalId);
            }

            intervalId = setInterval(() => {
                const currentSeconds = new Date().getSeconds();
                const timeLeft = 30 - (currentSeconds % 30);
                timerElement.textContent = timeLeft;

                if (timeLeft === 30) {
                    fetchTotpCode(seedInput, totpCodeElement);
                }
            }, 1000);
        }

        function addSeedField() {
            const seedsContainer = document.getElementById('seeds-container');
            const template = document.getElementById('totp-template').content.cloneNode(true);
            const totpBox = template.querySelector('.totp-box');
            const seedInput = template.querySelector('.seed-input');
            const totpCodeElement = template.querySelector('.totp-code');
            const timerElement = template.querySelector('.timer');

            template.querySelector('.remove-seed-btn').addEventListener('click', () => {
                totpBox.remove();
            });

            template.querySelector('.copy-btn').addEventListener('click', () => {
                navigator.clipboard.writeText(totpCodeElement.textContent).then(() => {
                    alert('TOTPコードをコピーしました');
                });
            });

            seedInput.addEventListener('input', () => {
                fetchTotpCode(seedInput, totpCodeElement);
                startTimer(seedInput, totpCodeElement, timerElement);
            });

            seedsContainer.appendChild(template);
            fetchTotpCode(seedInput, totpCodeElement);
            startTimer(seedInput, totpCodeElement, timerElement);
        }

        document.getElementById('add-seed-btn').addEventListener('click', addSeedField);

        addSeedField(); // 初期のシード値フィールドを1つ追加
    </script>
</body>
</html>
