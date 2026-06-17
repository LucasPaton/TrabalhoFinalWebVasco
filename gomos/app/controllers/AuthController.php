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
            header("Location: " . BASE_PATH . "/feed");
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
            header("Location: " . BASE_PATH . "/login");
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
            header("Location: " . BASE_PATH . "/admin");
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
            header("Location: " . BASE_PATH . "/feed");
            exit();
        }

        Session::setFlash('danger', 'Usuário ou senha incorretos.');
        header("Location: " . BASE_PATH . "/login");
        exit();
    }

    /**
     * Exibe a página de cadastro.
     */
    public function cadastroPagina() {
        if (Session::has('usuario_id')) {
            header("Location: " . BASE_PATH . "/feed");
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
        $nome = Validator::formatarNome($nome);

        $username = Validator::sanitize($_POST['username'] ?? '');
        $username = Validator::formatarUsername($username);

        $email = Validator::sanitize($_POST['email'] ?? '');
        $email = Validator::formatarEmail($email);

        $senha = $_POST['senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';
        
        $nivel_fitness = Validator::sanitize($_POST['nivel_fitness'] ?? 'iniciante');
        $peso = Validator::sanitizeFloat($_POST['peso'] ?? 0);
        $altura = Validator::sanitizeInt($_POST['altura'] ?? 0);
        
        $bio = Validator::sanitize($_POST['bio'] ?? '');
        $bio = trim($bio);
        
        $cidade = Validator::sanitize($_POST['cidade'] ?? '');
        $cidade = Validator::formatarCidade($cidade);

        $estado = Validator::sanitize($_POST['estado'] ?? '');
        $estado = Validator::formatarEstado($estado);

        $academia_id = Validator::sanitizeInt($_POST['academia_id'] ?? 0);

        // 1. Validações obrigatórias
        if (empty($nome) || empty($username) || empty($email) || empty($senha) || empty($cidade) || empty($estado)) {
            Session::setFlash('danger', 'Por favor, preencha todos os campos obrigatórios.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }

        // 2. Validação de Nome
        if (strlen($nome) < 3 || strlen($nome) > 60) {
            Session::setFlash('danger', 'O nome completo deve conter entre 3 e 60 caracteres.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }
        if (!preg_match("/^[a-zA-ZÀ-ÿ\s]+$/u", $nome)) {
            Session::setFlash('danger', 'O nome completo deve conter apenas letras e espaços.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }

        // 3. Validação de Username (Nome de Usuário)
        if (strlen($username) < 3 || strlen($username) > 20) {
            Session::setFlash('danger', 'O nome de usuário (username) deve conter entre 3 e 20 caracteres.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }
        if (!preg_match("/^[a-zA-Z0-9_\-\.]+$/", $username)) {
            Session::setFlash('danger', 'O nome de usuário deve conter apenas letras, números, pontos, traços ou sublinhados (sem espaços).');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }

        // 4. Validação de E-mail
        if (strlen($email) > 100) {
            Session::setFlash('danger', 'O e-mail não deve exceder 100 caracteres.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }
        if (!Validator::validateEmail($email)) {
            Session::setFlash('danger', 'O formato do e-mail inserido é inválido.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }

        // 5. Validação de Senha
        if (strlen($senha) < 6 || strlen($senha) > 32) {
            Session::setFlash('danger', 'A senha deve conter entre 6 e 32 caracteres.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }
        if (!Validator::matches($senha, $confirmar_senha)) {
            Session::setFlash('danger', 'As senhas inseridas não coincidem.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }

        // 6. Validação de Cidade e Estado
        if (strlen($cidade) < 2 || strlen($cidade) > 50) {
            Session::setFlash('danger', 'A cidade deve conter entre 2 e 50 caracteres.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }
        if (strlen($estado) !== 2) {
            Session::setFlash('danger', 'O estado deve conter exatamente 2 caracteres.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }

        // 7. Validação de Biografia
        if (!empty($bio) && strlen($bio) > 250) {
            Session::setFlash('danger', 'A biografia não deve ultrapassar 250 caracteres.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }

        // 8. Validação de Peso e Altura
        if ($peso > 0 && ($peso < 30 || $peso > 300)) {
            Session::setFlash('danger', 'Por favor, insira um peso realista entre 30kg e 300kg.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }
        if ($altura > 0 && ($altura < 100 || $altura > 250)) {
            Session::setFlash('danger', 'Por favor, insira uma altura realista entre 100cm e 250cm.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }

        $usuarioModel = new UsuarioModel();
        $db = \App\Config\Database::getConnection();

        // 9. Verificar se o nome de usuário já está cadastrado
        $stmt_check_user = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE username = :username");
        $stmt_check_user->execute([':username' => $username]);
        if ($stmt_check_user->fetchColumn() > 0) {
            Session::setFlash('danger', 'Este nome de usuário (username) já está em uso.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }

        // 10. Verificar se o e-mail já está cadastrado
        $stmt_check_email = $db->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
        $stmt_check_email->execute([':email' => $email]);
        if ($stmt_check_email->fetchColumn() > 0) {
            Session::setFlash('danger', 'Este endereço de e-mail já está em uso.');
            header("Location: " . BASE_PATH . "/cadastro");
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
                    header("Location: " . BASE_PATH . "/cadastro");
                    exit();
                }
            } else {
                Session::setFlash('danger', 'Extensão de imagem inválida (permitido JPG, JPEG, PNG, GIF).');
                header("Location: " . BASE_PATH . "/cadastro");
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
            header("Location: " . BASE_PATH . "/feed");
            exit();
        } else {
            Session::setFlash('danger', 'Houve um erro ao criar a conta. Tente novamente.');
            header("Location: " . BASE_PATH . "/cadastro");
            exit();
        }
    }

    /**
     * Processa a saída da sessão (Logout).
     */
    public function logout() {
        Session::destroy();
        // Não precisamos de mensagem flash persistente, mas podemos setar uma e direcionar
        header("Location: " . BASE_PATH . "/");
        exit();
    }
}
