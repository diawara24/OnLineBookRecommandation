<?php
   session_start();
   include("traitement.php");
   echo $erreur;
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="CSS/bootstrap/css/bootstrap.min.css"/>
<link rel="stylesheet" href="CSS/theme.css"/>
<script type="text/javascript"></script>
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
  <ul class="nav nav-pills">
      <li><a href="index.php">Acceuil</a></li>
       
      <li class="active"><a href="inscription.php">Inscription</a></li>
  </ul>
</div>
<div class="col-lg-6">
    <div id="iscri">
      <form method="post" action="loginCheck.php" > 
          <input type="text" name="email" placeholder="Email ou Username" required />
          <input type="password" name="pwd" placeholder="Password" required />
          <input type="submit" value="Login"/>
      </form>  
    </div> 
</div>
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-success panel1" >
      <div class="panel-heading">Inscription</div>
      <div class="panel-body">
        <fieldset>
    	     <legend><b>Inscripton Individuelle</b></legend>
            <form action="#" method="post" enctype="multipart/form-data">
        		<table class="login_table">
              <tr>
                <td>Erreur</td>
                <?php if(isset($erreur)) {?>
                <td><?php echo $erreur; ?></td>
                <?php  }  ?>
              </tr>
        		<tr>
        		<td>Email<span>*</span></td>
        		<td><input type="text" name="username" id="username" placeholder="email or username" required></td>
        		</tr>
        		<tr>
        		<td>Pasword<span>*</span></td>
        		<td><input type="password" name="password" id="password" placeholder="password" required></td>
        		</tr>
        		<tr>
        		<td>Nom<span>*</span></td>
        		<td><input type="text" name="nom" id="nom" placeholder="Nom" required></td>
        		</tr>
            <tr>
            <td>Nationalite<span>*</span></td>
            <td>
              <select class="form-control" id="country" name="nationalite">
                  <option value="FR">France</option>
                  <option value="IT">Italy</option>
                  <option value="SN">Senegal</option>
                  <option value="ES">Spain</option>
                  <option value="JPN">Japon</option>
              </select>
            </td>
            </tr>
            <tr>
            <td>Type de de livre<span>*</span></td>
            <td><select class="form-control" id="type_livre" name="type_livre">
              <?php if (isset($_SESSION['type_livre'])) { ?>
                <?php foreach($_SESSION['type_livre'] as $key => $value) { ?>
                <option value="<?php echo $value[0]; ?>"><?php echo $value[0]; ?></option>
                <?php } ?>
              <?php } ?>
            </select></td>
            </tr>
    		    <tr>
    		      <td></td>
    		      <td><input type="submit" name="inscription" value="inscription"/></td>
    		    </tr>
    		  </table>
    	   </form>

        </fieldset>  
      </div>
    </div>
  </div>
</div>
<?php
include('composant/footer.php')
?>
</body>
</html>




