<?
  include_once"function.php";
 $acces= validate($_COOKIE['hash']);
if($acces=='true' or $acces=='root'){header("Location: / ");}
if (!isset($_SESSION)) { session_start();}
$link=linkdb();

if (isset($_POST['sendrequest'])) {
 $login_reg=$_POST['regemail'];
     $query = mysqli_query($link, "SELECT user_id FROM users WHERE user_login='".$login_reg."'");
     if (mysqli_num_rows($query) > 0) {
         $_SESSION['message'] = "Пользователь с такой почтой уже отправлял заявку";
     } else {
         if (!mysqli_query(
             $link,
             "INSERT INTO users SET user_login='" .$login_reg. "', user_password='', role='user'")){
             $_SESSION['message']="Неправильный запрос в базу данных";
         }
     }
    }

if(isset($_POST['auth']))
{
    $query = mysqli_query($link,"SELECT user_id, user_password FROM users WHERE user_login='".mysqli_real_escape_string($link,$_POST['email'])."' LIMIT 1");
    $data = mysqli_fetch_assoc($query);
    if (is_null($data)) {
        $_SESSION['message']='Неверный адрес электронной почты';
    } else{
        if ($data['user_password'] === $_POST['password']) {
            $hash = md5(generateCode(10));

          if(mysqli_query($link, "UPDATE users SET user_hash='".$hash."' WHERE user_id='".$data['user_id']."'"))
          {
              // Ставим куки
              setcookie("id", $data['user_id'], time()+60*60*24*30, "/");
              setcookie("hash", $hash, time()+60*60*24*30, "/", null, null, true); 
              header("Location: index");
            }
            else{
              $_SESSION['message']="проблемы с авторизацией база данных не доступна";
            }            
        } else {
            $_SESSION['message']='Неверный пароль';
        }
          }
}
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="main.css" />
  <link class="main-ico"  href="/images/ico.png" rel="shortcut icon">
</head>
<body class="text-center">
  <nav class="haeder navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-xxl">
      <a class="navbar-brand" href="#">Заметки</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarScroll">
        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="/login">Авторизация</a>
          </li>
          <li class="nav-item" >
            <a class="nav-link" style="cursor: pointer;" href="/register" >Регистрация</a>
          </li>      
      </div>
    </div>
  </nav>
<!-- Modal -->
<div class="modal fade" id="reg" tabindex="-1" aria-labelledby="regModalW" aria-hidden="true">
  <form class="modal-dialog-centered" method="post">
  <div class="modal-dialog modal-dialog-centered">    
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reghModalW">Отправьте заявку на регистрацию</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
          <div class="input-group mb-3">
          <span class="input-group-text" id="basic-addon1">Электронная почта</span>
          <input name="regemail" type="email" class="form-control" placeholder="Электронная почта" aria-label="Электронная почта" aria-describedby="basic-addon1">
        </div>                
        </div>
      </div>
      <div class="modal-footer">
        <input  class="btn btn-primary" type="submit" name="sendrequest"  value="Отправить заявку">
      </div>
    </div>
    </form>
  </div>
</div>
<!-- Modal end -->
<form style="margin: 10% 40% 10% 40%;" class="justify-content-center align-items-center" method="post"> 
<svg xmlns="http://www.w3.org/2000/svg" width="92" height="92" fill="currentColor" class="bi bi-book" viewBox="0 0 16 16">
  <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
</svg>
  <h1 class="h3 mb-3 font-weight-normal" style="color: #ffffff;">Блог для заметок</h1>
  <input style="margin: 2px;" type="email" name="email" id="inputEmail" class="form-control" placeholder="Электронная почта" required>
  <input style="margin: 2px;" type="password" name="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
  
  <input style="margin: 2px;width:100%;" class="btn btn-lg btn-primary btn-block" value="Войти" name="auth" type="submit">
  <button style="margin: 2px;width:100%;" class="btn btn-lg btn-dark btn-block" data-bs-toggle="modal" data-bs-target="#reg">нет аккаунта?</button>
<?
      if ((isset($_SESSION['message']))and (!is_null($_POST['regemail']))) {
         echo '<div class="alert alert-warning" role="alert"> ' . $_SESSION ['message'] . ' </div>';
      }
      unset($_SESSION['message']);
?>
</form>
  <script src="/js/bootstrap.js"></script>
</body>
</html>