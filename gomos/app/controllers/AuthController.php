<?php
namespace App\Controllers;

use App\Helpers\Session;
use App\Helpers\Validator;
use App\Models\UsuarioModel;
use App\Models\AcademiaModel;
use App\Config\Database;

/**
 * Controller responsável pelos fluxos de Autenticação (Login, Cadastro, Logout).
 */
class AuthController {
    /**
     * Exibe a página de login.
     */
    public function loginPagina() {
        if (Session::has('usuario_id')) {
            header("Location: /feed");
            exit();
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Processa a submissão do formulário de login.
     */
    public function login() {
        $emailOrUsername = Validator::sanitize($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($emailOrUsername) || empty($senha)) {
            Session::setFlash('danger', 'Por favor, preencha todos os campos.');
            header("Location: /login");
            exit();
        }

        // 1. Tentar fazer login como administrador primeiro
        $db = Database::getConnection();
        $stmt_admin = $db->prepare("SELECT * FROM admin_usuarios WHERE email = :email AND ativo = 1");
        $stmt_admin->execute([':email' => $emailOrUsername]);
        $admin = $stmt_admin->fetch();

        if ($admin && password_verify($senha, $admin['senha'])) {
            Session::set('admin_id', $admin['id']);
            Session::set('admin_nome', $admin['nome']);
            Session::set('admin_email', $admin['email']);
            Session::set('nivel_acesso', $admin['nivel_acesso']);
            Session::setFlash('success', 'Bem-vindo ao Painel Administrativo, ' . $admin['nome'] . '!');
            header("Location: /admin");
            exit();
        }

        // 2. Se não for admin, tentar login de usuário comum
        $usuarioModel = new UsuarioModel();
        // Permite login por email ou username
        $usuario = null;
        if (Validator::validateEmail($emailOrUsername)) {
            $usuario = $usuarioModel->buscarPorEmail($emailOrUsername);
        } else {
            $usuario = $usuarioModel->buscarPorUsername($emailOrUsername);
        }

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Guarda dados básicos na sessão
            Session::set('usuario_id', $usuario['id']);
            Session::set('nome', $usuario['nome']);
            Session::set('username', $usuario['username']);
            Session::set('email', $usuario['email']);
            Session::set('foto_perfil', $usuario['foto_perfil']);
            Session::set('nivel_fitness', $usuario['nivel_fitness']);
            Session::set('academia_id', $usuario['academia_id']);
            Session::set('cidade', $usuario['cidade']);
            Session::set('estado', $usuario['estado']);

            // Atualizar último acesso no BD
            $stmt_acesso = $db->prepare("UPDATE usuarios SET ultimo_acesso = CURRENT_TIMESTAMP WHERE id = :id");
            $stmt_acesso->execute([':id' => $usuario['id']]);

            Session::setFlash('success', 'Bem-vindo de volta, ' . $usuario['nome'] . '!');
            header("Location: /feed");
            exit();
        }

        Session::setFlash('danger', 'Usuário ou senha incorretos.');
        header("Location: /login");
        exit();
    }

    /**
     * Exibe a página de cadastro.
     */
    public function cadastroPagina() {
        if (Session::has('usuario_id')) {
            header("Location: /feed");
            exit();
        }

        // Verificação AJAX de username único
        if (isset($_GET['check_username'])) {
            $uModel = new UsuarioModel();
            $username = strtolower(trim(Validator::sanitize($_GET['check_username'])));
            $user = $uModel->buscarPorUsername($username);
            header('Content-Type: application/json');
            echo json_encode(['disponivel' => !$user]);
            exit();
        }

        $academiaModel = new AcademiaModel();
        $academias = $academiaModel->listarTodas();
        require_once __DIR__ . '/../views/landing/cadastro.php';
    }

    /**
     * Processa a submissão de cadastro em etapas.
     */
    public function cadastrar() {
        $nome = Validator::sanitize($_POST['nome'] ?? '');
        $username = strtolower(Validator::sanitize($_POST['username'] ?? ''));
        $email = Validator::sanitize($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';
        
        $nivel_fitness = Validator::sanitize($_POST['nivel_fitness'] ?? 'iniciante');
        $peso = Validator::sanitizeFloat($_POST['peso'] ?? 0);
        $altura = Validator::sanitizeInt($_POST['altura'] ?? 0);
        $bio = Validator::sanitize($_POST['bio'] ?? '');
        
        $cidade = Validator::sanitize($_POST['cidade'] ?? '');
        $estado = strtoupper(Validator::sanitize($_POST['estado'] ?? ''));
        $academia_id = Validator::sanitizeInt($_POST['academia_id'] ?? 0);

        // Validações obrigatórias
        if (empty($nome) || empty($username) || empty($email) || empty($senha) || empty($cidade) || empty($estado)) {
            Session::setFlash('danger', 'Por favor, preencha todos os campos obrigatórios.');
            header("Location: /cadastro");
            exit();
        }

        if (!Validator::validateEmail($email)) {
            Session::setFlash('danger', 'O formato do e-mail inserido é inválido.');
            header("Location: /cadastro");
            exit();
        }

        if (!Validator::validatePasswordLength($senha, 6)) {
            Session::setFlash('danger', 'A senha deve conter no mínimo 6 caracteres.');
            header("Location: /cadastro");
            exit();
        }

        if (!Validator::matches($senha, $confirmar_senha)) {
            Session::setFlash('danger', 'As senhas não coincidem.');
            header("Location: /cadastro");
            exit();
        }

        $usuarioModel = new UsuarioModel();

        // Verificar unicidade de username e email
        if (!$usuarioModel::verificarUnicidade($username, $email)) {
            Session::setFlash('danger', 'Este nome de usuário ou e-mail já está em uso.');
            header("Location: /cadastro");
            exit();
        }

        // Upload da Foto de Perfil
        $foto_perfil = 'default_avatar.png';
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
            $fileName = $_FILES['foto_perfil']['name'];
            $fileSize = $_FILES['foto_perfil']['size'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedfileExtensions)) {
                if ($fileSize <= 2 * 1024 * 1024) { // Max 2MB
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../../public/assets/img/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }
                    
                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $foto_perfil = $newFileName;
                    }
                } else {
                    Session::setFlash('danger', 'A foto de perfil deve ter no máximo 2MB.');
                    header("Location: /cadastro");
                    exit();
                }
            } else {
                Session::setFlash('danger', 'Extensão de imagem inválida (permitido JPG, JPEG, PNG, GIF).');
                header("Location: /cadastro");
                exit();
            }
        }

        // Criptografar Senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Montar dados
        $dados = [
            'nome' => $nome,
            'username' => $username,
            'email' => $email,
            'senha' => $senha_hash,
            'foto_perfil' => $foto_perfil,
            'bio' => $bio,
            'nivel_fitness' => $nivel_fitness,
            'academia_id' => $academia_id ?: null,
            'cidade' => $cidade,
            'estado' => $estado,
            'peso' => $peso,
            'altura' => $altura
        ];

        // Criar usuário e logar automaticamente
        $usuario_id = $usuarioModel->criar($dados);

        if ($usuario_id) {
            Session::set('usuario_id', $usuario_id);
            Session::set('nome', $nome);
            Session::set('username', $username);
            Session::set('email', $email);
            Session::set('foto_perfil', $foto_perfil);
            Session::set('nivel_fitness', $nivel_fitness);
            Session::set('academia_id', $academia_id ?: null);
            Session::set('cidade', $cidade);
            Session::set('estado', $estado);

            Session::setFlash('success', 'Cadastro realizado com sucesso! Bem-vindo ao GOMOS.');
            header("Location: /feed");
            exit();
        } else {
            Session::setFlash('danger', 'Houve um erro ao criar a conta. Tente novamente.');
            header("Location: /cadastro");
            exit();
        }
    }

    /**
     * Processa a saída da sessão (Logout).
     */
    public function logout() {
        Session::destroy();
        // Não precisamos de mensagem flash persistente, mas podemos setar uma e direcionar
        header("Location: /");
        exit();
    }
}
