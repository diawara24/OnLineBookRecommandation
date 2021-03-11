<?php
  if(isset($_SESSION['connecter'])){
    if (isset($_SESSION['nom_client'])) {
      $nom=$_SESSION['nom_client'];
      $id_client = $_SESSION['id_client'];
    }else{
      $nom = "";
    }
  }
  require_once("recommend.php");
  require_once('modele/connexion.php');
  //$result = $link->query("select users.firstname, group_concat(book.bookName), group_concat(book.bookRate) from ownerBook INNER JOIN users ON ownerBook.user_id = users.id INNER JOIN book ON book.bookId = ownerBook.book_id group by users.firstname");
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
  //print_r($re->getRecommendations($outerarray, $nom));
  $recommends = $re->getRecommendations($outerarray, $nom);
 
  //On determine sur quelle page on se trouve
  if (isset($_GET['page']) && !empty($_GET['page'])) {
    $currentPage = (int) strip_tags($_GET['page']);

  }else{
    $currentPage= 1;
  }
  //On determine le nombre de livre par page
  $parpage = 12;

  $nb_livres  = 0;
  foreach ($recommends as $recommend=>$values) {
    $nb_livres++;
  }
  //Calcul du prmier article de la page
  $premier= ($currentPage * $parpage) - $parpage;

?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
  
  <div class="col-md-12">
  <header class="text-center text-white py-5">
      <h1 class="display-4 font-weight-bold mb-4" style="color: #F39539; margin-bottom:15px">Livre En Fonction des Achats</h1>
  </header>
    <?php 
    if($outerarray != NULL){
    $i=1;
      foreach ($recommends as $recommend=>$values) { 
        if ($i <= 6) {
       $sql="SELECT DISTINCT * 
        FROM livre l
        JOIN ecrire e 
        ON e.ISBN = l.ISBN 
        JOIN auteur a 
        ON a.id_auteur = e.id_auteur 
        WHERE l.titre_livre = ? LIMIT 6";
        $query1=$myPDO->prepare($sql);
        $query1->execute(array($recommend));
        $livres = $query1->fetch();
       
    ?>
      <div class="col-md-4" style="height:70vh;">
        <div class="thumbnail">
          <a href="consultation.php?ISBN=<?php echo $livres['ISBN'] ?>">
            <img src="<?php echo $livres['img_livre']; ?>" alt="Lights" style="width:100%;">
          </a>
        </div>
        <h4 style="color: royalblue"><?php echo $livres['titre_livre']; ?></h4>
      </div>
    <?php 
    $i++;
      }
      }
    }else{
     ?>
     <p><a href="#" class="btn btn-info" role="button">Achete Des Livres Pour être Recommendé des Livre En Fonctions des Achats</a><p>
     <?php 
    }
     ?>
     <div class="col-md-12 text-center" style="margin:20px">
       <p><a href="#" class="btn btn-primary" role="button">Voir plus</a><p>
     </div>
  </div>
  
  <div class="col-md-12">
  <header class="text-center text-white py-5">
      <h1 class="display-4 font-weight-bold mb-4" style="color: #F39539;margin-bottom:15px">Livre PLus Populaires</h1>
  </header>
    <?php 
   // Les livres les plus achetes
  $sql = "SELECT group_concat(livre.titre_livre) FROM achat INNER JOIN livre ON livre.ISBN = achat.ISBN Where achat.id_client =?";
  $query=$myPDO->prepare($sql);
  $query->execute(array($id_client));
  $livres = array();
  while($row = $query->fetch()){
    $livreAcheter = explode(',',$row['group_concat(livre.titre_livre)']);
    $livres += $livreAcheter;
  }

  /*trouver les plus populaire a l'exception des livres que le client a déjà achete */
  $sql = "SELECT DISTINCT * 
                    FROM ecrire e
                    JOIN livre l on l.ISBN = e.ISBN
                    JOIN auteur a on a.id_auteur = e.id_auteur
                    WHERE NOT FIND_IN_SET(`titre_livre`,?) ORDER BY note LIMIT 6 OFFSET 120";
  $stmt = $myPDO->prepare($sql);
  $stmt->execute([implode(",",$livres)]);
  $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo "-------------------";
  // $donnee=$query->fetch();
  
  foreach ($books as $book){ 
   ?>
      <div class="col-md-4">
         <div class="thumbnail" style="height:90vh;">
             <a href="consultation.php?ISBN=<?php echo $book['ISBN'] ?>">
              <img src="<?php echo $book['img_livre']; ?>" alt="Lights" style="width:100%;">
              </a>
              <h4 style="color:royalblue"><?php echo $book['titre_livre']; ?></h4>
          </div>
        </div>
       <?php 
      } 
     ?>
     <div class="col-md-12 text-center">
       <p><a href="#" class="btn btn-primary" role="button">Voir plus</a><p>
     </div>
  </div>
</body>
</html>