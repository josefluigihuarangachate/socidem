<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include '../model/consulSQL.php';
require_once '../vendor/autoload.php';
require_once '../model/credencialesLog.php';
 

if (isset($_GET['code'])){
    
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $correo =  $google_account_info->email;
    $clave =  md5($correo);
    $name_test  =  $google_account_info->name;

      
}else {

    $correo = $_POST['correo-login'];
    $clave = md5($_POST['clave-login']);

}
  

if (!$correo == "" && !$clave == "") {
    if (isset($_GET['code'])){  
        $verAfil = ejecutarSQL::consultar("SELECT `usuarios`.*, `perfil`.*, `usuarios`.`correo`, `usuarios`.`clave` FROM `usuarios` LEFT JOIN `perfil` ON `perfil`.`id` = `usuarios`.`id` WHERE `usuarios`.`correo` = '$correo'");
    }else {
        $verAfil = ejecutarSQL::consultar("SELECT `usuarios`.*, `perfil`.*, `usuarios`.`correo`, `usuarios`.`clave` FROM `usuarios` LEFT JOIN `perfil` ON `perfil`.`id` = `usuarios`.`id` WHERE `usuarios`.`correo` = '$correo' AND `usuarios`.`clave` = '$clave';");
    }
    
    
   
    
    while($datos_usuario=mysqli_fetch_assoc($verAfil)){
        $id_usuario=$datos_usuario['id'];
        $usuario=$datos_usuario['usuario'];
        $nombre=$datos_usuario['nombre'];
        $apellido=$datos_usuario['apellido'];
        $correo=$datos_usuario['correo'];
        $perfil=$datos_usuario['perfil'];
        $telefono=$datos_usuario['telefono'];
        $pais=$datos_usuario['pais'];
        $ciudad=$datos_usuario['ciudad'];
        $distrito=$datos_usuario['distrito'];
        $direccion=$datos_usuario['direccion'];
        $estado=$datos_usuario['estado']; 
        $last_login=$datos_usuario['last_login'];
        $imgPerfil = $datos_usuario['imagen_perfil'];
        $imgBanPerfil = $datos_usuario['ban_perfil'];
    } 
    
    $AfilC = mysqli_num_rows($verAfil);
        if ($AfilC > 0) {
        $_SESSION['id'] = $id_usuario;
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['apellido'] = $apellido;
        $_SESSION['correo'] = $correo;
        $_SESSION["perfil"] = $perfil;
        $_SESSION['telefono'] = $telefono;
        $_SESSION['pais'] = $pais;
        $_SESSION['ciudad'] = $ciudad;
        $_SESSION['distrito'] = $distrito;
        $_SESSION['direccion'] = $direccion;
        $_SESSION['estado'] = $estado;
        $_SESSION['last_login'] = $last_login;
        $_SESSION['imagen_perfil'] = $imgPerfil;
        $_SESSION["ban_perfil"] = $imgBanPerfil;

        
        
        $_SESSION["iniciarSesion"] = "ok";
       
        date_default_timezone_set('America/Lima');
        setlocale(LC_TIME, 'es_ES.UTF-8');
        setlocale(LC_TIME, 'spanish');
        $last_login_up = date('l jS F Y h:i:s A');
             
             
            consultasSQL::UpdateSQL("usuarios", "correo='$correo', last_login='$last_login_up' ", "correo='$correo'");
            if (isset($_GET['code'])){ 
                echo '<script> 	window.location = "../inicio"; </script>';
            }else{
                echo '<script> 	window.location = "inicio"; </script>';
            }
           
           
           
        } else {
            echo '<script> alert("Usuario no registrado"); 	window.location = "../registro"; </script>';
            // echo '<div class="progress"><div class="progress-bar progress-bar-danger" style="width: 100%">Usuario Incorrecto </div> </div>';
        }
    
    
} else {
    echo '<div class="progress"><div class="progress-bar progress-bar-danger" style="width: 100%">Campos Vacios</div> </div>';
}