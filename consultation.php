<?php 
	session_start();
	require_once('modele/connexion.php');

	if(!isset($_SESSION['connecter'])){
	  $_SESSION['connecter']=false;
	}

	$ISBN = $_GET['ISBN'];
	$sql="SELECT * FROM livre WHERE ISBN=?";
	$query=$myPDO->prepare($sql);
  	$query->execute(array($ISBN));
  	$livre=$query->fetch();

  	$sql="SELECT * FROM ecrire e JOIN auteur a
		ON e.id_auteur=a.id_auteur
		WHERE e.ISBN=?";
	$query = $myPDO->prepare($sql);
	$query->execute(array($ISBN));
	$auteur = $query->fetch();
	$DateAchat = date("Y-m-d");

	if(isset($_POST['emprunter'])){
		$nb_achat = $livre['nb_achat']+1;
		$DateAchat = date("Y-m-d");
		$id_client = $_SESSION['id_client'];
		$ISBN = $livre['ISBN'];
		$sql1="UPDATE livre SET nb_achat=? WHERE ISBN=?";
		$query1 = $myPDO->prepare($sql1);
		$query1->execute(array($nb_achat,$ISBN));
		$sql2="UPDATE client SET Nb_achat= ? WHERE id_client=? ";
		$query2 = $myPDO->prepare($sql2);
		$query2->execute(array($nb_achat,$id_client));
		$sql3 = "INSERT INTO achat(id_client,ISBN,date_achat) VALUES(?,?,?)";
		$query3 = $myPDO->prepare($sql3);
		$query3->execute(array($id_client,$ISBN,$DateAchat));
 ?>
 	<script type="text/javascript"> 
 		window.alert('Achat reuissi !!!');
 	</script>
 <?php } ?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="CSS/bootstrap/css/bootstrap.min.css"/>
<link rel="stylesheet" href="CSS/theme.css"/>
<link rel="stylesheet" href="CSS/consultation.css"/>
<script type="text/javascript"></script>
</head>
<body>
<div class="jumbotron">
      <div class="col-lg-8">
          <span class="biblio-logo">Biblioweb<span class="dot">.JBF</span></span>
      </div>
      <div class="col-lg-4">
          <div id="logoright">Bibliotheque du web</div>
      </div>
</div>

<div class="border_page">
	<h2>Livre</h2>
	<hr>
<div class="row">
  <div class="col-sm-6 col-md-4 img-livre">
    <div class="thumbnail">
       <?php echo '<img src='.$livre['img_livre'].' alt="" />'; ?>
    </div>
  </div>

    <div class="col-sm-6 col-md-4 paragraphe">
    <div class="thumbnail paragraphe-livre">
      <div class="caption">
        <h3 class="nom-livre"><?php echo $livre['titre_livre']; ?></h3>
        <p><?php echo $livre['Paragraphe']; ?></p>

        <?php if($_SESSION['connecter'] == true){  ?>
            <p>
            	<a href="myprofil.php" class="btn btn-primary" role="button">Retour</a> 

	            <form method="post" action="">
	                    
	            <input class="btn btn-default" name="emprunter" type="submit" value="Acheter" />       
	                    
	            </form>
            </p>
            <?php } else if($_SESSION['connecter'] == false) { ?>
            	<a href="index.php?chercher=<?php echo $livre['type_livre'] ?>" class="btn btn-primary" role="button">Retour</a> 
            <?php } ?>
        
      </div>
    </div>
    <div class="panel panel-default">
	    <table class="table">
	    	<tr><th>TYPE DE LIVRE</th><th>EDITEUR</th><th>NOMBRE D'ACHAT</th></tr>
	 		<tr><td><?php echo $livre['type_livre']; ?></td><td><?php echo $livre['editeur'] ?></td><td><?php echo $livre['nb_achat']; ?></td></tr>
	    </table>
    </div>
  </div>
</div>
<div>
<div class="row">

 <div class="col-sm-6 col-md-4 img-auteur">
   <h3 class="nom-livre">Auteur</h3>
    <div class="thumbnail">
      <?php echo '<img src='.$auteur['img_auteur'].' alt="" />'; ?>
       <div class="caption">
        <h3><?php echo $auteur['nom_auteur']." ".$auteur['prenom_auteur']; ?></h3>
        </div>
    </div>
  </div>
 
</div>
</div>
</div>
<?php include('composant/footer.php'); ?>
</body>
</html>
