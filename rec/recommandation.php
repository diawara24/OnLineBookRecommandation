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
  $result ="SELECT client.nom_client, group_concat(livre.titre_livre), group_concat(livre.evaluer) FROM achat INNER JOIN client ON achat.id_client = client.id_client INNER JOIN livre ON livre.ISBN = achat.ISBN GROUP BY client.nom_client";

  $query=$myPDO->prepare($result);
  $query->execute(array());


  $bookarray = array();
  $ratearray = array();
  $outerarray = array();
  $users = array();

  while($row = $query->fetch()){
  
    //$bookarray = explode(',',$row['group_concat(book.bookName)']);
    //$ratearray= explode(',',$row['group_concat(book.bookRate)']);
     $bookarray = explode(',',$row['group_concat(livre.titre_livre)']);
     $ratearray= explode(',',$row['group_concat(livre.evaluer)']);
    $namearray = $row[0];
    $inner = array_combine ($bookarray ,$ratearray);
  
    $outerarray += array($row[0]=>$inner);
   // $users = json_encode($outerarray, JSON_NUMERIC_CHECK);
 }
  //this will return a nested array
  // echo "<pre>";
  // print_r($outerarray);
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

  //Les livres les plus achetes
  $sql = "SELECT DISTINCT * FROM achat a , livre l WHERE a.ISBN=l.ISBN AND a.id_client != ? ORDER BY l.nb_achat DESC LIMIT 12 INTERSECT SELECT DISTINCT * FROM achat a , livre l WHERE a.ISBN=l.ISBN AND a.id_client = ? ";
  $query=$myPDO->prepare($sql);
  $query->execute(array($id_client,$id_client));
?>
<!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
  <div class="col-md-12">
    <h3>Les livres les plus Achetés</h3>
    <?php 
    $i=1;
      while($donnee=$query->fetch()) { 
        if ($i <= 3) {
    ?>
      <div class="col-md-4">
        <div class="thumbnail">
          <a href="consultation.php?ISBN=<?php echo $donnee['ISBN'] ?>">
            <img src="<?php echo $donnee['img_livre']; ?>" alt="Lights" style="width:100%;">
          </a>
        </div>
      </div>
    <?php 
    $i++;
      }
      } 
     ?>
     <div class="col-md-12 text-center">
       <p><a href="#" class="btn btn-primary" role="button">Voir plus</a><p>
     </div>
  </div>
  <div class="col-md-12">
    <h3>Rocommandation</h3>
    <?php 
    $i=1;
      foreach ($recommends as $recommend=>$values) { 
        if ($i <= 3) {
       $sql="SELECT DISTINCT * 
        FROM livre l
        JOIN ecrire e 
        ON e.ISBN = l.ISBN 
        JOIN auteur a 
        ON a.id_auteur = e.id_auteur 
        WHERE l.titre_livre = ?";
        $query1=$myPDO->prepare($sql);
        $query1->execute(array($recommend));
        $livres = $query1->fetch();
       
    ?>
      <div class="col-md-4">
        <div class="thumbnail">
          <a href="consultation.php?ISBN=<?php echo $livres['ISBN'] ?>">
            <img src="<?php echo $livres['img_livre']; ?>" alt="Lights" style="width:100%;">
          </a>
        </div>
      </div>
    <?php 
    $i++;
      }
      } 
     ?>
     <div class="col-md-12 text-center">
       <p><a href="#" class="btn btn-primary" role="button">Voir plus</a><p>
     </div>
  </div>
</body>
</html>