// Scripts Globais GOMOS

$(document).ready(function() {
    // 1. Efeito Glassmorphism no scroll do Navbar da Landing Page
    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('.navbar-glass').addClass('scrolled');
        } else {
            $('.navbar-glass').removeClass('scrolled');
        }
    });

    // 2. Animação de Scroll (Intersection Observer)
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('appear');
                }
            });
        }, {
            threshold: 0.15
        });

        document.querySelectorAll('.animate-fade-in').forEach((el) => {
            observer.observe(el);
        });
    } else {
        // Fallback caso navegador seja antigo
        $('.animate-fade-in').removeClass('animate-fade-in');
    }

    // 3. Inicializar Toasts do Bootstrap
    $('.toast').toast({
        delay: 5000
    }).toast('show');

    // 4. Lógica de Curtida via AJAX
    $(document).on('click', '.btn-curtir-ajax', function(e) {
        e.preventDefault();
        const btn = $(this);
        const treinoId = btn.data('id');
        
        // Obter raiz do GOMOS definida dinamicamente no cabeçalho
        const rootPath = window.GOMOS_ROOT || '';

        $.ajax({
            url: rootPath + '/treino/curtir/' + treinoId,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'sucesso') {
                    // Atualizar número de curtidas na tela
                    btn.find('.curtidas-count').text(response.total_curtidas);
                    
                    const icon = btn.find('i');
                    if (response.acao === 'curtiu') {
                        btn.addClass('active text-orange');
                        icon.removeClass('fa-regular').addClass('fa-solid text-orange');
                        showToast('info', 'Você curtiu este treino! 🏋️');
                    } else {
                        btn.removeClass('active text-orange');
                        icon.removeClass('fa-solid text-orange').addClass('fa-regular');
                        showToast('info', 'Você removeu sua curtida.');
                    }
                } else {
                    showToast('danger', 'Não foi possível curtir o treino.');
                }
            },
            error: function() {
                showToast('danger', 'Erro de conexão ao processar curtida.');
            }
        });
    });

    // Helper de Toast Dinâmico
    function showToast(type, message) {
        const toastId = 'toast_' + Math.random().toString(36).substr(2, 9);
        const toastHTML = `
            <div id="${toastId}" class="toast toast-gomos border-left-${type}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-dark text-white border-bottom-0">
                    <strong class="me-auto text-orange"><i class="fa-solid fa-dumbbell"></i> GOMOS</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        // Criar container de toast se não existir
        if ($('.toast-container').length === 0) {
            $('body').append('<div class="toast-container"></div>');
        }
        
        $('.toast-container').append(toastHTML);
        
        const toastEl = $('#' + toastId);
        toastEl.toast({ delay: 4000 }).toast('show');
        
        // Remover do DOM após fechar
        toastEl.on('hidden.bs.toast', function () {
            $(this).remove();
        });
    }
});
