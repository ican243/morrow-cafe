<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('admin_id')) {
            return redirect()->to(site_url('admin/login'))->with('error', '로그인이 필요합니다.');
        }
    }

    // 관리자 화면은 브라우저·검색엔진이 저장하지 않게
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('Cache-Control', 'no-store, private');
        $response->setHeader('X-Robots-Tag', 'noindex, nofollow');
    }
}
