<?php
namespace App\Helpers;

/**
 * Helper para validação e sanitização de dados de entrada.
 */
class Validator {
    /**
     * Sanitiza dados de texto gerais para exibição HTML e evita XSS.
     * 
     * @param string $data
     * @return string
     */
    public static function sanitize($data) {
        return htmlspecialchars(trim(strip_tags($data)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Verifica se um email possui formato válido.
     * 
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida o tamanho mínimo de uma senha.
     * 
     * @param string $password
     * @param int $min
     * @return bool
     */
    public static function validatePasswordLength($password, $min = 6) {
        return strlen($password) >= $min;
    }

    /**
     * Verifica se dois campos coincidem (ex: senha e confirmação de senha).
     * 
     * @param string $value1
     * @param string $value2
     * @return bool
     */
    public static function matches($value1, $value2) {
        return $value1 === $value2;
    }

    /**
     * Limpa e converte um valor para float (peso, altura).
     * 
     * @param mixed $value
     * @return float
     */
    public static function sanitizeFloat($value) {
        $clean = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return floatval($clean);
    }

    /**
     * Limpa e converte um valor para int.
     * 
     * @param mixed $value
     * @return int
     */
    public static function sanitizeInt($value) {
        $clean = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        return intval($clean);
    }
}
