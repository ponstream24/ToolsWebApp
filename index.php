<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebTools | YukiTetsuka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
    <style>
        html {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .content-wrapper {
            flex: 1;
        }

        .card {
            background-color: #2a2a2a;
            border: 1px solid #3a3a3a;
        }

        .card-content, .card-footer-item {
            color: #ffffff;
        }

        .navbar-burger span {
            background-color: #ffffff;
        }
    </style>
</head>

<body>
    <nav class="navbar is-dark">
        <div class="navbar-brand">
            <a class="navbar-item" href="#">
                <h1 class="title has-text-white">WebTools</h1>
            </a>
            <div class="navbar-burger" data-target="navMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="navMenu" class="navbar-menu">
            <div class="navbar-end">
                <!-- 動的にメニューアイテムを生成します -->
                <?php
                $json = file_get_contents('tools.json');
                $tools = json_decode($json, true);

                foreach ($tools as $tool) {
                    echo '<a class="navbar-item has-text-white" href="' . htmlspecialchars($tool['link']) . '">' . htmlspecialchars($tool['name']) . '</a>';
                }
                ?>
            </div>
        </div>
    </nav>

    <main class="section content-wrapper">
        <div class="container">
            <div class="columns">
                <?php
                foreach ($tools as $tool) {
                    echo '<div class="column is-one-third">
                        <div class="card">
                            <header class="card-header">
                                <p class="card-header-title has-text-white">
                                    ' . htmlspecialchars($tool['name']) . '
                                </p>
                            </header>
                            <div class="card-content">
                                <div class="content has-text-white">
                                    ' . htmlspecialchars($tool['description']) . '
                                </div>
                            </div>
                            <footer class="card-footer">
                                <a href="' . htmlspecialchars($tool['link']) . '" class="card-footer-item has-text-white">ツールへ</a>
                            </footer>
                        </div>
                    </div>';
                }
                ?>
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
