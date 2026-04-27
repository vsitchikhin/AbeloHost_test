<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Post;

class PostController
{
    private View $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function show(int $id): void
    {
        $post = Post::findById($id);

        if ($post === null) {
            http_response_code(404);
            $this->view->render('404.tpl');
            return;
        }

        Post::incrementViews($id);

        /** @var list<array<string, mixed>> $postCategories */
        $postCategories = (array) $post['categories'];

        /** @var list<int> $categoryIds */
        $categoryIds = array_map(
            static fn (mixed $cat): int => (int) (is_array($cat) ? $cat['id'] : 0),
            $postCategories
        );

        $similar = Post::getSimilar($id, $categoryIds);

        $this->view->render('post.tpl', [
            'post'    => $post,
            'similar' => $similar,
        ]);
    }
}
