<!DOCTYPE html>
<html lang="ja" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'WebTools'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
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
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
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
            content: "";
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
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
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

        .theme-button.active {
            border-color: var(--accent-color);
            transform: scale(1.1);
        }

        .footer {
            background-color: var(--primary-color) !important;
            color: var(--text-color);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .title, .label, .subtitle {
            color: var(--text-color) !important;
        }

        .button.is-primary {
            background-color: var(--accent-color);
            border-color: transparent;
        }

        .button.is-primary:hover {
            background-color: var(--accent-color);
            opacity: 0.9;
        }

        .input, .textarea, .select select {
            background-color: var(--secondary-color);
            border-color: var(--card-border);
            color: var(--text-color);
        }

        .input:focus, .textarea:focus, .select select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.125em rgba(59, 130, 246, 0.25);
        }

        .box {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .content p {
            color: var(--text-color);
        }

        .hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
        }

        .hero-body {
            padding: 6rem 1.5rem;
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--text-color) 0%, var(--accent-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 700px;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .hero-button {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .hero-button.is-primary {
            background-color: var(--accent-color);
            color: var(--card-bg);
            border: none;
        }

        .hero-button.is-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .hero-button.is-outlined {
            background-color: transparent;
            border: 2px solid var(--accent-color);
            color: var(--accent-color);
        }

        .hero-button.is-outlined:hover {
            background-color: var(--accent-color);
            color: var(--card-bg);
            transform: translateY(-4px);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
            padding-bottom: 1.5rem;
        }

        .section-title::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--accent-color);
            border-radius: 2px;
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                font-size: 1.2rem;
            }
            .section-title {
                font-size: 2rem;
            }
        }

        .totp-box {
            margin-top: 50px;
        }
        .seed-box {
            margin-bottom: 15px;
        }
        .totp-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .qr-code {
            margin-top: 1rem;
            text-align: center;
        }
        .qr-code img {
            max-width: 200px;
            margin: 0 auto;
        }
        .field-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .tabs-content > div {
            display: none;
        }
        .tabs-content > div.is-active {
            display: block;
        }
    </style>
    <?php if (isset($additionalStyles)): ?>
    <style>
        <?php echo $additionalStyles; ?>
    </style>
    <?php endif; ?>
</head>
<body>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="/ToolsWebApp/public/">
                <h1 class="title" style="margin-bottom: 0;">WebTools</h1>
            </a>
        </div>
        <div class="navbar-menu">
            <div class="navbar-start">
                <a class="navbar-item" href="/ToolsWebApp/public/tool/qr">
                    <span class="icon"><i class="fas fa-qrcode"></i></span>
                    <span>QRコード</span>
                </a>
                <a class="navbar-item" href="/ToolsWebApp/public/tool/totp">
                    <span class="icon"><i class="fas fa-key"></i></span>
                    <span>TOTP</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="theme-switcher">
        <div class="theme-button" data-theme="dark" title="ダーク">
            <i class="fas fa-moon"></i>
        </div>
        <div class="theme-button" data-theme="light" title="ライト">
            <i class="fas fa-sun"></i>
        </div>
        <div class="theme-button" data-theme="cyberpunk" title="サイバー">
            <i class="fas fa-robot"></i>
        </div>
        <div class="theme-button" data-theme="japanese" title="和風">
            <i class="fas fa-fan"></i>
        </div>
    </div>

    <section class="section">
        <div class="container"> 