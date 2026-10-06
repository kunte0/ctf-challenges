<?php


function isString(array &$array, $key)
{
    return (isset($array[$key]) && is_string($array[$key]));
}

function getString(array &$array, $key, $default='')
{
    return (isString($array, $key)) ? $array[$key] : $default;
}
function ms($s){
    return htmlspecialchars($s, ENT_QUOTES);
}
function getIp(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } 
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } 
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
function loggedIn() {
    return isset($_SESSION['username']);
}

function isAdmin(){
    return getString($_SESSION, 'id') === "1";
}
