<?php
namespace App\Controllers;

use App\Models\Post;

class PostController
{
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $post = $id ? Post::find($id) : null;
        require __DIR__ . '/../../views/post.php';
    }
}

