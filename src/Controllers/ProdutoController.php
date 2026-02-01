<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Support\FlashMessage;
use App\Support\FormValidation;
use App\Support\Upload;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator;

class ProdutoController extends BaseController
{
    private ProdutoModel $produtoModel;

    public function __construct() {
        parent::__construct();
        $this->produtoModel = new ProdutoModel();
    }

    public function index(ServerRequest $request): ResponseInterface
    {
        $flashMessage = FlashMessage::get();

        $data = $request->getQueryParams();

        $paginaAtual = (int) ($data['pagina'] ?? 1);
        $totalDePaginas = (int) ceil($this->produtoModel->contarProdutos() / 5);

        $produtos = $this->produtoModel->listarProdutos($paginaAtual);

        return new HtmlResponse(
            $this->render('produtos/listar.twig', compact('produtos', 'totalDePaginas', 'paginaAtual', 'flashMessage'))
        );
    }

    public function show(ServerRequest $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        $produto = $this->produtoModel->buscarProdutoPorId($id);        

        return new HtmlResponse(
            $this->render('produtos/ver.twig', compact('produto'))
        );
    }

    public function createForm(): ResponseInterface
    {
        return new HtmlResponse(
            $this->render('produtos/criar.twig')
        );
    }

    public function store(ServerRequest $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validator = new FormValidation();

        $rules = [
            'titulo' => Validator::stringType()->notEmpty()->length(3, 100),
            'descricao' => Validator::stringType()->notEmpty()->length(3, 255),
            'preco' => Validator::floatVal()->positive(),
        ];

        $messages = [
            'titulo.length' => 'O nome não pode ficar vazio.',
            'descricao.length' => 'A descrição não pode ficar vazia.',
            'preco.floatVal' => 'O preço deve ser numérico.',
            'preco.positive' => 'O preço deve ser maior que zero.',
        ];

        $errors = $validator->validate($data, $rules, $messages);

        if (!empty($errors)) {
            return new HtmlResponse(
                $this->render('produtos/criar.twig', [
                    'errors' => $errors,
                    'old' => $data
                ]),
                422
            );
        }

        $uploadedFiles = $request->getUploadedFiles();
        $foto = $uploadedFiles['foto'] ?? null;

        if ($foto) {
            $data['foto_path'] = Upload::handle($foto);
        }

        $this->produtoModel->criarProduto($data);

        FlashMessage::set('success', 'Produto criado com sucesso!');

        return new RedirectResponse('/produtos');
    }

    public function editForm(ServerRequest $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        $produto = $this->produtoModel->buscarProdutoPorId($id);

        return new HtmlResponse(
            $this->render('produtos/editar.twig', compact('produto'))
        );
    }

    public function update(ServerRequest $request): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $data = $request->getParsedBody();

        $validator = new FormValidation();

        $rules = [
            'titulo' => Validator::stringType()->notEmpty()->length(3, 100),
            'descricao' => Validator::stringType()->notEmpty()->length(3, 255),
            'preco' => Validator::floatVal()->positive(),
        ];

        $messages = [
            'titulo.length' => 'O nome não pode ficar vazio.',
            'descricao.length' => 'A descrição não pode ficar vazia.',
            'preco.floatVal' => 'O preço deve ser numérico.',
            'preco.positive' => 'O preço deve ser maior que zero.',
        ];

        $errors = $validator->validate($data, $rules, $messages);

        if (!empty($errors)) {
            $produto = $this->produtoModel->buscarProdutoPorId($id);

            return new HtmlResponse(
                $this->render('produtos/editar.twig', [
                    'errors' => $errors,
                    'produto' => $produto
                ]),
                422
            );
        }

        $uploadedFiles = $request->getUploadedFiles();
        $foto = $uploadedFiles['foto'] ?? null;

        if ($foto) {
            $data['foto_path'] = Upload::handle($foto);
        }

        $this->produtoModel->atualizarProduto($id, $data);

        FlashMessage::set('success', 'Produto atualizado com sucesso!');

        return new RedirectResponse('/produtos');
    }

    public function delete(ServerRequest $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $produtoId = (int) ($data['produto_id'] ?? 0);

        try {
            $this->produtoModel->excluirProduto($produtoId);
            FlashMessage::set('success', 'Produto excluído com sucesso!');
        } catch (\Exception $e) {
            FlashMessage::set('error', 'Erro ao excluir o produto.');
        }

        return new RedirectResponse('/produtos');
    }
}
