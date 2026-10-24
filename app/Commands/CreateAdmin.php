<?php

namespace App\Commands;

use App\Models\AdminModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * 관리자 계정 만들기. 비밀번호는 코드·문서에 남기지 않고 무작위로 만들어 한 번만 보여준다.
 *
 *   php spark cafe:admin owner@example.com 사장님
 */
class CreateAdmin extends BaseCommand
{
    protected $group       = 'Cafe';
    protected $name        = 'cafe:admin';
    protected $description = '관리자 계정을 만들고 임시 비밀번호를 발급합니다.';
    protected $usage       = 'cafe:admin <email> [name]';

    public function run(array $params)
    {
        $email = $params[0] ?? CLI::prompt('관리자 이메일', null, 'required|valid_email');
        $name  = $params[1] ?? '관리자';

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            CLI::error('이메일 형식이 올바르지 않습니다.');

            return EXIT_ERROR;
        }

        $model = model(AdminModel::class);
        if ($model->where('email', $email)->first()) {
            CLI::error('이미 있는 이메일입니다.');

            return EXIT_ERROR;
        }

        $password = rtrim(strtr(base64_encode(random_bytes(12)), '+/', 'Kq'), '=');
        $model->insert([
            'email'    => $email,
            'name'     => $name,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        CLI::write('관리자 계정을 만들었습니다.', 'green');
        CLI::write('  이메일: ' . $email);
        CLI::write('  임시 비밀번호: ' . $password, 'yellow');
        CLI::write('로그인 후 바로 비밀번호를 바꿔 주세요. (이 비밀번호는 다시 볼 수 없습니다)');

        return EXIT_SUCCESS;
    }
}
