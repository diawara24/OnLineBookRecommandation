<?php 
	
	require_once("modele/connexion.php");
	$erreur = array();
	if (isset($_POST['inscription'])) {
		// Verifier si le champs ne sont pas vide
		if(!empty($_POST['username']) AND !empty($_POST['password']) AND !empty($_POST['nom'])){
		    try{

		        $sql = "SELECT count(*) as nbClient FROM client c WHERE c.Email = ?";
		        $query1=$myPDO->prepare($sql);
	    		$query1->execute(array($_POST['username']));
	    		$resulat = $query1->fetch();
	    		$nbClient = (int) $resulat['nbClient'];
	    		if ($nbClient != 1) {
	    			$sql = "INSERT INTO client(nom_client, Email, MDP, nationalite, type_livre) VALUES(?, ?, ?, ?, ?)";
	    			$query=$myPDO->prepare($sql);
	    			$query->execute(array($_POST['nom'],$_POST['username'],$_POST['password'],$_POST['nationalite'],$_POST['type_livre']));
	    			$erreur['success'] ="inscription reussi !!! Connectez-vous...";
	    			

	    		}
	    		if($nbClient >= 1){
	    			$erreur['erreur'] ="Mot de pass ou nom d'utilisateur incorrect !!!";
	    			
	    		}

	    		
		    }
		    catch(Exception $e)
		    {
		        echo $e->getMessage();
		    }
		}
	}
	

 ?>