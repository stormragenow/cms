<?php
  include_once"function.php";
  $acces=validate($_COOKIE['hash']);  
  if(validate($_COOKIE['hash'])=='false'){      
    header("Location: /login ");  
  }
  $link=linkdb();
?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="main.css" />
  <link class="main-ico" href="/images/ico.png" rel="shortcut icon">
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
        if($acces=='root'){
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
<main>       
    <div class="d-flex justify-content-center">
        <div style="width: 90%;">
        <form method="post">
             <div style="margin-bottom: 5px;" class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingOne">
                    <button style="background-color: white;" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    Добавление заметки
                    </button>
                </h2>
                  <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                <div style="background-color: white;" class="accordion-body">
                <input name="new-note-name" type="text" class="form-control" maxlength="255" placeholder="Заголовок заметки" style="width:40%; margin-bottom: 5px;"/>
            <textarea style="margin-bottom: 5px;" class="form-control" rows="3" type="text" name="new-note-text" id ="new_note_text" maxlength="1024" placeholder="Введите текст заметки"></textarea>
            <button class="btn btn-primary" style="margin-bottom: 5px;" type="submit" name="send"> Добавить </button> 
           
            <div class="accordion accordion-flush form-control" id="accordionFlushBBcode">
              <div class="accordion-item">
              <h2 class="accordion-header" id="flush-headingBBcode">
                
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseBBcode" aria-expanded="false" aria-controls="flush-collapseBBcode">
                  Можно использовать BB коды  
                </button>
              </h2>
            <div id="flush-collapseBBcode" class="accordion-collapse collapse" aria-labelledby="flush-headingBBcode" data-bs-parent="#accordionFlushBBcode">
            <div class="accordion-body">
           [b]<b>текст</b>[/b]",
           <br> [i]<i>текст</i>[/i]",
           <br> [u]<u>текст</u> [/u]",
           <br> [img]<img height='72' width='72' src='/images/ico.png'/>[/img]",
            </div>
            </div>
           </div>            
            </div> 
              </div>
            </div>
            </div>
            
            <?php
            //оброботка пост запросов
              if(isset($_POST['send']))
              {                   
              $name_note=$_POST['new-note-name'];
              $note_data = $_POST['new-note-text']; # Получаем данные за POST запроса
              if (($note_data == '') and ($name_note == '')) {
              $_SESSION['message']='одно из полей должно быть заполнено';
              }
              else{                 
                  $request_on_base="SELECT user_login,activate FROM users WHERE user_hash ='".$_COOKIE['hash'] ."'";   
                  $request_result=mysqli_fetch_assoc($link->query($request_on_base));
                  $login=$request_result['user_login'];
                  $user_activ=$request_result['activate'];                 

                  if (!is_null($login)) {

                      if ($user_activ==1) {
                       mysqli_query($link, "INSERT INTO note_data SET user_login='".$login."', note_name='".$name_note."',note_char='".$note_data."'");
                      }
                      else{
                        echo("<meta http-equiv='refresh' content='0'>");
                      }
                  }
                  else {
                    echo("<meta http-equiv='refresh' content='0'>");
                  }
              }
            }  
            ?>
          </form>
               <div class="card shadow-sm ">              
                  <div class=" card-body " >                                     
            <?php               
            if (isset($_POST['del_id'])) { 
              $temp_id=$_POST['del_id'];
              if(!mysqli_query($link,"UPDATE note_data SET del='1' WHERE note_id='". $temp_id ."'"))
              {
               echo"проблема с базой данных";
              };
            }      
            ?>
           <form method="POST">
           <?php
             if (isset($_POST['edit_id'])) {
                            
              $sql = mysqli_query($link, "SELECT * FROM note_data WHERE note_id='".$_POST['edit_id']. "'"); 
                           
                  while ($tmp = mysqli_fetch_array( $sql)) {
            ?>
              <div class="justify-content-center row row-cols-1 row-cols-sm-1 row-cols-md-2 g-2 flex-row">                            
              <input type="text"  class="form-control"  name="name-editable" maxlength="255" value="<?php echo($tmp['note_name']); ?>">                           
              <textarea type="text" class="form-control" name="text-editable" maxlength="1024"><?php echo($tmp['note_char']); ?></textarea>
              <input value="<?php echo($tmp['note_id']); ?>" name="id-editable" style="display: none;">              
              <button class="btn btn-sm btn-outline-primary"  style="margin-bottom: 50px;" type="submit" >Изменить</button>         
              </div>
               <?php
                
                } 
                
                }
                if(isset($_POST['name-editable'])){                
                  if(!mysqli_query($link,"UPDATE note_data SET note_name='".$_POST['name-editable']."', note_char='".$_POST['text-editable']."', activate=0 WHERE note_id='". $_POST['id-editable'] ."'")){
                    echo 'Проблема с базой данных'; 
                  }else{
                    echo("<meta http-equiv='refresh' content='0'>");
                  }  
                }
                ?>
                
                <input style="margin-bottom: 5px;" onkeyup="serchNote()" class="form-control" placeholder="Поиск заметок" type="text" id="mySearchNote">
                <script>
              function serchNote() {
              var input, filter, noteList, elSerch, k, i;
              input = document.getElementById("mySearchNote");
              filter = input.value.toUpperCase();
              noteList = document.getElementById("noteList"); 
              elSerch = noteList.getElementsByTagName("h2");
              for (i = 0; i < elSerch.length; i++) {
              k = elSerch[i].getElementsByTagName("button")[0];
              if (k.innerHTML.toUpperCase().indexOf(filter) > -1) {
                elSerch[i].style.display = "";
              } else {
                elSerch[i].style.display = "none";}}}
            </script>
              <div id="noteList">
                <?php
                //вывод заметок
                $query = mysqli_query($link,"SELECT user_login FROM users WHERE user_hash ='".$_COOKIE['hash'] ."'");
                $auth_user = mysqli_fetch_array($query)['user_login'];    
                $notedQuery=mysqli_query($link,"SELECT * FROM note_data WHERE user_login='".$auth_user ."' ORDER BY note_id DESC");
                while($tmp = mysqli_fetch_array($notedQuery)){
                if($tmp['activate']==1 and $tmp['del']==0){ ?>
                  <div class="accordion<?php echo $tmp['note_id']?>" id="accordion-note-n<?php echo $tmp['note_id']?>">
                  <div class="accordion-item<?php echo $tmp['note_id']?>">
                    <h2 class="accordion-header" id="heading<?php echo $tmp['note_id']?>">
                      <button name="note-<?php echo $tmp['note_id']?>"  class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $tmp['note_id']?>" aria-expanded="true" aria-controls="collapse<?php echo $tmp['note_id']?>">
                      <?php $textwithoutbb=replaceBBCode($tmp['note_name']);                      
                        echo $textwithoutbb;?> 
                      </button>
                    </h2>
                    <div id="collapse<?php echo $tmp['note_id']?>" class="form-control   accordion-collapse collapse " aria-labelledby="heading<?php echo $tmp['note_name']?>" data-bs-parent="#accordion-note-n<?php echo $tmp['note_id']?>">
                      <div class="accordion-body">
                      <?php 
                      $textwithoutbb=replaceBBCode($tmp['note_char']);                      
                        echo $textwithoutbb;
                      ?>
                  <div class="d-flex justify-content-center">
                  <div class="btn-group">                 
                  <button style="margin-bottom: 5px;margin-top: 5px;" name="edit_id" value="<?php echo $tmp['note_id']?>" href="/?edit_id=<?php echo $tmp['note_id']?>" type="d-flex button submit"  class="btn btn-sm btn-outline-primary">Изменить</button>
                  <button style="margin-bottom: 5px;margin-top: 5px;" name="del_id" value="<?php echo $tmp['note_id']?>"  href="/?del_id=<?php echo $tmp['note_id']?>" type="button "  class="btn btn-sm btn-outline-secondary">Удалить</button>                  
                  </div>
                  </div> 
                      </div>                      
                    </div>                    
                  </div>
                  </div>                        
            <?php
                }
              }          
            ?>
            </form>
            </div>
              </div>
                 
            </div>
        </div> 
    </div>
</main>
   
<script src="/js/bootstrap.js"></script>
</body>
