<?php
  include('modele/connexion.php');
  if(isset($_SESSION['connecter'])){
    if (isset($_SESSION['nom_client'])) {
      $nom=$_SESSION['nom_client'];
      $id_client = $_SESSION['id_client'];
    }else{
      $nom = "";
    }
  }
  require_once("recommend.php");
  //require_once('modele/connexion.php');
  $result ="SELECT client.nom_client, group_concat(livre.titre_livre SEPARATOR ';'), group_concat(achat.evaluer SEPARATOR ';') FROM achat INNER JOIN client ON achat.id_client = client.id_client INNER JOIN livre ON livre.ISBN = achat.ISBN GROUP BY client.nom_client";

  $query=$myPDO->prepare($result);
  $query->execute(array());
  $bookarray = array();
  $ratearray = array();
  $outerarray = array();
  $users = array();
  while($row = $query->fetch()){
    // echo "<pre>";
    // print_r($row);
    //  echo "</pre>";
    $bookarray = explode(';',$row[1]);
    $ratearray= explode(';',$row[2]);
    $inner = array_combine ($bookarray ,$ratearray);
    // echo "<pre>";
    //   print_r($bookarray);
    //   //print_r($ratearray);
    // echo "</pre>";
    $outerarray += array($row[0]=>$inner);
   // $users = json_encode($outerarray, JSON_NUMERIC_CHECK);
 }
    // echo "<pre>";
    //   print_r($outerarray);
    // echo "</pre>";
  $re = new Recommend();

  //Listes des livres renvoyer par la recommandation
  $recommends = $re->getRecommendations($outerarray, $nom);
 
  //Listes des livres achetes par le client
  $livres = array();
  $livres = $re->getClientAchats($id_client);
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
  <!-- Section de notre Algorithme de recommandation -->
  <div class="col-md-12">
    <header class="text-center text-white py-5">
        <h1 class="display-4 font-weight-bold mb-4" style="color: #F39539; margin-bottom:15px">Nous vous recommandons</h1>
    </header>
    <?php 
      if($outerarray != NULL){
      $i=1;
      $plus_recommand = "recommandation";
      foreach ($recommends as $recommend) { ?>
    <?php if ($i <= 3) { ?>
      <div class="col-md-4">
          <div class="thumbnail" style="height:70vh;">
            <a href="consultation.php?ISBN=<?php echo $recommend['ISBN'] ?>">
            <img src="<?php echo $recommend['img_livre']; ?>" alt="Lights" style="height:100%;">
            </a>
          </div>
        </div>
    <?php $i++;} }
    }else{
     ?>
     <p><a href="#" class="btn btn-info" role="button">Achete Des Livres Pour être Recommendé des Livre En Fonctions des Achats</a><p>
     <?php 
    }
     ?>
     <?php if ($i>3) { ?>
     <div class="col-md-12 text-center" style="margin:20px">
       <p><a href="voirPlus.php?voirPlus=<?=$plus_recommand?>" class="btn btn-primary" role="button">Voir plus</a><p>
     </div>
   <?php } ?>
  </div>
  
  <div class="col-md-12">
    <header class="text-center text-white py-5">
        <h1 class="display-4 font-weight-bold mb-4" style="color: #F39539;margin-bottom:15px">Les livre les plus populaires</h1>
    </header>
    <?php 

      $plus_populaire = "populaire";
      
      $_books = array();
      /*trouver les plus populaire a l'exception des livres que le client a déjà achete */
      $_books  = $re->livrePopulaire($livres);
      $i=1;
      foreach ($_books as $book){ ;?>
        <?php if ($i <= 3) { ?>
          <div class="col-md-4">
            <div class="thumbnail" style="height:70vh;">
              <a href="consultation.php?ISBN=<?php echo $book['ISBN'] ?>">
              <img src="<?php echo $book['img_livre']; ?>" alt="Lights" style="height:100%;">
              </a>
            </div>
          </div>
        <?php  $i++;} ?>
      <?php
        } 
      ?>
       <?php if ($i>3) { ?>
       <div class="col-md-12 text-center">
         <p><a href="voirPlus.php?voirPlus=<?=$plus_populaire?>" class="btn btn-primary" role="button">Voir plus</a><p>
       </div>
       <?php } ?>
    </div>
    <div class="col-md-12">
      <header class="text-center text-white py-5">
          <h1 class="display-4 font-weight-bold mb-4" style="color: #F39539;margin-bottom:15px">Les livre les plus achetes</h1>
      </header>
      <?php 
        $plus_achetes = "plusAchetes";
        /*trouver les plus populaire a l'exception des livres que le client a déjà achete */
        $achats = $re->getLivresPlusAchetes($livres);
        $i=1;
        // print_r($livres);
        // echo "<br>------------- <br>";
        // print_r($achats);
        foreach ($achats as $achat){ ;?>
          <?php if ($i <= 3) { ?>
            <div class="col-md-4">
              <div class="thumbnail" style="height:70vh;">
                <a href="consultation.php?ISBN=<?php echo $achat['ISBN'] ?>">
                <img src="<?php echo $achat['img_livre']; ?>" alt="Lights" style="height:100%;">
                </a>
              </div>
            </div>
          <?php  $i++;} ?>
        <?php
          } 
        ?>
         <?php if ($i>3) { ?>
         <div class="col-md-12 text-center">
           <p><a href="voirPlus.php?voirPlus=<?=$plus_achetes?>" class="btn btn-primary" role="button">Voir plus</a><p>
         </div>
         <?php } ?>
      </div>
</body>
</html>