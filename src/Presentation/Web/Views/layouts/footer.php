        </div>
    </section>
    <footer class="footer">
        <div class="content has-text-centered">
            <p>
                <strong>WebTools</strong> - 便利なウェブツールコレクション
            </p>
        </div>
    </footer>

    <script>
        // テーマ切り替え機能
        document.addEventListener("DOMContentLoaded", function() {
            const themeButtons = document.querySelectorAll(".theme-button");
            const htmlElement = document.documentElement;
            
            // ローカルストレージからテーマを取得
            const savedTheme = localStorage.getItem("preferred-theme") || "dark";
            htmlElement.setAttribute("data-theme", savedTheme);
            
            // アクティブなテーマボタンを設定
            themeButtons.forEach(button => {
                if (button.getAttribute("data-theme") === savedTheme) {
                    button.classList.add("active");
                } else {
                    button.classList.remove("active");
                }
            });
            
            // テーマボタンのクリックイベント
            themeButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const theme = this.getAttribute("data-theme");
                    htmlElement.setAttribute("data-theme", theme);
                    localStorage.setItem("preferred-theme", theme);
                    
                    themeButtons.forEach(btn => btn.classList.remove("active"));
                    this.classList.add("active");
                });
            });
        });
    </script>
    
    <?php if (isset($additionalScripts)): ?>
    <script>
        <?php echo $additionalScripts; ?>
    </script>
    <?php endif; ?>
</body>
</html> 