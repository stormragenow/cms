<?php
//подключение к бд
function linkdb(){
$servername = "localhost";
$database = "test";
$username =  "root";
$password =  "root";
$link=mysqli_connect( $servername, $username, $password,$database);
return $link; 
}
//удаление из строки бб кдо и вставка html
function replaceBBCode($text_post) {
    $str_search = array(
      "#\\\n#is",
      "#\[b\](.+?)\[\/b\]#is",
      "#\[i\](.+?)\[\/i\]#is",
      "#\[u\](.+?)\[\/u\]#is",     
      "#\[img\](.+?)\[\/img\]#is",
      "#\[\*\](.+?)\[\/\*\]#"
    );
    $str_replace = array(
      "<br />",
      "<b>\\1</b>",
      "<i>\\1</i>",
      "<span style='text-decoration:underline'>\\1</span>",
      "<img height='300' width='300' src='\\1' alt = 'Изображение' />",
      "<li>\\1</li>"
    );
    return preg_replace($str_search, $str_replace, $text_post);
  }
// проверка куков
function validate($cookie_hash){
    if (!is_null($cookie_hash)) {
        $acces='true';    
        $request_result=mysqli_fetch_assoc(linkdb()->query("SELECT user_login,activate,role FROM users WHERE user_hash ='".$cookie_hash ."'"));
        $login=$request_result['user_login'];
        $user_activ=$request_result['activate'];
        $acces_lvl=$request_result['role'];      
        if (!is_null($login)) {
            if ($user_activ==0) {
                $acces='false';
            }
            if ($acces_lvl=='root') {
                $acces=$acces_lvl;
            }
        } else {
            $acces='false';
        }
    }
   else{
       $acces='false';
    }    
    return $acces;
}
//генерация случайныйх символов 
function generateCode($length=6) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
  $code = "";
  $clen = strlen($chars) - 1;
  while (strlen($code) < $length) {
          $code .= $chars[mt_rand(0,$clen)];
  }
  return $code;
}
?>