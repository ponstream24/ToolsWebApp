<?php
$pageTitle = 'テキストエンコーダー/デコーダー | ' . \Infrastructure\Config\Config::get('app.title');
$additionalStyles = '';

$additionalScripts = '
document.addEventListener("DOMContentLoaded", function() {
    const inputText = document.getElementById("input-text");
    const outputText = document.getElementById("output-text");
    const encodeType = document.getElementById("encode-type");
    const encodeBtn = document.getElementById("encode-btn");
    const decodeBtn = document.getElementById("decode-btn");
    const copyBtn = document.getElementById("copy-btn");
    const clearBtn = document.getElementById("clear-btn");
    
    // エンコード処理
    encodeBtn.addEventListener("click", async function() {
        const text = inputText.value;
        const type = encodeType.value;
        
        if (!text) {
            alert("エンコードするテキストを入力してください。");
            return;
        }
        
        try {
            const response = await fetch("/ToolsWebApp/public/api/tool/encoder/encode", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `text=${encodeURIComponent(text)}&type=${type}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                outputText.value = data.data;
            } else {
                alert(data.error);
            }
        } catch (error) {
            console.error("エラーが発生しました:", error);
            alert("エンコード中にエラーが発生しました。");
        }
    });
    
    // デコード処理
    decodeBtn.addEventListener("click", async function() {
        const text = inputText.value;
        const type = encodeType.value;
        
        if (!text) {
            alert("デコードするテキストを入力してください。");
            return;
        }
        
        try {
            const response = await fetch("/ToolsWebApp/public/api/tool/encoder/decode", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `text=${encodeURIComponent(text)}&type=${type}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                outputText.value = data.data;
            } else {
                alert(data.error);
            }
        } catch (error) {
            console.error("エラーが発生しました:", error);
            alert("デコード中にエラーが発生しました。");
        }
    });
    
    // コピーボタン
    copyBtn.addEventListener("click", function() {
        outputText.select();
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
    
    // クリアボタン
    clearBtn.addEventListener("click", function() {
        inputText.value = "";
        outputText.value = "";
        inputText.focus();
    });
    
    // 入力と出力を入れ替えるボタン
    document.getElementById("swap-btn").addEventListener("click", function() {
        const temp = inputText.value;
        inputText.value = outputText.value;
        outputText.value = temp;
    });
});
';

include __DIR__ . '/../../layouts/header.php';
?>

<div class="content-wrapper">
    <div class="container">
        <h1 class="title section-title animate-on-scroll" data-animation="animate__fadeInUp">テキストエンコーダー/デコーダー</h1>
        
        <div class="columns">
            <div class="column is-10 is-offset-1">
                <div class="card animate-on-scroll" data-animation="animate__fadeInUp">
                    <div class="card-content">
                        <div class="content">
                            <p>テキストを様々な形式でエンコード・デコードできます。Base64、URL、HTML、JSONなどの形式に対応しています。</p>
                        </div>
                        
                        <div class="field">
                            <label class="label">エンコード/デコード形式</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="encode-type">
                                        <option value="base64">Base64</option>
                                        <option value="url">URL</option>
                                        <option value="html">HTML</option>
                                        <option value="json">JSON</option>
                                        <option value="hex">16進数</option>
                                        <option value="binary">2進数</option>
                                        <option value="md5">MD5（エンコードのみ）</option>
                                        <option value="sha1">SHA-1（エンコードのみ）</option>
                                        <option value="sha256">SHA-256（エンコードのみ）</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="columns">
                            <div class="column">
                                <div class="field">
                                    <label class="label">入力テキスト</label>
                                    <div class="control">
                                        <textarea id="input-text" class="textarea" rows="10" placeholder="エンコード/デコードするテキストを入力してください"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="column is-narrow is-flex is-flex-direction-column is-justify-content-center">
                                <button id="swap-btn" class="button is-info mb-3">
                                    <span class="icon">
                                        <i class="fas fa-exchange-alt"></i>
                                    </span>
                                </button>
                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="label">出力テキスト</label>
                                    <div class="control">
                                        <textarea id="output-text" class="textarea" rows="10" readonly placeholder="結果がここに表示されます"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="field is-grouped">
                            <div class="control">
                                <button id="encode-btn" class="button is-primary">
                                    <span class="icon">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <span>エンコード</span>
                                </button>
                            </div>
                            <div class="control">
                                <button id="decode-btn" class="button is-warning">
                                    <span class="icon">
                                        <i class="fas fa-unlock"></i>
                                    </span>
                                    <span>デコード</span>
                                </button>
                            </div>
                            <div class="control">
                                <button id="copy-btn" class="button is-info">
                                    <span class="icon">
                                        <i class="fas fa-copy"></i>
                                    </span>
                                    <span>コピー</span>
                                </button>
                            </div>
                            <div class="control">
                                <button id="clear-btn" class="button is-danger">
                                    <span class="icon">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                    <span>クリア</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-5 animate-on-scroll" data-animation="animate__fadeInUp">
                    <div class="card-content">
                        <h2 class="title is-4">エンコード/デコード形式の説明</h2>
                        <div class="content">
                            <dl>
                                <dt><strong>Base64</strong></dt>
                                <dd>バイナリデータをASCII文字列に変換するエンコード方式です。メール添付ファイルなどで使用されます。</dd>
                                
                                <dt><strong>URL</strong></dt>
                                <dd>URLで使用できない特殊文字をエンコードします。スペースは「%20」、日本語は「%E3%81%82」のように変換されます。</dd>
                                
                                <dt><strong>HTML</strong></dt>
                                <dd>HTMLで特別な意味を持つ文字（&lt;, &gt;, &amp;など）をエンティティに変換します。</dd>
                                
                                <dt><strong>JSON</strong></dt>
                                <dd>テキストをJSON文字列としてエスケープします。引用符やバックスラッシュなどが処理されます。</dd>
                                
                                <dt><strong>16進数</strong></dt>
                                <dd>テキストを16進数表記に変換します。「A」は「41」、「あ」は「E3 81 82」のように表示されます。</dd>
                                
                                <dt><strong>2進数</strong></dt>
                                <dd>テキストを2進数表記に変換します。「A」は「01000001」のように表示されます。</dd>
                                
                                <dt><strong>MD5/SHA-1/SHA-256</strong></dt>
                                <dd>テキストのハッシュ値を計算します。これらは一方向の暗号化のため、デコードはできません。</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?> 