<?php
include_once"function.php";
if (!isset($_SESSION)) { session_start(); }
$link=linkdb();
$request_on_base="SELECT * FROM users WHERE user_hash ='".$_COOKIE['hash'] ."'";   
$request_result=mysqli_fetch_assoc($link->query($request_on_base));
$user_role=$request_result['role'];

if($user_role=="root"){

// проверям данные регистрации
if (isset($_POST['regUser'])) {
    $err = [];
    
    if (!preg_match("/[0-9a-z]+@[a-z]/", $_POST['login'])) {
        $err[] = "Логин может состоять только из букв английского алфавита и цифр";
    }

    if (strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30) {
        $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
    }

    $query = mysqli_query($link, "SELECT user_id FROM users WHERE user_login='".mysqli_real_escape_string($link, $_POST['login'])."'");
    if (mysqli_num_rows($query) > 0) {
        $err[] = "Пользователь с таким логином уже существует в базе данных";
    }

    if (count($err) == 0) {
        $login = $_POST['login'];

        $password = trim($_POST['password']);

        
        $role=$_POST['role'];
        
        mail($login,"Данные для входа",' ' .$login ."\n" . $_POST['password'] . ' '
       ,'From: strnowtest@gmail.com' . "\r\n" .
        'Reply-To: strnowtest@gmail.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion());
        
        if (mysqli_query($link, "INSERT INTO users SET user_login='" .$login. "', user_password='".$password."'".", role='".$role."',activate='1'"))
         {
          echo("<meta http-equiv='refresh' content='0'>");
        }
    } else {
        $_SESSION['err_message']="При регистрации произошли следующие ошибки:";
        foreach ($err as $error) {
            $_SESSION['err_message'].=$error." ";
        }
    }
}
if(isset($_POST['newpasswordusr'])){
  $newpass=$_POST['newPassword'];
  if(!mysqli_query($link,"UPDATE users SET user_password='".$newpass."', activate='1' WHERE user_id='".$_POST['newpasswordusr']."'")){
    $_SESSION['err_message']="Неправильный запрос в базу данных";
  }

} 
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="main.css" />
  <title>Заметки</title>
</head>
<body>
<header>
    <nav class="haeder navbar navbar-expand-lg navbar-dark bg-dark scrolling-navbar">
    <div class="container-fluid">
      <a class="navbar-brand" >Заметки</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarScroll">
        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="../login">Авторизация</a>
          </li> 
          <li class="nav-item">
            <a class="nav-link" href="../register">Регистрация</a>
          </li>  
          <?php
        if($user_role=='root'){
          echo'<li class="nav-item">
          <a class="nav-link" href="../admin">Панель администратора</a>
          </li>  ';
        }
      ?>   
        </ul>
      </div>
      <img class="nav-link" style="cursor: pointer;" onclick="document.location='/logout'" width="60" height="40" src="/images/logout.ico">
          
    </div>
  </nav>
</header>
    
  <?
  //cообщения пользователю
  if (isset($_SESSION['err_message'])) {
        echo '<div class="alert alert-warning" role="alert"> ' . $_SESSION ['err_message'] . ' </div>';
      }
      unset($_SESSION['err_message']);
    ?>
    
     <form method="POST">
    <?php
    //редактирование заметки
    if (isset($_POST['edit_note_id'])) {
                    
              $sql = mysqli_query($link, "SELECT * FROM note_data WHERE note_id='".$_POST['edit_note_id']. "'");                       
              while ($tmp = mysqli_fetch_array( $sql)) {
            ?> <div class="form-control">
              <b style="text-align: center;">Изменение заметки пользователя <?php echo($tmp['user_login']); ?></b>                     
              <div class="justify-content-center row row-cols-1 row-cols-sm-1 row-cols-md-2 g-2 flex-row">                            
              <input type="text"  class="form-control"  name="name-editable" maxlength="255" value="<?php echo($tmp['note_name']); ?>">                         
              <textarea type="text" class="form-control" name="text-editable" maxlength="1024"><?php echo($tmp['note_char'])?></textarea>
              <input value="<?php echo($tmp['note_id']); ?>" name="id-editable" style="display: none;">              
              <button class="btn btn-sm btn-outline-primary" type="submit" style="margin: 1px;">Опубликовать</button>            
              <button class="btn btn-sm btn-outline-secondary" type="button" style="margin: 1px;" data-bs-toggle="modal" data-bs-target="#modalWindowEditNote" data-bs-whatever="<?php echo$tmp['note_id']." ".$tmp['user_login']?>" >Удалить</button>
              </div>   
               </div> 
               <?php
                }  
                }
                ?>
    </form> 
<div style="background: white;" class="accordion" id="accordionExample">
<div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
        Регистрация пользователей
      </button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
      <div class="accordion-body">
      <form method="post" class="justify-content-center align-items-center">
    <input class="form-control" name="login" type="email" placeholder="Логин" required><br>
    <input class="form-control" name="password" type="password" placeholder="Пароль" required><br>
    <select class="form-select " style="margin-bottom:10px" name="role" aria-label="Выбор роли" id="role_select">
        <option value="root">Администратор</option>
        <option selected value="user">Пользователь</option>
    </select>
    <input class="btn btn-xxl btn-outline-success" name="regUser" type="submit" value="Выслать данные">

    </form>
    </div>
    
    </div>
            
  <div class="accordion-item">
    <h2 class="accordion-header" id="heading2">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
        Заметки на одобрение
      </button>
    </h2>
    <div class="modal fade" id="modalWindowEditNote" tabindex="-1" aria-labelledby="modalWindowEditNoteLabel" aria-hidden="true">
        <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalWindowEditNoteLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>        
      </div>
      <?php  
            //заявки на заметки
            if (isset($_POST['delNoteReason'])) { 
             $arr=explode(" ",$_POST['delNoteReason']);
             if(mysqli_query($link,"UPDATE note_data SET del=1 WHERE note_id='".$arr[0]."'"))
              {
                if( mail($arr[1],"Ваша запись была не одобрена администратором",$_POST['noteCancelReason']  
                ,'From: strnowtest@gmail.com' . "\r\n" .
                 'Reply-To: strnowtest@gmail.com' . "\r\n" .
                 'X-Mailer: PHP/' . phpversion())){
                 $_SESSION['err_message']="Запись удалена, уведомление об удалении отправленно";
                 }
              }; 
            }
            ?>
      <form method="POST">
      <div class="modal-body">
       
          <div class="mb-3">
            <label for="recipient-name" class="col-form-label">Причина откланения заметки</label>
            <input type="text" class="form-control" name="noteCancelReason"  id="recipient-name">
          </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
        <button type="submit" id="delNoteReason" value="" name="delNoteReason" class="btn btn-primary">Откланить</button>
      </div>
      </form>
      <script type="text/javascript">
      // окно причины откланения заявок пользователя
    var modalWindowEditNote = document.getElementById('modalWindowEditNote')
    var get_button_edit_note =document.getElementById('delNoteReason')

    modalWindowEditNote.addEventListener('show.bs.modal', function (event) {    
    var button = event.relatedTarget
    var delNoteUsr = button.getAttribute('data-bs-whatever')
    
    var modalTitle = modalWindowEditNote.querySelector('.modal-title')
    var modalBodyInput = modalWindowEditNote.querySelector('.modal-body input')
    modalTitle.textContent = 'Откланение заметки'
    get_button_edit_note.value=delNoteUsr
    ;})
  </script>
    </div>
  </div>
  
  </div>
    <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#accordionExample">
      <div class="accordion-body">
      
           <form method="POST">
         
           <?php
           //разрешение заметки пользователю
            if (isset($_POST['acc_note_id'])) {
              if(mysqli_query($link,"UPDATE note_data SET activate='1' WHERE note_id='". $_POST['acc_note_id'] ."'")){
                $_SESSION['err_message']="Запись опубликовавна";
                echo("<meta http-equiv='refresh' content='0'>");
              }
            }
                //заявки на заметки
                if(isset($_POST['name-editable']) or isset($_POST['name-editable'])){                
                  if(!mysqli_query($link,"UPDATE note_data SET note_name='".$_POST['name-editable']."', note_char='".$_POST['text-editable']."', activate='1' WHERE note_id='". $_POST['id-editable'] ."'")){
                    $_SESSION['err_message']='Проблема с базой данных'; 
                  }else{
                    echo("<meta http-equiv='refresh' content='0'>");
                  }  
                }
                $notedQuery=mysqli_query($link,"SELECT * FROM note_data WHERE activate=0 AND del=0 ");               
                while($tmp = mysqli_fetch_array($notedQuery)){
                ?>
                  <div class="accordion<?php echo $tmp['note_id']?>" id="accordion-note-n<?php echo $tmp['note_id']?>">
                  <div class="accordion-item<?php echo $tmp['note_id']?>">
                    <h2 class="accordion-header" id="heading<?php echo $tmp['note_id']?>">
                      <button name="note-<?php echo $tmp['note_id']?>"  class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $tmp['note_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $tmp['note_id']?>">
                       Заметка пользователя: <?php echo $tmp['user_login']." "?>                   
                      </button>
                    </h2>
                    <div id="collapse<?php echo $tmp['note_id']?>" 
                    class="form-control accordion-collapse collapse " 
                    aria-labelledby="heading<?php echo $tmp['note_name']?>" 
                    data-bs-parent="#accordion-note-n<?php echo $tmp['note_id']?>">
                    <div class="d-flex justify-content-center">
                  <div class="btn-group">
                  <button style="margin-bottom: 5px;margin-top: 5px;" name="acc_note_id" value="<?php echo $tmp['note_id']?>"  type="d-flex submit"  class="btn btn-sm btn-outline-success">Опубликовать</button>                 
                  <button style="margin-bottom: 5px;margin-top: 5px;" name="edit_note_id" value="<?php echo $tmp['note_id']?>"  type="d-flex submit"  class="btn btn-sm btn-outline-primary">Изменить</button>
                  <button style="margin-bottom: 5px;margin-top: 5px;" type="button"  class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalWindowEditNote" data-bs-whatever="<?php echo$tmp['note_id']." ".$tmp['user_login']?>">Удалить</button>                  
                  </div>
                  </div> 
                      <div class="accordion-body">
                      Загаловок:
                      <?php $textwithoutbb=replaceBBCode($tmp['note_name']);                      
                        echo " ".$textwithoutbb;?>
                      <br>
                      Содержание:
                      <?php 
                      $textwithoutbb=replaceBBCode($tmp['note_char']);                      
                        echo $textwithoutbb;
                      ?>
                  
                      </div>                      
                    </div>                    
                  </div>
                  </div>                        
            <?php                
              }          
            ?>
           </form>
    </div>
    </div>
  </div>
<div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUserTab" aria-expanded="true" aria-controls="collapseUserTab">
        Пользователи
      </button>
    </h2>
    <div id="collapseUserTab" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
      <div class="accordion-body">
              
      <input style="margin-bottom: 5px;" onkeyup="myUserSearch()" class="form-control" placeholder="Поиск пользователей" type="text" id="mySearchUser">
      <script>
      //поиск в пользователях
      function myUserSearch() {
  var input, filter, usertab, h, a, i;
  input = document.getElementById("mySearchUser");
  filter = input.value.toUpperCase();
  usertab = document.getElementById("collapseUserTab");
  h = usertab.getElementsByTagName("h2");
  for (i = 0; i < h.length; i++) {
    a = h[i].getElementsByTagName("button")[0];
    if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
      h[i].style.display = "";
    } else {
      h[i].style.display = "none";
    }
  }
}
</script>
      <form method="POST">
      <?php       
          //удаление пользователей       
          if(isset($_POST['del-usr'])){
           if(mysqli_query($link,"DELETE FROM users WHERE user_id='".$_POST['del-usr']."' AND user_password=''"))
            {
              echo("<meta http-equiv='refresh' content='0'>");
              mysqli_query($link,"UPDATE users SET del='1' WHERE user_id='".$_POST['del-usr']."'");             
            }            
            
          }
          //сообщить данные авторизации пользователю
          if(isset($_POST['send-usr'])){
            $query=mysqli_query($link,
            "SELECT user_login,user_password FROM users WHERE user_id='". $_POST['send-usr'] ."'");            
            $sendPass=mysqli_fetch_row($query);

             if( mail($sendPass[0],"Данные для входа в ваши заметки ",' Ваша почта:' .$sendPass[0] ."\n Ваш пароль:" . $sendPass[1]. ' '
              ,'From: strnowtest@gmail.com' . "\r\n" .
               'Reply-To: strnowtest@gmail.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion())){
                $_SESSION ['err_message']="Сообщение отправлено";

               }
          }
          if(isset($_POST['list-note-usr'])){            
            $_SESSION['list_user_note']=$_POST['list-note-usr'];           
          }
          //вывод пользователяей и их заявки 
          $userQuery=mysqli_query($link,"SELECT * FROM users ORDER BY user_password");  
            while($tmp = mysqli_fetch_array($userQuery)){
              $pass=trim($tmp['user_password']); 
              if($pass=='' and $tmp['del']==0)
               {
                ?> 
                <div class="accordion<?php echo $tmp['user_id']?>" id="accordion-user-n<?php echo $tmp['user_id']?>">
                <div class="accordion-item<?php echo $tmp['user_id']?>">
                  <h2 class="accordion-header<?php echo $tmp['user_id']?>" id="heading<?php echo $tmp['user_id']?>">               
                    <button style="color: #0c63e4; background-color: #e7f1ff;" name="user-<?php echo $tmp['user_id']?> "  class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $tmp['user_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $tmp['user_id']?>">
                   <?php echo "Заявка на регистрацию от:".$tmp['user_login'];?>
                    </button>
                  </h2>
                  <div id="collapse<?php echo $tmp['user_id']?>" 
                  class="accordion-collapse collapse " 
                  aria-labelledby="heading<?php echo$tmp['user_login']?> " 
                  data-bs-parent="#accordion-user-n<?php echo $tmp['user_id']?>">
                    <div class="accordion-body">
                    <div class="d-flex justify-content-center align-items-center">
                <div class="btn-group">
                <button style="margin-bottom: 5px;margin-top: 5px;" name="edit-usr" value="<?php echo $tmp['user_id']?>" type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newPassModal" data-bs-whatever="<?php echo$tmp['user_id']." ".$tmp['user_password'];?>">Установить пароль</button>
                <button style="margin-bottom: 5px;margin-top: 5px;" name="del-usr" value="<?php echo $tmp['user_id']?>" type="submit"  class="btn btn-sm btn-outline-secondary">Отклонить</button>
                </div>
                </div>
                    </div>
                      
                  </div>
                </div>
                </div>
               <?php               
                
              }elseif($tmp['del']==0){
               ?>

                <div class="accordion<?php echo $tmp['user_id']?>" id="accordion-user-n<?php echo $tmp['user_id']?>">
                <div class="accordion-item<?php echo $tmp['user_id']?>">
                  <h2 class="accordion-header<?php echo $tmp['user_id']?>" id="heading<?php echo $tmp['user_id']?>">
                    <button name="user-<?php echo $tmp['user_id']?> "  class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $tmp['user_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $tmp['user_id']?>">
                    <?php echo$tmp['user_login']?>
                    </button>
                  </h2>
                  
                  <div id="collapse<?php echo $tmp['user_id']?>" 
                  class="accordion-collapse collapse " 
                  aria-labelledby="heading<?php echo$tmp['user_login']?> " 
                  data-bs-parent="#accordion-user-n<?php echo $tmp['user_id']?>">
                    <div class="accordion-body">
                    <div class="d-flex justify-content-center align-items-center">
                <div class="btn-group">                
                <button style="margin-bottom: 5px;margin-top: 5px;" name="list-note-usr" value="<?php echo $tmp['user_login']?>" type="submit" class="btn btn-sm btn-outline-primary">Просмотр заметок</button>                
                <button style="margin-bottom: 5px;margin-top: 5px;" name="edit-usr" value="<?php echo $tmp['user_id']?>" type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newPassModal" data-bs-whatever="<?php echo$tmp['user_id']." ".$tmp['user_password'];?>">Изменить пароль</button>
                <button style="margin-bottom: 5px;margin-top: 5px;" name="send-usr" value="<?php echo $tmp['user_id']?>" type="submit"  class="btn btn-sm btn-outline-success">Выслать данные авторизации</button>
                <button style="margin-bottom: 5px;margin-top: 5px;" name="del-usr" value="<?php echo $tmp['user_id']?>" type="submit"  class="btn btn-sm btn-outline-secondary">Удалить</button>
                    </div>
                    </div>
                      <?php echo$tmp['user_password']?> 
                    </div>                      
                  </div>
                </div>
                </div>                
            <?php  
              }
            }            
           ?> 
      </form>

      <form method="POST">
<?php
            //вывод списка заметок выбраного пользователя
            if(isset($_SESSION['list_user_note'])){
                $notedUserQuery=mysqli_query($link,"SELECT * FROM note_data WHERE user_login='".$_SESSION['list_user_note']."' AND activate=1 AND del=0 ");  
               ?>
               <b>Заметки пользователя <?php echo $_SESSION['list_user_note']?> </b>
               <input style="margin-bottom: 5px;" onkeyup="userNoteSerach()" class="form-control" placeholder="Поиск в заметках пользователя <?php echo $_SESSION['list_user_note']?>" type="text" id="noteSearchUsers">
      <script>
      //поиск в пользователях
      function userNoteSerach() {
  var input, filter, mEl, pEl, o, i;
  input = document.getElementById("noteSearchUsers");
  filter = input.value.toUpperCase();
  mEl = document.getElementById("usersNoteList");
  pEl = mEl.getElementsByTagName("h2");
  for (i = 0; i < pEl.length; i++) {
    o = pEl[i].getElementsByTagName("button")[0];
    if (o.innerHTML.toUpperCase().indexOf(filter) > -1) {
      pEl[i].style.display = "";
    } else {
      pEl[i].style.display = "none";
    }
  }
}
</script>
      <div id="usersNoteList">
               <?php  
                           //вывод списка заметок выбраного пользователя 
                while($tmp = mysqli_fetch_array($notedUserQuery)){
                ?>
                  <div class="accordion<?php echo $tmp['note_id']?>" id="accordion-note-n<?php echo $tmp['note_id']?>">
                  <div class="accordion-item<?php echo $tmp['note_id']?>">
                    <h2 class="accordion-header" id="heading<?php echo $tmp['note_id']?>">
                      <button name="note-<?php echo $tmp['note_id']?>"  class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $tmp['note_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $tmp['note_id']?>">
                      <?php $textwithoutbb=replaceBBCode($tmp['note_name']);                      
                        echo " ".$textwithoutbb;?>             
                      </button>
                    </h2>
                    <div id="collapse<?php echo $tmp['note_id']?>" 
                    class="form-control accordion-collapse collapse " 
                    aria-labelledby="heading<?php echo $tmp['note_name']?>" 
                    data-bs-parent="#accordion-note-n<?php echo $tmp['note_id']?>">
                    <div class="d-flex justify-content-center">
                  <div class="btn-group">               
                  <button style="margin-bottom: 5px;margin-top: 5px;" name="edit_note_id" value="<?php echo $tmp['note_id']?>"  type="d-flex submit"  class="btn btn-sm btn-outline-primary">Изменить</button>
                  <button style="margin-bottom: 5px;margin-top: 5px;" type="button"  class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalWindowEditNote" data-bs-whatever="<?php echo$tmp['note_id']." ".$tmp['user_login']?>">Удалить</button>                  
                  </div>
                  </div> 
                      <div class="accordion-body">    
                      <?php 
                      $textwithoutbb=replaceBBCode($tmp['note_char']);                      
                        echo $textwithoutbb;
                      ?>                  
                      </div>                      
                    </div>                    
                  </div>
                  </div>                        
            <?php                
              }  
            unset($_SESSION['list_user_note']);
            }        
            ?>
            </div>
</form>


          </div>
               
            </div>      
    
  <div class="modal fade" id="newPassModal" tabindex="-1" aria-labelledby="newPassModalLabel" aria-hidden="true">
        <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newPassModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>        
      </div>
          
      <form method="POST">
      <div class="modal-body">
       
          <div class="mb-3">
            <label for="recipient-name" class="col-form-label">Новый пароль:</label>
            <input type="text" class="form-control" name="newPassword"  id="recipient-name">
          </div>
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
        <button type="submit" id="edit_usr_pass" name="newpasswordusr" value="" class="btn btn-primary">Установить пароль</button>
      </div>
      </form>
    </div>
  </div>
  <script type="text/javascript">
  // окно для изменения пароля
    var newPassModal = document.getElementById('newPassModal')
    var get_button_edit =document.getElementById('edit_usr_pass')
    newPassModal.addEventListener('show.bs.modal', function (event) {    
    var button = event.relatedTarget
    var passUser = button.getAttribute('data-bs-whatever')
    var modalTitle = newPassModal.querySelector('.modal-title')
    var modalBodyInput = newPassModal.querySelector('.modal-body input')
    modalTitle.textContent = 'новый пароль для пользователя'
    modalBodyInput.value = passUser.replace(/[^ ]+ /, '')
    var idUsr=passUser.replace(modalBodyInput.value,'')
    get_button_edit.value=idUsr;})
  </script>
  </div> 
</div>
</div>

</div>
<script src="/js/bootstrap.js"></script>
</body>
<?php
} else{
  header("Location: / ");
}    
?>