<?php 
    if (!isset($_SESSION['connecter'])) {
      header("Location: index.php");
    }
    include('modele/connexion.php');
    $sql="SELECT DISTINCT type_livre FROM livre WHERE type_livre != '' LIMIT 15";


    $query=$myPDO->prepare($sql);
    $query->execute(array());
    $_SESSION['type_livre']= $query->fetchAll();
    //print_r($_SESSION['type_livre']);
    $sql1="SELECT DISTINCT nom_auteur, prenom_auteur FROM auteur LIMIT 15";

    $query1=$myPDO->prepare($sql1);
    $query1->execute(array());
  ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="CSS/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="CSS/theme.css"/>
    <script type="text/javascript" src="JQ/jquery-2.2.1.min.js"></script>
</head>
<body>
<div class="col-lg-3">
    <form method="get" action="index.php" > 
    <div class="input-group">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit"  placeholder="chercher">Go!</button>
      </span>
      
      <input name="chercher" type="text" class="form-control" />
      
    </div>
 </from>

    <div class="col-lg-12">
          <div id="menu1" style="height: auto;">
        <h3>Themes</h3>
            <ul>
              <?php 
              foreach ($_SESSION['type_livre'] as $key => $value) { 
             ?>
                <li>
                  <?php if (!isset($_SESSION['nom_client'])) { ?>
                  <a href="index.php?chercher=<?= $value[0] ?>"><?php echo $value[0]; ?></a>
                  <?php } elseif (isset($_SESSION['nom_client'])) {?>
                    <a href="myprofil.php?chercher=<?= $value[0] ?>"><?php echo $value[0]; ?></a>
                  <?php } ?>
                </li>
              <?php 
              }
            ?>
            </ul>
          </div>
          <div id="menu2" style="height: auto;">
            <h3>Auteurs</h3>
                <ul>
                  <?php while ($donnees1=$query1->fetch()) {
                   ?>
                    <li>
                      <?php if (!isset($_SESSION['nom_client'])) { ?>
                      <a href="index.php?chercher=<?= $donnees1['nom_auteur'] ?>"><?php echo $donnees1['prenom_auteur'].' '.$donnees1['nom_auteur']; ?></a>
                      <?php } elseif (isset($_SESSION['nom_client'])) {?>
                        <a href="myprofil.php?chercher=<?= $donnees1['nom_auteur'] ?>"><?php echo $donnees1['prenom_auteur'].' '.$donnees1['nom_auteur']; ?></a>
                        <?php } ?>
                      </li>
                  <?php 
                  }
                ?>
            </ul>
          </div>
  </div>
  </div>
</body>
</html>