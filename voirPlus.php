<?php 
	session_start();
	require_once('modele/connexion.php');
	require_once("rec/recommend.php");
	$re = new Recommend();
	
    if (isset($_SESSION['nom_client'])) {
      	$id_client = $_SESSION['id_client'];
      	$livres = array();
		$livres = $re->getClientAchats($id_client);
		
    }else{
    	header("Location: index.php");
    }
	
  ?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="CSS/bootstrap/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="CSS/theme.css"/>
	<link rel="stylesheet" href="CSS/consultation.css"/>
	<link rel="stylesheet" type="text/css" href="style.css">
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
	<h2>Plus de livres pour vous</h2>
	<hr>


	<!-- liste total des livres recommandes -->
	<div class="row">
		<?php if ($_GET['voirPlus'] == "recommandation") {?>
			<?php 
			    $livres = array();
			    $livres = $re->getClientAchats($id_client);
			    /*trouver les plus populaire a l'exception des livres que le client a déjà achete */
			    $books = $re->livrePopulaire($livres);
			    foreach ($books as $book){ ;?>
	    <div class="col-md-4">
	      <div class="thumbnail" style="height:70vh;">
	        <a href="consultation.php?ISBN=<?php echo $book['ISBN'] ?>">
	        <img src="<?php echo $book['img_livre']; ?>" alt="Lights" style="height:100%;">
	       
	        </a>
	      </div>
		</div>
	    	<?php } ?>
	    <?php }  ?>
	</div>

	<!-- Top 20 des lovres les plus populaire -->
	<div class="row">
		<?php if ($_GET['voirPlus'] == "populaire") {?>
			<?php 
			    /*trouver les plus populaire a l'exception des livres que le client a déjà achete */
			    $books = $re->livrePopulaire($livres);
			    foreach ($books as $book){ ;?>
	    <div class="col-md-4">
	      <div class="thumbnail" style="height:70vh;">
	        <a href="consultation.php?ISBN=<?php echo $book['ISBN'] ?>">
	        <img src="<?php echo $book['img_livre']; ?>" alt="Lights" style="height:100%;">
	        </a>
	      </div>
		</div>
	    	<?php } ?>
	    <?php }  ?>
	</div>

	<!-- Top 20 des lovres les plus achetes -->
	<div class="row">
		<?php if ($_GET['voirPlus'] == "plusAchetes") {?>
			<?php 
			    
			    /*trouver les plus echates a l'exception des livres que le client a déjà achete */
			    $books = $re->getLivresPlusAchetes($livres);
			    foreach ($books as $book){ ;?>
	    <div class="col-md-4">
	      <div class="thumbnail" style="height:70vh;">
	        <a href="consultation.php?ISBN=<?php echo $book['ISBN'] ?>">
	        <img src="<?php echo $book['img_livre']; ?>" alt="Lights" style="height:100%;">
	        </a>
	      </div>
		</div>
	    	<?php } ?>
	    <?php }  ?>
	</div>


</div>
<?php include('composant/footer.php'); ?>
</body>
</html>
