<?php

namespace App\Controllers\Admin;

use App\Models\AdminModel;

class Account extends AdminController
{
    public function index()
    {
        return $this->render('account', [
            'title' => '관리자 비밀번호 변경',
            'nav'   => 'account',
            'admin' => model(AdminModel::class)->find(session('admin_id')),
        ]);
    }

    public function password()
    {
        $model   = model(AdminModel::class);
        $admin   = $model->find(session('admin_id'));
        $current = (string) $this->request->getPost('current_password');
        $new     = (string) $this->request->getPost('new_password');
        $confirm = (string) $this->request->getPost('new_password_confirm');

        if (! password_verify($current, $admin['password'])) {
            return $this->backWithErrors('현재 비밀번호가 올바르지 않습니다.');
        }
        if (mb_strlen($new) < 10) {
            return $this->backWithErrors('새 비밀번호는 10자 이상으로 정해 주세요.');
        }
        if (! preg_match('/[A-Za-z]/', $new) || ! preg_match('/\d/', $new)) {
            return $this->backWithErrors('새 비밀번호에 영문과 숫자를 모두 넣어 주세요.');
        }
        if ($new !== $confirm) {
            return $this->backWithErrors('새 비밀번호 확인이 일치하지 않습니다.');
        }
        if (password_verify($new, $admin['password'])) {
            return $this->backWithErrors('지금 쓰는 비밀번호와 다른 비밀번호로 정해 주세요.');
        }

        $model->update($admin['id'], [
            'password'            => password_hash($new, PASSWORD_DEFAULT),
            'password_changed_at' => date('Y-m-d H:i:s'),
        ]);

        // 비밀번호를 바꾸면 세션 ID 도 새로
        session()->regenerate(true);

        return redirect()->to(site_url('admin/account'))->with('message', '비밀번호를 변경했습니다.');
    }
}
