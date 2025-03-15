<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>生成されたQRコード | <?php echo \Infrastructure\Config\Config::get('app.title'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">生成されたQRコード</h1>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <?php if (isset($qrCodeUri)): ?>
                            <img src="<?php echo htmlspecialchars($qrCodeUri); ?>" alt="生成されたQRコード" class="img-fluid mb-3">
                            <div class="mt-3">
                                <a href="<?php echo htmlspecialchars($qrCodeUri); ?>" download="qrcode.png" class="btn btn-success">
                                    <i class="bi bi-download"></i> ダウンロード
                                </a>
                                <a href="/ToolsWebApp/public/tool/qr" class="btn btn-primary ms-2">
                                    <i class="bi bi-arrow-left"></i> 戻る
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> QRコードの生成に失敗しました。
                            </div>
                            <a href="/ToolsWebApp/public/tool/qr" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> 戻る
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 