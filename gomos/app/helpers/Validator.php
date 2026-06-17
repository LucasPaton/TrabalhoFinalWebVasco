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

    /**
     * Formata um nome completo com a primeira letra de cada palavra em maiúscula,
     * mantendo preposições em minúscula (de, da, do, dos, e).
     * 
     * @param string $nome
     * @return string
     */
    public static function formatarNome($nome) {
        $nome = mb_convert_case(trim($nome), MB_CASE_TITLE, "UTF-8");
        $preposicoes = ['De', 'Da', 'Do', 'Dos', 'Das', 'E'];
        foreach ($preposicoes as $prep) {
            $nome = preg_replace('/\b' . $prep . '\b/u', mb_strtolower($prep), $nome);
        }
        return $nome;
    }

    /**
     * Formata e limpa o username para letras minúsculas e remove espaços.
     * 
     * @param string $username
     * @return string
     */
    public static function formatarUsername($username) {
        $username = trim($username);
        $username = strtolower($username);
        return preg_replace('/\s+/', '', $username);
    }

    /**
     * Formata o e-mail para letras minúsculas e remove espaços.
     * 
     * @param string $email
     * @return string
     */
    public static function formatarEmail($email) {
        return strtolower(trim($email));
    }

    /**
     * Formata o nome da cidade em Title Case.
     * 
     * @param string $cidade
     * @return string
     */
    public static function formatarCidade($cidade) {
        return mb_convert_case(trim($cidade), MB_CASE_TITLE, "UTF-8");
    }

    /**
     * Formata a sigla do estado em letras maiúsculas.
     * 
     * @param string $estado
     * @return string
     */
    public static function formatarEstado($estado) {
        return strtoupper(trim($estado));
    }
}
