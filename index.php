<?php
session_start();

include('modele/connexion.php');


//la verfication de variable session et et verfier resevoir la variable b
if(isset($_SESSION['connecter'])){ 

  if(isset($_GET['d'])){
      session_destroy();
      $_SESSION['connecter']=false;
    }
}

//si n'y a pas la variable de connecter alors connecter est false
if(!isset($_SESSION['connecter'])) 
  $_SESSION['connecter']=false; 
   
//pour lad conexion je dois verfier dans la premier temps est ce que j'ai les variable 
if(!empty($_POST['email']) AND !empty($_POST['pwd'])) 
{
  $sql="SELECT * FROM client WHERE Email=? AND MDP=?";
  $query=$myPDO->prepare($sql);
  $query->execute(array($_POST['email'],$_POST['pwd']));

    $_SESSION['connecter']=true;
    if($client_connecter=$query->fetch()) {
    //stocke tt les variable dans session pour travaille dans tous les page
    $_SESSION['id_client']=$client_connecter['id_client'];
    $_SESSION['nom_client']=$client_connecter['nom_client'];
    $_SESSION['Email']=$client_connecter['Email'];
    $_SESSION['adresse']=$client_connecter['adresse'];
    $_SESSION['MDP']=$client_connecter['MDP'];
    $_SESSION['Date_inscription']=$client_connecter['Date_inscription'];
    }

    header("location: myprofil.php");
  
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="CSS/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="CSS/theme.css"/>
    <script type="text/javascript" src="JQ/jquery-2.2.1.min.js"></script>
  <title>biblioweb</title>
</head>

<body>
<div class="jumbotron">
      <div class="col-lg-8">
          <span class="biblio-logo">Biblioweb<span class="dot">.CYU</span></span>
      </div>
      <div class="col-lg-4">
          <div id="logoright">Bibliotheque du web</div>
      </div>
</div>

<div class="col-lg-6">
  <ul class="nav nav-pills nav1">
            <li class="active"><a href="index.php">Acceuil</a></li>
   <?php 
 
  if(!$_SESSION['connecter']){//si connecter il n,affiche pas else il affiche
  ?>
           <li><a href="#">Inscription</a></li>
    <?php
  }
    ?> 
           <!-- <li><a href="reglement.php" >Reglement</a></li> -->
          
           <li><a href="#" >La bibliotheque</a></li>
  </ul>
</div>

<div class="col-lg-6">
 <div id="iscri">
  <?php 
  if(!$_SESSION['connecter']){
  ?>
        <form method="post" action="" > 
            <input type="text" name="email" placeholder="Email ou Username" required />
            <input type="password" name="pwd" placeholder="Password" required />
            <input type="submit" value="Login"/>
        </form>  
      
    <?php
  }else{
    ?> 

 <div class="col-lg-7">
 </div>
 <div class="col-lg-5">
 <ul class="nav nav-pills">
      <li class="active"><a href="profil.php" >Profile</a></li>
      <li><a href="index.php?d=true" >Deconnecter</a></li>
 
  </ul>

</div>

    <?php
    }
    ?>

  </div> 
</div>

<div class="row">

<?php
include('composant/menu.php');
?>

  <div class="col-lg-9">
  <h2> Acceuil</h2>
    <div class="panel panel-default panel2">
      <div class="panel-heading">Page d'Acceuil</div>
      <div class="panel-body">
        <div class="row">
            <?php include('livres.php'); ?>

        </div>
      </div>
    </div>
  </div>
</div>


<?php
include('composant/footer.php');

?>


</body>
</html>










