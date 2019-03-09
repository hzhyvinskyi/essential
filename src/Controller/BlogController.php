<?php

namespace App\Controller;

use App\Service\Greeting;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BlogController
 * @package App\Controller
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @var Greeting
     */
    private $greeting;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(Greeting $greeting, RouterInterface $router, SessionInterface $session)
    {
        $this->greeting = $greeting;
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * @Route("/", name="blog_index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'posts' => $this->session->get('posts')
        ]);
    }

    /**
     * @Route("/store", name="blog_store")
     * @return RedirectResponse
     * @throws \Exception
     */
    public function store(): RedirectResponse
    {
        $posts = $this->session->get('posts');
        $posts[uniqid()] = [
            'title' => 'Post example title ' . rand(1, 1000),
            'body' => 'Post example body ' . rand(1, 1000),
            'date' => new \DateTime()
        ];
        $this->session->set('posts', $posts);

        return new RedirectResponse($this->router->generate('blog_index'));
    }

    /**
     * @Route("/{id}", name="blog_show")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id): Response
    {
        $posts = $this->session->get('posts');

        if (!$posts || !isset($posts[$id])) {
            throw new NotFoundHttpException('Post not found');
        }

        return $this->render('blog/show.html.twig', [
            'id' => $id,
            'post' => $posts[$id]
        ]);
    }
}
