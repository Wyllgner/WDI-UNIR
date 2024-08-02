<?php
require 'database.php';
require 'User.php';

// Inicie a sessão
session_start();

// Verifique se o usuário está autenticado e se a sessão contém o nome de usuário
if (!isset($_SESSION['username'])) {
    die('Você precisa estar logado para acessar esta página.');
}

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Nome de usuário do usuário logado
$loggedInUser = $_SESSION['username'];
$user = User::find($_SESSION['user_id']);

// Processa a atualização das informações do usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!empty($newPassword) && !empty($confirmPassword)) {
        if($newPassword === $confirmPassword){
            $user->password = password_hash($newPassword, PASSWORD_BCRYPT); // Atualiza a senha
        }
        else($error ='Senhas não coincidem');
    }

    
    $existingUser= User::where('username',$newUsername)->first();

    if(isset($newUsername) && !empty($newUsername) ){
        if(!$existingUser || $existingUser->id ==$_SESSION['user_id']){
            $user->username = $newUsername; // Atualiza o nome de usuário
            $user->save();
            $_SESSION['username'] = $newUsername;// Atualiza o username da sessão
        }
        else{
            $error='Username já está em uso';
        }
    } else {
        $error = 'As senhas não coincidem.';
    }
    if(!isset($error)){
        header('Location: feed.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário</title>
    <style>
        /* estilos gerais */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* container principal */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        /* formulário de atualização do perfil */
        .profile-form {
            margin-bottom: 20px;
        }

        .profile-form input[type="text"],
        .profile-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .profile-form button {
            background-color: #1DA1F2; /* cor do Twitter */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Atualizar Perfil</h1>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Formulário para atualizar o perfil -->
        <div class="profile-form">
            <form action="profile.php" method="POST">
                <label for="new_username">Novo Nome de Usuário:</label>
                <input type="text" name="new_username" id="new_username" value="<?php echo htmlspecialchars($user->username); ?>" >

                <label for="new_password">Nova Senha:</label>
                <input type="password" name="new_password" id="new_password" >

                <label for="confirm_password">Confirme a Nova Senha:</label>
                <input type="password" name="confirm_password" id="confirm_password" >

                
                <button type="submit">Atualizar</button>
                
            </form>
            <br>
            <a href= "feed.php"><button>Voltar</button></a>
        </div>
    </div>
</body>
</html>
