    <!-- Dependências Javascript via CDNs -->
    
    <!-- jQuery (Obrigatório e carregado primeiro) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- jQuery UI (Para Sortable e Autocomplete) -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    
    <!-- Bootstrap 5 Bundle (com Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js (para gráficos de comparações e admin) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Script Geral GOMOS -->
    <script src="<?= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) ?>/assets/js/main.js"></script>

    <!-- Script construtor de treinos dinâmico (opcional) -->
    <?php if (isset($loadTreinoJs) && $loadTreinoJs): ?>
        <script src="<?= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) ?>/assets/js/treino.js"></script>
    <?php endif; ?>

</body>
</html>
