<?php
/*
 * 値のバリデーションや変数の安全なバインドを行う関数を所有するファイル
 */

// htmlspecialcharsのラッパー
function h(String $text) :string
{
    return htmlspecialchars($text);
}

// メールアドレスが正しい形であるか審査します。
function EmailValid(String $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// パスワードが要求した形であるかを審査します。
function PasswordValid(String $password): bool
{
    $max = 16;
    $min = 8;
    if(strlen(trim($password)) <= $max && strlen(trim($password)) >= $min){
        $flag = False;
        $mark = ['?', '!', '#', ','];
        foreach($mark as $m){
            if(strpos($password,$m) !== false){
                $flag = True;
                return $flag;
            }
        }
        return $flag;
    }else{
        return False;
    }
}

// ユーザー名が要求した形であるかを審査します。
function UserNameValid(String $UserName): bool
{
    $max = 50;
    $min = 1;
    if(strlen(trim($UserName)) <= $max && strlen(trim($UserName)) >= $min){
        return True;
    }else{
        return False;
    }
}