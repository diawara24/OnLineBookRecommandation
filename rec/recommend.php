<?php
    
    
    class Recommend{
        
        
        public function similarityDistance($preferences, $person1, $person2)
        {
            $similar = array(); // on declare un tableau 
            $sum = 0; // on initialise une somme
            
            // pour chaque client dans la liste de clients
            foreach($preferences[$person1] as $key=>$value)
            {
               // on verifie si la personne a les mêmes livre que chaque autre client
                if(array_key_exists($key, $preferences[$person2]))
                    $similar[$key] = 1; // si oui a chaque existence de met la clé 
            }
            
            if(count($similar) == 0)
                return 0;
            
            foreach($preferences[$person1] as $key=>$value)
            {
                if(array_key_exists($key, $preferences[$person2]))
                    $sum = $sum + pow($value - $preferences[$person2][$key], 2);
            }
            
            return  1/(1 + sqrt($sum));     
        }
        
        
        
        // cette fonction fait la fonctionnalité de la récommendation et prend deux paramètres
        // la liste des personnes et la liste de leur achat avec la personne qu'on doit récommender
        public function getRecommendations($preferences, $person)
        {
            $myPDO = new PDO('mysql:host=localhost;dbname=biblio', 'root', '');
            $total = array(); // on declare un tableau pour mettre
            $simSums = array(); // on declare un tableau pour mettre
            $ranks = array(); // on declare un tableau pour mettre
            $sim = 0; //on initialise la similarité a 0
            
            $resultat = array();
            // on fait une loop sur la liste des personnes et on le met sous forme key et value
            foreach($preferences as $otherPerson=>$values)
            {
                
                if($otherPerson != $person) // on substrait le nom de la personne à suggerer dans la liste et on met dans otherPersonne 
                {
                    // on fait appel a la fonction similarité et on passe en paramètre la liste de toute de les personnes, la personne à suggerer et les autres personnes
                    $sim = $this->similarityDistance($preferences, $person, $otherPerson); 
                }
                
                if($sim > 0)
                {
                    foreach($preferences[$otherPerson] as $key=>$value)
                    {
                        if(!array_key_exists($key, $preferences[$person]))
                        {
                            if(!array_key_exists($key, $total)) {
                                $total[$key] = 0;
                            }
                            $total[$key] += $preferences[$otherPerson][$key] * $sim;
                            
                            if(!array_key_exists($key, $simSums)) {
                                $simSums[$key] = 0;
                            }
                            $simSums[$key] += $sim;
                        }
                    }
                    
                }
            }
            // trier la recommandation
            foreach($total as $key=>$value)
            {
                $ranks[$key] = $value / $simSums[$key];
            }
            
            array_multisort($ranks, SORT_DESC);    
            foreach ($ranks as $recommend=>$values) { 
                $sql="SELECT DISTINCT * 
                FROM livre l
                JOIN ecrire e 
                ON e.ISBN = l.ISBN 
                JOIN auteur a 
                ON a.id_auteur = e.id_auteur 
                WHERE l.titre_livre = ? LIMIT 6";
                $query1=$myPDO->prepare($sql);
                $query1->execute(array($recommend));
                $data = $query1->fetch();
                $resultat[] = $data;
            }

            return $resultat;
        }

        public function getClientAchats($idClient){
            $myPDO = new PDO('mysql:host=localhost;dbname=biblio', 'root', '');
            $livres = array();
            $sql="SELECT group_concat(livre.titre_livre) FROM achat INNER JOIN livre ON livre.ISBN = achat.ISBN Where achat.id_client =?";

          $query=$myPDO->prepare($sql);
          $query->execute(array($idClient));
          while($data = $query->fetch()){
                $livreAcheter = explode(',',$data['group_concat(livre.titre_livre)']);
                $livres[]=$livreAcheter;
            }

            return $livres;
        }

        public function livrePopulaire($achats){
            $myPDO = new PDO('mysql:host=localhost;dbname=biblio', 'root', '');
            $achats = array();
            $sql = "SELECT DISTINCT * 
                    FROM ecrire e
                    JOIN livre l on l.ISBN = e.ISBN
                    JOIN auteur a on a.id_auteur = e.id_auteur
                    WHERE NOT FIND_IN_SET(`titre_livre`,?) ORDER BY l.note DESC LIMIT 21";
          $stmt = $myPDO->prepare($sql);
          $stmt->execute([implode(",",$achats)]);
          $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $books;
        }

        public function getLivresPlusAchetes($achats){
            $myPDO = new PDO('mysql:host=localhost;dbname=biblio', 'root', '');
            $achats = array();
            $sql = "SELECT DISTINCT * 
                    FROM ecrire e
                    JOIN livre l on l.ISBN = e.ISBN
                    JOIN auteur a on a.id_auteur = e.id_auteur
                    WHERE NOT FIND_IN_SET(`titre_livre`,?) ORDER BY l.nb_achat DESC LIMIT 21";
          $stmt = $myPDO->prepare($sql);
          $stmt->execute([implode(",",$achats)]);
          $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $books;
        }
       
    }

?>