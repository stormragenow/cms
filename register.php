<?php
include_once"function.php";
$acces=validate($_COOKIE['hash']);  
  if($acces=='True' or $acces=='root'){      
    header("Location: / ");  
  }

if (!isset($_SESSION)) { session_start(); }
    $link=linkdb();
    
    if(isset($_POST['sendreg'])){
      $err = [];
      // проверям логин
      if(!preg_match("/[0-9a-z]+@[a-z]/",$_POST['login']))
      {
          $err[] = "Неверный формат почты";
      }
  
      if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)
      {
          $err[] = "Почта должна быть не меньше 3-х символов и не больше 30";
      }
      $login=$_POST['login'];
      
      // проверяем, не сущестует ли пользователя с таким именем
      $query = mysqli_query($link, "SELECT user_id FROM users WHERE user_login='".$_POST['login']."'");
      if(mysqli_num_rows($query) > 0)
      {
          $err[] = "Пользователь с такой почтой уже отправлял заявку";
      }
      if(count($err) == 0)
      {
        if(!mysqli_query($link,
        "INSERT INTO users SET user_login='" .$login. "', user_password='', role='user'")){
            $_SESSION['message']="Неправильный запрос в базу данных";       
        }
        else{
          $_SESSION['message']='Заявка успешно отправленна';
        }
      }
      else{
        $_SESSION['message']=implode(" ",$err);
      }
    }
?>
<!doctype html>
<html lang="ru">

<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="main.css"/>
  <title>Заметки</title>
</head>
<body class="text-center">
  <nav class="haeder navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-xxl">
      <a class="navbar-brand" href="/">Заметки</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarScroll">
        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="/register">Регистрация</a>
          </li>
          <li class="nav-item">
            <a class="nav-link"  aria-current="page" href="/login">Авторизация</a>
          </li>
      
      </div>
    </div>
  </nav>
<!-- Modal -->

<div class="modal fade" id="auth" tabindex="-1" aria-labelledby="authModalW" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="authModalW">Войти в личный кабинет</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
          <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon1">Электронная почта</span>
          <input type="email" class="form-control" placeholder="Электронная почта" aria-label="Электронная почта" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon1">Пароль</span>
          <input type="password" class="form-control" placeholder="Пароль" aria-label="Пароль" aria-describedby="basic-addon1">
        </div>
         
        </div>
        
      </div>
      <div class="modal-footer">  
        <button type="button" class="btn btn-primary" >Войти</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal end -->
<form  method="post" style="margin: 10% 40% 10% 40%;" class="justify-content-center" >
  <svg xmlns="http://www.w3.org/2000/svg" width="92" height="92" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
  <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
</svg>
  <h1 class="h3 mb-3 font-weight-normal" style="color: #ffffff;">Блог для заметок</h1>
  <input type="email" name="login" class="form-control" placeholder="Электронная почта" required autofocus>
<?
      if ((isset($_SESSION['message']))) {
        echo '<div class="alert alert-warning" role="alert"> ' . $_SESSION ['message'] . ' </div>';
      }
      unset($_SESSION['message']);
?> 
  <button style="margin:5px;"  class="btn btn-lg btn-primary btn-block" name="sendreg">Отправить заявку на регистрацию</button>
  <button style="margin:5px;"  class="btn btn-lg btn-dark btn-block" data-bs-toggle="modal" data-bs-target="#auth">уже есть аккаунт?</button>

</form>
  <script src="/js/bootstrap.js"></script>
</body>

</html>