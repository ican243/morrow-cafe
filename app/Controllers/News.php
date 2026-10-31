<?php

namespace App\Controllers;

use App\Models\PostModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class News extends SiteController
{
    public function index(): string
    {
        $type  = $this->request->getGet('type');
        $model = model(PostModel::class)->withMedia()->published();
        if (in_array($type, ['notice', 'event'], true)) {
            $model->where('posts.type', $type);
        } else {
            $type = null;
        }

        $posts = $model->orderBy('posts.is_pinned', 'DESC')->orderBy('posts.published_at', 'DESC')->paginate(9);

        return $this->render('news', [
            'title'     => 'News',
            'navActive' => 'news',
            'description' => '{매장}의 공지사항과 이벤트 소식.',
            'posts'     => $posts,
            'pager'     => $model->pager,
            'type'      => $type,
            'types'     => config(\Config\Cafe::class)->postTypes,
        ]);
    }

    public function show(int $id): string
    {
        $model = model(PostModel::class);
        $post  = $model->findPublic($id);
        if (! $post) {
            throw PageNotFoundException::forPageNotFound();
        }

        [$prev, $next] = $model->neighbors($post);

        return $this->render('news_show', [
            'title'       => $post['title'],
            'description' => $post['summary'] ?: mb_substr(strip_tags($post['body']), 0, 120),
            'navActive'   => 'news',
            'post'        => $post,
            'prev'        => $prev,
            'next'        => $next,
            'types'       => config(\Config\Cafe::class)->postTypes,
            'ogImage'     => media_url($post['media_path'], 'lg'),
        ]);
    }
}
