<?php

namespace App\Controllers;

use App\Exceptions\EmailNotSendedException;
use App\Models\UsuarioModel;
use App\Services\EmailService;
use App\Support\FlashMessage;
use App\Support\FormValidation;
use App\Support\LoggerFactory;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Rules\Email;
use Respect\Validation\Validator;

class AuthController extends BaseController
{
    private UsuarioModel $usuarioModel;
    private EmailService $emailService;
    private $logger;

    public function __construct() {
        parent::__construct();
        $this->usuarioModel = new UsuarioModel();
        $this->emailService = new EmailService();
        $this->logger = LoggerFactory::create('auth');
    }

    public function cadastroForm(): ResponseInterface
    {
        $flashMessage = FlashMessage::get();

        return new HtmlResponse(
            $this->render('auth/cadastro.twig', ['flashMessage' => $flashMessage])
        );
    }

    public function cadastro(ServerRequest $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validator = new FormValidation();

        $rules = [
            'nome' => Validator::stringType()->notEmpty()->length(3, 100),
            'email' => Validator::stringType()->notEmpty()->email(),
            'password' => Validator::stringType()->notEmpty()->length(6, 100),
            'password_confirmation' => Validator::stringType()->notEmpty()->length(6, 100)->equals($data['password'] ?? ''),
        ];

        $messages = [
            'nome.stringType' => 'O nome deve ser uma string.',
            'nome.notEmpty' => 'O nome é obrigatório.',
            'nome.length' => 'O nome deve ter entre 3 e 100 caracteres.',
            'email.stringType' => 'O email deve ser uma string.',
            'email.notEmpty' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser válido.',
            'password.stringType' => 'A senha deve ser uma string.',
            'password.notEmpty' => 'A senha é obrigatória.',
            'password.length' => 'A senha deve ter entre 6 e 100 caracteres.',
            'password_confirmation.stringType' => 'A confirmação de senha deve ser uma string.',
            'password_confirmation.notEmpty' => 'A confirmação de senha é obrigatória.',
            'password_confirmation.length' => 'A confirmação de senha deve ter entre 6 e 100 caracteres.',
            'password_confirmation.equals' => 'As senhas não coincidem.',
        ];

        $errors = $validator->validate($data, $rules, $messages);

        if (!empty($errors)) {
            return new HtmlResponse(
                $this->render('auth/cadastro.twig', [
                    'errors' => $errors,
                    'old' => $data
                ]),
                422
            );
        }

        $this->usuarioModel->criar($data);

        FlashMessage::set('success', 'Usuário cadastrado com sucesso.');
        return new RedirectResponse('/login');
    }

    public function loginForm(): ResponseInterface
    {
        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;

        $flashMessage = FlashMessage::get();

        return new HtmlResponse(
            $this->render('auth/login.twig', ['flashMessage' => $flashMessage, 'csrfToken' => $csrfToken])
        );
    }

    public function login(ServerRequest $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        $csrfToken = $data['csrf_token'] ?? '';

        if (!$csrfToken || $csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
            FlashMessage::set('error', 'Token CSRF inválido.');
            unset($_SESSION['csrf_token']);
            return new RedirectResponse('/login');
        }

        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $usuario = $this->usuarioModel->buscarPorEmail($email, $password);

        if (!$usuario) {
            FlashMessage::set('error', 'Email ou senha inválidos.');
            return new RedirectResponse('/login');
        }

        $usuarioValido = password_verify($password, $usuario['senha']);

        if (!$usuarioValido) {
            FlashMessage::set('error', 'Email ou senha inválidos.');
            return new RedirectResponse('/login');
        }

        $_SESSION['user'] = $usuario;
        return new RedirectResponse('/produtos');
    }

    public function logout(): ResponseInterface
    {
        unset($_SESSION['user']);

        return new RedirectResponse('/login');
    }

    public function lembrarSenhaForm(): ResponseInterface
    {
        $flashMessage = FlashMessage::get();

        return new HtmlResponse(
            $this->render('auth/lembrar_senha.twig', ['flashMessage' => $flashMessage])
        );
    }

    public function lembrarSenha(ServerRequest $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $email = $data['email'] ?? '';

        $tokenLembrarSenha = bin2hex(random_bytes(16));

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if (!$usuario) {
            FlashMessage::set('error', 'Nenhum usuário encontrado com esse email.');
            return new RedirectResponse('/lembrar-senha');
        }

        $usuario['token_lembrar_senha'] = $tokenLembrarSenha;        

        $this->usuarioModel->atualizar($usuario);

        try {
            $this->emailService->enviarEmail(
                $email,
                'Instruções para Recuperação de Senha',
                "Olá,<br><br>Clique no link abaixo para redefinir sua senha:<br><a href='http://localhost:9000/redefinir-senha?token={$tokenLembrarSenha}'>Redefinir Senha</a><br><br>Se você não solicitou essa alteração, ignore este email."
            );
        } catch (EmailNotSendedException $e) {
            $this->logger->error("Erro ao enviar email para {email}: {error}", [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            FlashMessage::set('error', 'Não foi possível enviar o email de recuperação de senha. Por favor, tente novamente mais tarde.');
            return new RedirectResponse('/lembrar-senha');
        }

        FlashMessage::set('success', 'Instruções para recuperação de senha foram enviadas para ' . htmlspecialchars($email) . '.');
        return new RedirectResponse('/login');
    }

    public function redefinirSenhaForm(ServerRequest $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $token = $queryParams['token'] ?? '';

        $usuario = $this->usuarioModel->buscarPorTokenRedefinicao($token);

        if (!$usuario) {
            FlashMessage::set('error', 'Token inválido ou expirado para redefinição de senha.');
            return new RedirectResponse('/login');
        }

        $flashMessage = FlashMessage::get();

        return new HtmlResponse(
            $this->render('auth/redefinir_senha.twig', [
                'flashMessage' => $flashMessage,
                'token' => $token
            ])
        );
    }

    public function redefinirSenha(ServerRequest $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $token = $data['token'] ?? '';
        $newPassword = $data['password'] ?? '';
        $confirmPassword = $data['password_confirmation'] ?? '';

        if ($newPassword !== $confirmPassword) {
            FlashMessage::set('error', 'As senhas não coincidem.');
            return new RedirectResponse('/redefinir-senha?token=' . urlencode($token));
        }

        $usuario = $this->usuarioModel->buscarPorTokenRedefinicao($token);

        if (!$usuario) {
            FlashMessage::set('error', 'Token inválido ou expirado para redefinição de senha.');
            return new RedirectResponse('/login');
        }

        $usuario['senha'] = password_hash($newPassword, PASSWORD_BCRYPT);
        $usuario['token_lembrar_senha'] = null;

        $this->usuarioModel->atualizar($usuario);

        FlashMessage::set('success', 'Senha redefinida com sucesso. Agora você pode fazer login com sua nova senha.');
        return new RedirectResponse('/login');
    }
}
