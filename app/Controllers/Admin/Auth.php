<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Auth extends BaseController
{
    public function loginForm()
    {
        if (session('admin_id')) {
            return redirect()->to(site_url('admin'));
        }

        return view('admin/login');
    }

    public function login()
    {
        $email    = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        // 무차별 대입 방지: 같은 IP, 같은 이메일 각각 5분에 5번까지
        $throttler = service('throttler');
        $ipKey     = 'cafe_login_ip_' . md5($this->request->getIPAddress());
        $emailKey  = 'cafe_login_email_' . md5(strtolower($email));

        if ($throttler->check($ipKey, 5, 300) === false || $throttler->check($emailKey, 5, 300) === false) {
            return redirect()->back()->withInput()->with('error', '로그인 시도가 너무 많습니다. 5분 뒤 다시 시도해 주세요.');
        }

        $model = model(AdminModel::class);
        $admin = $model->where('email', $email)->first();

        if (! $admin || ! password_verify($password, $admin['password'])) {
            return redirect()->back()->withInput()->with('error', '이메일 또는 비밀번호가 올바르지 않습니다.');
        }

        // 해시 방식이 바뀌었으면 로그인하는 김에 새 방식으로 다시 저장
        if (password_needs_rehash($admin['password'], PASSWORD_DEFAULT)) {
            $model->update($admin['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
        }

        // 세션 고정 공격 방지: 로그인 직후 세션 ID 새로 발급
        session()->regenerate(true);
        session()->set([
            'admin_id'    => (int) $admin['id'],
            'admin_name'  => $admin['name'],
            'admin_email' => $admin['email'],
        ]);
        $model->update($admin['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        return redirect()->to(site_url('admin'));
    }

    public function logout()
    {
        session()->remove(['admin_id', 'admin_name', 'admin_email']);
        session()->regenerate(true);

        return redirect()->to(site_url('admin/login'))->with('message', '로그아웃했습니다.');
    }
}
