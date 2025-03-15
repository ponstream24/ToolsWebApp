<!DOCTYPE html>
<html lang="ja" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebTools | YukiTetsuka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ダークテーマ（デフォルト） */
        :root[data-theme="dark"] {
            --primary-color: #0f172a;
            --secondary-color: #1e293b;
            --accent-color: #3b82f6;
            --text-color: #f8fafc;
            --card-bg: #1e293b;
            --card-border: #3b82f6;
            --navbar-bg: rgba(15, 23, 42, 0.9);
            --gradient-start: #0f172a;
            --gradient-end: #1e293b;
        }

        /* ライトテーマ */
        :root[data-theme="light"] {
            --primary-color: #ffffff;
            --secondary-color: #f1f5f9;
            --accent-color: #3b82f6;
            --text-color: #1e293b;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --navbar-bg: rgba(255, 255, 255, 0.9);
            --gradient-start: #f1f5f9;
            --gradient-end: #ffffff;
        }

        /* サイバーパンクテーマ */
        :root[data-theme="cyberpunk"] {
            --primary-color: #18181b;
            --secondary-color: #27272a;
            --accent-color: #8b5cf6;
            --text-color: #fafafa;
            --card-bg: #27272a;
            --card-border: #8b5cf6;
            --navbar-bg: rgba(24, 24, 27, 0.9);
            --gradient-start: #18181b;
            --gradient-end: #27272a;
        }

        /* 和風テーマ */
        :root[data-theme="japanese"] {
            --primary-color: #1a1a1a;
            --secondary-color: #262626;
            --accent-color: #dc2626;
            --text-color: #fafafa;
            --card-bg: #262626;
            --card-border: #dc2626;
            --navbar-bg: rgba(26, 26, 26, 0.9);
            --gradient-start: #1a1a1a;
            --gradient-end: #262626;
        }

        html {
            height: 100%;
            scroll-behavior: smooth;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: radial-gradient(circle at top right, var(--gradient-end) 0%, var(--gradient-start) 100%);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
        }

        .content-wrapper {
            flex: 1;
            padding: 4rem 0;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--accent-color);
            transform: scaleX(0);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        .card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: transparent;
            border: none;
            padding: 1.5rem;
        }

        .card-header-title {
            color: var(--text-color) !important;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card-content {
            padding: 1.5rem;
            border-top: 1px solid rgba(var(--card-border), 0.1);
        }

        .card-content .content {
            color: var(--text-color);
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .card-footer {
            border: none;
            padding: 1rem;
        }

        .card-footer-item {
            color: var(--accent-color) !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border-radius: 12px;
            margin: 0 1rem;
            padding: 0.75rem 1.5rem;
        }

        .card-footer-item:hover {
            background-color: var(--accent-color);
            color: var(--card-bg) !important;
            transform: translateY(-2px);
        }

        .navbar {
            background-color: var(--navbar-bg) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand .title {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .navbar-item {
            color: var(--text-color) !important;
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 8px;
        }

        .navbar-item:hover {
            color: var(--accent-color) !important;
            background-color: rgba(var(--accent-color), 0.1) !important;
        }

        .theme-switcher {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--card-bg);
            border-radius: 12px;
            margin-right: 1rem;
            border: 1px solid var(--card-border);
        }

        .theme-button {
            width: 40px;
            height: 40px;
            border: 2px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            position: relative;
            overflow: hidden;
        }

        .theme-button::after {
            content: attr(title);
            position: absolute;
            bottom: -2rem;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.7rem;
            opacity: 0;
            transition: all 0.3s ease;
            white-space: nowrap;
            color: var(--text-color);
        }

        .theme-button:hover::after {
            opacity: 1;
            bottom: -1.5rem;
        }

        .theme-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .theme-button[data-theme="dark"] { 
            background: #0f172a;
            color: #3b82f6;
        }
        .theme-button[data-theme="light"] { 
            background: #ffffff;
            color: #3b82f6;
        }
        .theme-button[data-theme="cyberpunk"] { 
            background: #18181b;
            color: #8b5cf6;
        }
        .theme-button[data-theme="japanese"] { 
            background: #1a1a1a;
            color: #dc2626;
        }

        /* 選択中のテーマボタンのスタイル */
        .theme-button.active {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px var(--accent-color);
            transform: scale(1.1);
        }

        .theme-button.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--accent-color);
            opacity: 0.1;
            border-radius: 8px;
        }

        /* テーマ名のツールチップ */
        .theme-tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 0.4rem 0.8rem;
            background: var(--card-bg);
            color: var(--text-color);
            border-radius: 6px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--card-border);
        }

        .theme-button:hover .theme-tooltip {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 5px);
        }

        .footer {
            background-color: var(--primary-color) !important;
            padding: 3rem 0;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--accent-color), transparent);
        }

        .footer a {
            color: var(--accent-color) !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: var(--text-color) !important;
            text-decoration: underline;
        }

        @media screen and (max-width: 768px) {
            .content-wrapper {
                padding: 2rem 1rem;
            }

            .card {
                margin: 1rem;
            }

            .theme-switcher {
                margin: 1rem;
                justify-content: center;
            }

            .navbar-brand .title {
                font-size: 1.5rem;
            }
        }

        .animate__animated {
            animation-duration: 0.8s;
        }

        .columns {
            margin-top: 2rem;
        }

        .column {
            padding: 1rem;
        }

        /* 追加のスタイル */
        .category-section {
            margin-bottom: 4rem;
        }

        .category-header {
            margin-bottom: 2rem;
            padding: 1rem;
            border-radius: 12px;
            background: var(--card-bg);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .category-title {
            color: var(--text-color);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .category-description {
            color: var(--text-color);
            opacity: 0.8;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .search-section {
            margin-bottom: 3rem;
            padding: 2rem;
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .search-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--card-border);
            border-radius: 12px;
            background: var(--primary-color);
            color: var(--text-color);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(var(--accent-color), 0.1);
        }

        .category-filter {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .category-tag {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: var(--primary-color);
            color: var(--text-color);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--card-border);
        }

        .category-tag:hover,
        .category-tag.active {
            background: var(--accent-color);
            color: var(--card-bg);
            border-color: var(--accent-color);
        }

        @media screen and (max-width: 768px) {
            .category-section {
                margin-bottom: 2rem;
            }

            .tools-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .search-section {
                padding: 1rem;
                margin: 1rem;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <a class="navbar-item" href="#">
                <h1 class="title" style="color: var(--text-color)">WebTools</h1>
            </a>
            <div class="navbar-burger" data-target="navMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="navMenu" class="navbar-menu">
            <div class="navbar-end">
                <div class="theme-switcher navbar-item">
                    <button class="theme-button" data-theme="dark" title="ダークテーマ">
                        <i class="fas fa-moon"></i>
                        <div class="theme-tooltip">ダークテーマ</div>
                    </button>
                    <button class="theme-button" data-theme="light" title="ライトテーマ">
                        <i class="fas fa-sun"></i>
                        <div class="theme-tooltip">ライトテーマ</div>
                    </button>
                    <button class="theme-button" data-theme="cyberpunk" title="サイバーパンク">
                        <i class="fas fa-robot"></i>
                        <div class="theme-tooltip">サイバーパンク</div>
                    </button>
                    <button class="theme-button" data-theme="japanese" title="和風">
                        <i class="fas fa-fan"></i>
                        <div class="theme-tooltip">和風</div>
                    </button>
                </div>
                <?php
                $json = file_get_contents('tools.json');
                $tools = json_decode($json, true);

                foreach ($tools as $tool) {
                    echo '<a class="navbar-item" href="' . htmlspecialchars($tool['link']) . '">' . htmlspecialchars($tool['name']) . '</a>';
                }
                ?>
            </div>
        </div>
    </nav>

    <main class="section content-wrapper">
        <div class="container">
            <div class="search-section animate__animated animate__fadeIn">
                <input type="text" class="search-input" placeholder="ツールを検索..." id="toolSearch">
                <div class="category-filter" id="categoryFilter">
                    <span class="category-tag active" data-category="all">すべて</span>
                    <span class="category-tag" data-category="security">セキュリティ</span>
                    <span class="category-tag" data-category="converter">変換</span>
                    <span class="category-tag" data-category="generator">生成</span>
                    <span class="category-tag" data-category="utility">ユーティリティ</span>
                </div>
            </div>

            <?php
            // ツールをカテゴリーごとに分類
            $categories = [
                'security' => [
                    'name' => 'セキュリティ',
                    'icon' => 'fa-shield-alt',
                    'description' => '暗号化、パスワード生成など、セキュリティ関連のツール群です。',
                ],
                'converter' => [
                    'name' => '変換',
                    'icon' => 'fa-exchange-alt',
                    'description' => '様々なフォーマット間の変換を行うツール群です。',
                ],
                'generator' => [
                    'name' => '生成',
                    'icon' => 'fa-magic',
                    'description' => 'QRコード、画像など、様々なものを生成するツール群です。',
                ],
                'utility' => [
                    'name' => 'ユーティリティ',
                    'icon' => 'fa-tools',
                    'description' => '便利な機能を提供する汎用ツール群です。',
                ],
            ];

            $json = file_get_contents('tools.json');
            $tools = json_decode($json, true);

            // 仮のカテゴリー割り当て（本来はtools.jsonに含めるべき）
            $toolCategories = [
                'QRコードジェネレーター' => 'generator',
                '暗号化・復号化' => 'security',
                'ツール3' => 'utility',
            ];

            foreach ($categories as $categoryId => $category) {
                echo '<div class="category-section animate__animated animate__fadeIn" data-category="' . $categoryId . '">
                    <div class="category-header">
                        <h2 class="category-title">
                            <i class="fas ' . $category['icon'] . '"></i>
                            ' . $category['name'] . '
                        </h2>
                        <p class="category-description">' . $category['description'] . '</p>
                    </div>
                    <div class="tools-grid">';

                foreach ($tools as $tool) {
                    if (isset($toolCategories[$tool['name']]) && $toolCategories[$tool['name']] === $categoryId) {
                        echo '<div class="animate__animated animate__fadeIn tool-card" data-tool-name="' . strtolower(htmlspecialchars($tool['name'])) . '">
                            <div class="card">
                                <header class="card-header">
                                    <p class="card-header-title">
                                        ' . htmlspecialchars($tool['name']) . '
                                    </p>
                                </header>
                                <div class="card-content">
                                    <div class="content">
                                        ' . htmlspecialchars($tool['description']) . '
                                    </div>
                                </div>
                                <footer class="card-footer">
                                    <a href="' . htmlspecialchars($tool['link']) . '" class="card-footer-item">
                                        <span>ツールへ</span>
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                </footer>
                            </div>
                        </div>';
                    }
                }

                echo '</div></div>';
            }
            ?>
        </div>
    </main>

    <footer class="footer">
        <div class="content has-text-centered" style="color: var(--text-color)">
            <p>&copy; 2024 <a href="https://yukiytetsuka.com">YukiTetsuka</a>. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ナビゲーションバーガーメニューの制御
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

            // テーマ切り替えの制御
            const themeButtons = document.querySelectorAll('.theme-button');
            
            function updateActiveTheme(theme) {
                themeButtons.forEach(button => {
                    if (button.getAttribute('data-theme') === theme) {
                        button.classList.add('active');
                    } else {
                        button.classList.remove('active');
                    }
                });
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('preferred-theme', theme);
            }

            themeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const theme = button.getAttribute('data-theme');
                    updateActiveTheme(theme);
                });
            });

            // 保存されたテーマの復元と初期アクティブ状態の設定
            const savedTheme = localStorage.getItem('preferred-theme') || 'dark';
            updateActiveTheme(savedTheme);

            // 検索機能
            const searchInput = document.getElementById('toolSearch');
            const toolCards = document.querySelectorAll('.tool-card');
            const categoryTags = document.querySelectorAll('.category-tag');
            const categorySections = document.querySelectorAll('.category-section');

            function filterTools() {
                const searchTerm = searchInput.value.toLowerCase();
                const activeCategory = document.querySelector('.category-tag.active').dataset.category;

                toolCards.forEach(card => {
                    const toolName = card.dataset.toolName;
                    const categorySection = card.closest('.category-section');
                    const matchesSearch = toolName.includes(searchTerm);
                    const matchesCategory = activeCategory === 'all' || categorySection.dataset.category === activeCategory;

                    card.style.display = matchesSearch && matchesCategory ? '' : 'none';
                });

                // カテゴリーセクションの表示制御
                categorySections.forEach(section => {
                    const hasVisibleTools = Array.from(section.querySelectorAll('.tool-card'))
                        .some(card => card.style.display !== 'none');
                    section.style.display = hasVisibleTools ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterTools);

            categoryTags.forEach(tag => {
                tag.addEventListener('click', () => {
                    categoryTags.forEach(t => t.classList.remove('active'));
                    tag.classList.add('active');
                    filterTools();
                });
            });
        });
    </script>
</body>

</html>
