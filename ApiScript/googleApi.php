<?php 
require_once("recommend.php");
require_once("sample_list.php");
//require_once 'inputData.php';
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'biblio_v1');

// Create connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
// Check connection
if($link === false){
  die("ERROR: Could not connect. " . mysqli_connect_error());
}else{
  //echo "connected";
}

// $isbn = isset($_POST['isbn']) ? $_POST['isbn'] : '';

$request = 'https://www.googleapis.com/books/v1/volumes?q=romance&maxResults=40&langRestrict=fr&orderBy=newest&key=AIzaSyB4wF3fh4UmO2-lVTlZO3gjCYlHGMUYIRQ';
$response = file_get_contents($request);
$results = json_decode($response, true);
$books = array();
$info = array();
// echo "<pre>";
// print_r($results);
// echo "</pre>";
if($results['totalItems'] >0)
{

  $i=0;
  foreach($results['items'] as $book)
  {
    $books['title'] = $results['items'][$i]['volumeInfo']['title'];
    if (strlen($books['title']) > 300)
    $books['title'] = substr($books['title'], 0, 90) . '...';
    $books['author'] = $results['items'][$i]['volumeInfo']['authors'][0];
    $books['description'] = $results['items'][$i]['volumeInfo']['description'];
    if (strlen($books['description']) > 300)
        $books['description'] = substr($books['description'], 0, 300) . '...';
    $books['categories'] = $results['items'][$i]['volumeInfo']['categories'][0];
    $books['country'] = $results['items'][$i]['saleInfo']['country'];
    $books['imageLinks'] = $results['items'][$i]['volumeInfo']['imageLinks']['thumbnail'];
    $i++;
    
    $title = $books['title'];
    $author = $books['author'];
    $namesAut = preg_split('/\s+/', $author, -1, PREG_SPLIT_NO_EMPTY);
    $description  = $books['description'];
    $categories =  $books['categories'];
    $country =  $books['country'];
    $imageLinks =  $books['imageLinks'];
    echo "<pre>";
    print_r($books);
    echo "</pre>";
    $title = mysqli_real_escape_string($link,$title);
    $description = mysqli_real_escape_string($link,$description);
    $namesAut[1] = mysqli_real_escape_string($link,$namesAut[1]);
    $namesAut[0] = mysqli_real_escape_string($link,$namesAut[0]);
    $query = "SELECT * FROM livre WHERE titre_livre = '$title'";
    $result = mysqli_query($link,$query); //$link is the connection
    if(mysqli_num_rows($result) > 0 )
    {
      echo "Records Already exist.".$i;
      echo "<br/>";
    }
    else
    {
      echo "New Records";
      $sql1 ="INSERT INTO livre (titre_livre,Paragraphe,type_livre,img_livre,cat_pay) VALUES('$title', '$description','$categories','$imageLinks', '$country')";
      if(mysqli_query($link, $sql1)){
        echo "Records Sql1 added successfully.";
      }
      else
      {
        echo "Cannot process query $sql1. " . mysqli_error($link);
      }
      $book_id = $link->insert_id;
      $sql2 = "INSERT INTO auteur (nom_auteur, prenom_auteur) VALUES('$namesAut[1]','$namesAut[0]' )";
      if(mysqli_query($link, $sql2))
      {
        echo "Records added successfully.";
      } 
      else
      {
        echo "Cannot process query $sql2. " . mysqli_error($link);
      }
      $auth_id = $link->insert_id;
      $sql3 = "INSERT INTO ecrire(id_auteur, isbn) Values('$auth_id', '$book_id')";
      if(mysqli_query($link, $sql3)){
        echo "Records added successfully.";
      }
      else
      {
        echo "Cannot process query $sql3. " . mysqli_error($link);
      }
    }
    
    
  }     
   }else{
   echo 'Livre introuvable';
}
?>
