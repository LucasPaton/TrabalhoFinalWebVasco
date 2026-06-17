<?php
namespace App\Helpers;

/**
 * Helper para gerenciamento de sessão PHP e autenticação.
 */
class Session {
    /**
     * Inicia a sessão se ela já não estiver iniciada.
     */
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Define um valor na sessão.
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Obtém um valor da sessão.
     */
    public static function get($key) {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Verifica se uma chave existe na sessão.
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove um valor da sessão.
     */
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destrói a sessão inteira.
     */
    public static function destroy() {
        self::start();
        session_unset();
        session_destroy();
    }

    /**
     * Verifica se o usuário está logado. 
     * Se não estiver, redireciona para a tela de login.
     * 
     * @return bool
     */
    public static function check() {
        self::start();
        if (!isset($_SESSION['usuario_id'])) {
            self::setFlash('danger', 'Acesso restrito. Por favor, faça login para continuar.');
            header("Location: /login");
            exit();
        }
        return true;
    }

    /**
     * Verifica se o usuário está logado como administrador.
     * Se não estiver, redireciona para a página correspondente.
     * 
     * @return bool
     */
    public static function checkAdmin() {
        self::start();
        if (!isset($_SESSION['admin_id'])) {
            self::setFlash('danger', 'Acesso restrito a administradores.');
            header("Location: /login"); // Pode ser redirecionado para login de admin ou login geral
            exit();
        }
        return true;
    }

    /**
     * Define uma mensagem flash (Toaster) de notificação.
     */
    public static function setFlash($type, $message) {
        self::start();
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Retorna e limpa a mensagem flash da sessão se existir.
     * 
     * @return string|null
     */
    public static function getFlash($type) {
        self::start();
        if (isset($_SESSION['flash'][$type])) {
            $msg = $_SESSION['flash'][$type];
            unset($_SESSION['flash'][$type]);
            return $msg;
        }
        return null;
    }

    /**
     * Retorna todas as mensagens flash e limpa o container de flashes.
     * 
     * @return array
     */
    public static function getAllFlashes() {
        self::start();
        $flashes = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
        $_SESSION['flash'] = [];
        return $flashes;
    }
}
