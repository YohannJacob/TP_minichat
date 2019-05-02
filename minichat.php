<?php
session_start();
// Appel vers la base de donnée
    try
    {
       $bdd = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', 'root',array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }
    // Gérer les erreurs
    catch(Exception $e)
    {
        die('Erreur : '.$e->getMessage());
    }
    // Aller chercher les données dans la table
    $reponse = $bdd->query('SELECT pseudo, message_post, DATE_FORMAT(date_mess, \'%d/%m/%Y à %H:%i\') AS date_mess FROM Minichat ORDER BY id DESC LIMIT 0, 10');
    // var_dump($_POST);
    
    // a ce stade on crée une conditions pour ne pas afficher le code si jamais la data pseudo et message_post n'existe pas. Ces donnnées n'existant que lorsque le formulaire est rempli 
    if (!empty($_POST)) {
        // si le post (message_post ou pseudo) n'est pas vide on insert le contenu du formulaire dans la table
        $req = $bdd->prepare('INSERT INTO Minichat(pseudo, message_post) VALUES(:pseudo, :message_post)');
        $req->execute(array(
            'pseudo'=> $_POST['pseudo'], 
            'message_post' => $_POST['message_post'],
            
            ));
        // et dans ce cas on affiche la raffraichit la page en l'ouvrant à nouveau 
        setcookie('pseudo', $_POST['pseudo'], time() + 365*24*3600, null, null, false, true);
        header('Location: minichat.php');
    }   

?>

<!-- ici on commence le HTML -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >

    <head>
        <title>Minichat</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="style.css" />
    </head>

    <body>

    <h1>Bonjour, bienvenue sur le minichat.</h1>
    <div class="content">
        <form action="minichat.php" method="post">

            <p><label>Pseudo</label> : <input type="text" id="pseudo_post" name="pseudo" value="<?php echo $_COOKIE['pseudo']?>"/></p>
            <p><label>Message</label> : <textarea name="message_post" id="message_post" cols="18" rows="20"></textarea></p>
            
            <p><input type="submit" value="Valider" id="bt_post"/></p>

        </form>
        <!-- cette parti inclu du html donc on ouvre à nouveau les balises PHP -->
        <!-- on va créer une boucle pour afficher les messages. Attention concaténation un peu spécifique à utiliser tout le temps : "?="" equivaut "?php echo" -->

        <ul class="post">
            <?php while ($donnees = $reponse->fetch()){ ?>
                <li>
                <p id="date"> <?= htmlspecialchars($donnees['date_mess']) ?> </p>
                <div id="message">
                    <p id="pseudo"> <?= htmlspecialchars($donnees['pseudo']) ?> </strong> : </p> 
                    <p id="post"> <?= htmlspecialchars($donnees['message_post']) ?> </p>
                </div>
                </li>
            <?php } ?>

        </ul>
    </div>
   </body>
   <!--  -->