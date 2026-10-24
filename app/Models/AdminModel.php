<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table          = 'admins';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $allowedFields  = ['email', 'password', 'name', 'last_login_at', 'password_changed_at'];
}
