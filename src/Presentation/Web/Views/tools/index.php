<?php
$pageTitle = \Infrastructure\Config\Config::get('app.title') . ' | ' . \Infrastructure\Config\Config::get('app.author');
$additionalStyles = '';

$additionalScripts = '
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

include __DIR__ . '/../layouts/header.php';
?>

<div class="hero">
    <div class="hero-body">
        <div class="container">
            <h1 class="hero-title animate-on-scroll" data-animation="animate__fadeInUp">
                便利なウェブツールコレクション
            </h1>
            <p class="hero-subtitle animate-on-scroll" data-animation="animate__fadeInUp">
                日常のタスクを簡単に解決するための様々なツールを提供しています。
                QRコード生成、TOTP認証など、必要なツールをすぐに使えます。
            </p>
            <div class="hero-buttons animate-on-scroll" data-animation="animate__fadeInUp">
                <a href="#tools" class="button hero-button is-primary">
                    ツールを見る
                </a>
                <a href="#about" class="button hero-button is-outlined">
                    詳細を見る
                </a>
            </div>
        </div>
            </div>
        </div>

<div class="content-wrapper">
    <section class="section" id="tools">
        <div class="container">
            <h2 class="section-title animate-on-scroll" data-animation="animate__fadeInUp">
                利用可能なツール
                        </h2>
            
            <div class="tools-grid">
                <?php foreach ($tools as $tool): ?>
                <div class="card animate-on-scroll" data-animation="animate__fadeInUp">
                                <header class="card-header">
                                    <p class="card-header-title">
                            <?= htmlspecialchars($tool->getName()) ?>
                                    </p>
                                </header>
                                <div class="card-content">
                                    <div class="content">
                            <?= htmlspecialchars($tool->getDescription()) ?>
                                    </div>
                                </div>
                                <footer class="card-footer">
                        <a href="<?= htmlspecialchars($tool->getUrl()) ?>" class="card-footer-item">
                            使ってみる
                                    </a>
                                </footer>
                            </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <section class="section" id="about">
        <div class="container">
            <h2 class="section-title animate-on-scroll" data-animation="animate__fadeInUp">
                WebToolsについて
            </h2>
            
            <div class="columns">
                <div class="column is-8 is-offset-2">
                    <div class="content animate-on-scroll" data-animation="animate__fadeInUp">
                        <p>
                            WebToolsは、日常的に必要となる様々なツールをウェブブラウザから簡単に利用できるようにしたサービスです。
                            セキュリティ、データ変換、コード生成など、様々なカテゴリのツールを提供しています。
                        </p>
                        <p>
                            すべてのツールはブラウザ上で動作し、データはサーバーに保存されません。
                            安心して利用いただけます。
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
