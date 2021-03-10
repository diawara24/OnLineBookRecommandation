<?php 
	//On determine sur quelle page on se trouve
	if (isset($_GET['page']) && !empty($_GET['page'])) {
		$currentPage = (int) strip_tags($_GET['page']);

	}else{
		$currentPage= 1;
	}

	//On determine le nombre total de livres
	$sql1="SELECT count(*) as nb_livres
		FROM livre l
		JOIN ecrire e 
		ON e.ISBN = l.ISBN 
		JOIN auteur a 
		ON a.id_auteur = e.id_auteur";


	$query1=$myPDO->prepare($sql1);
    $query1->execute();
    $resulat = $query1->fetch();

    $nb_livres = (int) $resulat['nb_livres'];
    
    //On determine le nombre de livre par page
    $parpage = 12;

    //On determine le nombre total par page
    $pages = ceil($nb_livres/$parpage);


    //Calcul du premier article de la page
    $premier= ($currentPage * $parpage) - $parpage;

    require_once('modele/connexion.php');

    	$recherche = '%%';
	
	
		if (isset($_GET['chercher'])) {
			$recherche = '%'.$_GET['chercher'].'%';
		}
		$sql="SELECT DISTINCT * 
		FROM livre l
		JOIN ecrire e 
		ON e.ISBN = l.ISBN 
		JOIN auteur a 
		ON a.id_auteur = e.id_auteur
		WHERE l.titre_livre LIKE :recherche OR l.type_livre LIKE :recherche OR a.nom_auteur LIKE :recherche OR a.prenom_auteur LIKE :recherche
		LIMIT :premier,:parpage";
	    $query=$myPDO->prepare($sql);

	    $query->bindValue(':premier',$premier,PDO::PARAM_INT);
	    $query->bindValue(':parpage',$parpage,PDO::PARAM_INT);
	    $query->bindParam(':recherche', $recherche);
	    $query->execute();
    	$livres = $query->fetchAll(PDO::FETCH_ASSOC);
	
	

    require_once('modele/close.php');

 ?>
 <!DOCTYPE html>
 <html lang="fr">
 <head>
 	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="CSS/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="CSS/theme.css"/>
    <script type="text/javascript" src="JQ/jquery-2.2.1.min.js"></script>
 </head>
 <body>
 	<div class="col-md-12">
 		<?php  
	 		foreach ($livres as $livre) {
	 	?>
		<div class="col-md-4">
		    <div class="thumbnail" style="height:90vh;">
		      <a href="consultation.php?ISBN=<?php echo $livre['ISBN'] ?>">
			  <img src="<?php echo $livre['img_livre']; ?>" alt="Lights" style="width:100%;">
		      </a>
			  <h4><?php echo $livre['titre_livre']; ?></h4>
		    </div>
		</div>
	  <?php } ?>
 	</div>
	<div class="col-md-12">
		<nav>
		  	<ul class="pager">
		  		<li class="<?= ($currentPage == 1) ?"disabled": "" ?>">
		  			<a href="index.php?page=<?= $currentPage-1 ?>>" class="page-link">Prcédente</a>
		  		</li>
		  		<?php for ($page=1; $page <= $pages ; $page++): ?>
		  		<li class="<?= ($currentPage == $page) ?"active": "" ?>">
		  			<a href="index.php?page=<?= $page ?>" class="page-link"><?= $page ?></a>
		  		</li>
		  		<?php endfor ?>
		  		<li class="<?= ($currentPage == $pages) ?"disabled": "" ?>">
		  			<a href="index.php?page=<?= $currentPage+1 ?>" class="page-link">Suivante</a>
		  		</li>
		  	</ul>
		 </nav>
	</div>
 </body>
 </html>