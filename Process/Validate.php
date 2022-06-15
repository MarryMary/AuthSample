<?php
function EmailValid(String $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function PasswordValid(String $password): bool
{
    $max = 16;
    $min = 8;
    if(trim($password) <= $max && trim($password) >= $min){
        $flag = False;
        $mark = ['?', '!', '#', ','];
        foreach($mark as $m){
            if(strpos($password,$m) !== false){
                $flag = True;
            }
        }
        return $flag;
    }else{
        return False;
    }
}

function UserNameValid(String $UserName): bool
{
    $max = 50;
    $min = 1;
    if(trim($UserName) <= $max && trim($UserName) >= $min){
        return True;
    }else{
        return False;
    }
}