<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\Category;
use App\Models\Post;
use App\Models\Article;
use App\Models\Upload;

class DashboardController extends BaseController
{
    public function index()
    {
        $total_categories = Category::count();
        $total_posts = Post::count();
        $total_article = Article::count();

        $categories = Category::withCount(['posts', 'articles'])
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
        foreach ($categories as $category) {
            $upload_ids = json_decode($category->upload_id, true);
            if ($upload_ids) {
                $category->uploads = Upload::whereIn('id', $upload_ids)->get();
            }
        }
        $posts = Post::latest()->where('status', 'published')->limit(10)->get();
        foreach ($posts as $post) {
            $post->post_meta = $post->postMeta()->get();
            $upload_ids = json_decode($post->upload_id, true);
            if ($upload_ids) {
                $post->uploads = Upload::whereIn('id', $upload_ids)->get();
            }
        }
        $articles = Article::latest()->where('status', 'published')->limit(10)->get();
        foreach ($articles as $article) {
            $upload_ids = json_decode($article->upload_id, true);
            if ($upload_ids) {
                $article->uploads = Upload::whereIn('id', $upload_ids)->get();
            }
        }
        $data = [
            'total_categories' => $total_categories,
            'total_posts' => $total_posts,
            'total_article' => $total_article,
            'categories' => $categories,
            'posts' => $posts,
            'articles' => $articles,
        ];

        return $this->handleResponse($data, 'Dashboard data');
    }
}
