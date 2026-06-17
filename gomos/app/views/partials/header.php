<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Meta tags de SEO -->
    <meta name="description" content="GOMOS - A rede social definitiva para praticantes de academia. Monte treinos, compare com amigos, registre check-ins e suba no ranking!">
    <meta name="author" content="Lucas Paton">
    
    <title><?= isset($pageTitle) ? $pageTitle . " | GOMOS" : "GOMOS - Treina. Posta. Supera." ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- jQuery UI CSS (para Autocomplete styling) -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- Estilo Global -->
    <link rel="stylesheet" href="<?= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) ?>/assets/css/style.css">

    <!-- Estilos específicos baseado na página -->
    <?php if (isset($isLanding) && $isLanding): ?>
        <link rel="stylesheet" href="<?= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) ?>/assets/css/landing.css">
    <?php else: ?>
        <link rel="stylesheet" href="<?= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) ?>/assets/css/dashboard.css">
    <?php endif; ?>

    <!-- Definir raiz do projeto dinamicamente para scripts JS -->
    <script>
        window.GOMOS_ROOT = '<?= rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/public') ?>';
    </script>
</head>
<body>
    
    <!-- Toast Flash Notifications -->
    <div class="toast-container">
        <?php 
        $flashes = \App\Helpers\Session::getAllFlashes();
        foreach ($flashes as $type => $message): 
        ?>
            <div class="toast toast-gomos border-left-<?= $type ?>" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-dark text-white border-bottom-0">
                    <strong class="me-auto text-orange"><i class="fa-solid fa-dumbbell"></i> GOMOS</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <?= $message ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
