<?php
session_start();
require_once 'classes/Membership.php';
$membership = new Membership();

// If the user clicks the "Log Out" link on the index page.
if(isset($_GET['status']) && $_GET['status'] == 'loggedout') {
	$membership->log_User_Out();
}

if($_POST && !empty($_POST['username'])  && !empty($_POST['email'])&& !empty($_POST['pwd']) && $_POST['submitsignup'] ) {
    $response = $membership->create_user($_POST['username'],$_POST['email'], $_POST['pwd']);
}
// Did the user enter a password/username and click submit?
if($_POST && !empty($_POST['username']) && empty($_POST['email']) &&  !empty($_POST['pwd']) && $_POST['submitlogin'] ) {
	$response = $membership->validate_User($_POST['username'], $_POST['pwd']);
}
														

?>


<!DOCTYPE html >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login page</title>
<link rel="stylesheet" type="text/css" href="css/login.css" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/login.js"></script>
<script type="text/javascript" src="js/js.js"></script>
</head>

<body>
<div class="form-wrap">
        <div class="tabs">
            <h3 class="login-tab"><a href="#login-tab-content">Connexion</a></h3>
            <h3 class="signup-tab"><a class="active" href="#signup-tab-content">S'inscrire</a></h3>
            
        </div><!--.tabs-->

        <div class="tabs-content">
            

            <div id="login-tab-content">
                <form class="login-form" action="" method="post">
                    <input type="text" class="input" id="user_login" autocomplete="off" placeholder="Email ou nom d'utilisateur" name="username">
                    <input type="password" class="input" id="user_pass" autocomplete="off" placeholder="Mot de passe" name="pwd">
                    <input type="checkbox" class="checkbox" id="remember_me">
                    <label for="remember_me">Se souvenir de moi</label>

                    <input type="submit" class="button" id="submit" value="Connexion" name="submitlogin">
                </form><!--.login-form-->
                <?php if(isset($response)) echo "<h4 class='alert'>" . $response . "</h4>"; ?>
                <div class="help-text">
                    <p><a id="bmdp" style="cursor:default;" >Mot de pass oublié ?</a></p>
                    <p id="tmdp" style="display:none;">contactez l'administrateur à l'adresse : <a  href="mailto:arthur.laurent2@etu.unilasalle.fr">arthur.laurent2@etu.unilasalle.fr</a></p>
                </div><!--.help-text-->
            </div><!--.login-tab-content-->

            <div id="signup-tab-content" class="active">
                <form class="signup-form" action="" method="post">
                    <input type="text" class="input" id="user_name" autocomplete="off" placeholder="Nom d'utilisateur" name="username" required>
                    <input type="email" class="input" id="user_email" autocomplete="off" placeholder="Email" name="email" required>
                    <input type="password" class="input" id="user_pass" autocomplete="off" placeholder="Mot de passe" name="pwd" minlength="5" required>
                    <input type="submit" class="button" id="submit" value="Créer un compte" name="submitsignup">
                </form><!--.login-form-->
                <?php if(isset($response)) echo "<h4 class='alert'>" . $response . "</h4>"; ?>
                <div class="help-text">
                    <p>En vous inscrivant, vous acceptez nos</p>
                    <p><a href="#">Conditions d'utilisation</a></p>
                </div><!--.help-text-->
            </div><!--.signup-tab-content-->
        </div><!--.tabs-content-->
    </div><!--.form-wrap-->
</body>

</html>




