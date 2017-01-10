<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use League\Flysystem\Exception;

class UserModel extends Model
{
    
    
    
    
    public function __construct() {
        $this->userData = \Auth::user();
    }

    
    
    
    static function validateUserInfo($user) {
        $result = [
            'result' => false,
            'display' => false
        ];

        if(strlen($user['name']) < 2) {
            $result['display'] = 'Имя пользователя слишком короткое';
            return $result;
        }
        if(!$user['name']) {
            $result['display'] = 'Введите имя';
            return $result;
        }
        if($res = preg_match('/[^А-Яа-яЁё]/u', $user['name'])) {
            $result['display'] = 'Имя должно состоять из русских букв';
            return $result;
        }
        if(!strpos($user['email'], '@') || strlen($user['email']) < 5) {
            $result['display'] = 'Введите корректный e-mail';
            return $result;
        }
        if(!$user['surname']) {
            $result['display'] = 'Введите фамилию';
            return $result;
        }
        if(!$user['phone']) {
            $result['display'] = 'Введите телефон';
            return $result;
        }
        if(!$user['birthday']) {
            $result['display'] = 'Введите дату рождения';
            return $result;
        }
        if(!$user['country']) {
            $result['display'] = 'Введите страну';
            return $result;
        }
        if(!$user['city']) {
            $result['display'] = 'Введите город';
            return $result;
        }
        if(!$user['email']) {
            $result['display'] = 'Введите e-mail';
            return $result;
        }

        $result['result'] = true;
        $result['user'] = $user;
        return $result;
    }

    static function getUserInfo($id) {
        $userData = \DB::select("
            select users.name, users.phone, users.email,
            task_first.username, task_first.surname, task_first.birthday, task_first.gender, task_first.country, task_first.city,
            task_second.weight, task_second.waist, task_second.chest, task_second.hip, task_second.date_change
             from users
             inner join task_first on users.id=task_first.user_id
             inner join task_second on users.id=task_second.user_id
              where users.id='".$id."'
        ");
        if($userData['0']->gender == 1) {
            $userData['0']->gender = 'Мужской';
        }
        if($userData['0']->gender == 2) {
            $userData['0']->gender = 'Женский';
        }
        return $userData['0'];
    }

    static function redUserInfo($user) {
        $result = [
            'result' => false,
            'display' => false
        ];

        try {
            \DB::table('users')
                ->join('task_first', function($join){
                    $id = \Auth::id();
                    $join->on('users.id', '=', 'task_first.user_id')
                        ->where('users.id', $id);
                })
                ->update([
                    'users.name' => $user['name'],
                    'task_first.surname' => $user['surname'],
                    'users.phone' => $user['phone'],
                    'users.email' => $user['email'],
                    'task_first.birthday' => $user['birthday'],
                    'task_first.gender' => $user['gender'],
                    'task_first.country' => $user['country'],
                    'task_first.city' => $user['city']
                ]);

        } catch(\Exception $e) {
            if($e->getCode() == 23000) {
                $result['display'] = 'Такой пользователь уже существует';
                return $result;
            }
            else {
                $result['display'] = 'Не удалось обновить информацию';
                return $result;
            }
        }
        $result['result'] = true;

        return $result;

    }

    static function firstTask($user) {
        $result = [
            'result' => false,
            'display' => false
        ];
        $user['password'] = \Hash::make($user['password']);

        try {
            \DB::table('users')
                ->join('task_first', function($join){
                    $id = \Auth::id();
                    $join->on('users.id', '=', 'task_first.user_id')
                        ->where('users.id', $id);
                })
                ->update([
                'task_first.username' => $user['username'],
                    'users.username' => $user['username'],
                'users.password' => $user['password'],
                'users.name' => $user['name'],
                'task_first.surname' => $user['surname'],
                'users.phone' => $user['phone'],
                'users.email' => $user['email'],
                'task_first.birthday' => $user['birthday'],
                'task_first.gender' => $user['gender'],
                'task_first.country' => $user['country'],
                'task_first.city' => $user['city']
            ]);

        } catch(\Exception $e) {
            if($e->getCode() == 23000) {
                $result['display'] = 'Такой пользователь уже существует';
                return $result;
            }
            else {
                $result['display'] = 'Не удалось добавить пользователя';
                return $result;
            }
        }
        $result['result'] = true;

        return $result;
    }

    static function secondTask($user) {
        $result = [
            'result' => false,
            'display' => false
        ];
        $id = \Auth::id();

        try {
            \DB::table('task_second')
                ->where('user_id', $id)
                ->update([
                    'date_change' => date('Y-m-d'),
                    'weight' => $user['weight'],
                    'waist' => $user['waist'],
                    'chest' => $user['chest'],
                    'hip' => $user['hip']
                ]);
        } catch(\Exception $e) {
            if($e->getCode() == 23000) {
                $result['display'] = 'Такой пользователь уже существует';
                return $result;
            }
            else {
                $result['display'] = 'Не удалось добавить пользователя';
                return $result;
            }
        }

        $result['result'] = true;

        return $result;
    }

    static function thirdTask($user) {
        $result = [
            'result' => false,
            'display' => false
        ];
        $id = \Auth::id();

        try {
            \DB::table('task_third')
                ->where('user_id', $id)
                ->update([
                    'weight' => $user['weight'],
                    'length' => $user['length']
                ]);
        } catch(\Exception $e) {
            if($e->getCode() == 23000) {
                $result['display'] = 'Такой пользователь уже существует';
                return $result;
            }
            else {
                $result['display'] = 'Не удалось добавить пользователя';
                return $result;
            }
        }

        $result['result'] = true;

        return $result;
    }

    static function addReport($message) {
        $result = [
            'result' => false,
            'display' => false
        ];
        $id = \Auth::id();

        try {
            \DB::table('reports')
                ->insert([
                    'user_id' => $id,
                    'message' => $message
                ]);
        } catch(\Exception $e) {
            if($e->getCode() == 23000) {
                $result['display'] = 'Такой отчет уже был добавлен ранее';
                return $result;
            }
            else {
                $result['display'] = 'Не удалось добавить отчет';
                return $result;
            }
        }

        $result['result'] = true;
        $result['message'] = $message;

        return $result;
    }

    static function addQuest($message) {
        $result = [
            'result' => false,
            'display' => false
        ];
        $id = \Auth::id();

        if($message != '') {
            try {
                \DB::table('questions')
                    ->insert([
                        'user_id' => $id,
                        'message' => $message
                    ]);
            } catch(\Exception $e) {
                if($e->getCode() == 23000) {
                    $result['display'] = 'Такой вопрос уже был добавлен ранее';
                    return $result;
                }
                else {
                    $result['display'] = 'Не удалось добавить вопрос';
                    return $result;
                }
            }
        } else {
            $result['display'] = 'Введите ваш вопрос';
            return $result;
        }

        $result['result'] = true;
        $result['message'] = $message;

        return $result;
    }

    static function getAllReports($id) {
        $message = \DB::select("select message from reports where user_id='".$id."'");

        return $message;
    }
}
