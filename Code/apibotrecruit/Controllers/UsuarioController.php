<?php

class UsuarioController{

  private $usuarioModel = null;

  public function __construct($params){
    $this->usuarioModel = new UsuarioModel();
    $success = 'error';

    if(count($params) >= 2){
      if($params[1] == "botadd"){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $post_body = file_get_contents('php://input');

            $user_face = json_decode($post_body);

            $usuario = new Usuario();

    			  $usuario->nome = $user_face->first_name . $user_face->last_name;
            $usuario->facebook_id = $user_face->id;
            $usuario->facebook_img = $user_face->profile_pic;

            $success = $this->usuarioModel->insert($usuario);
        }
      }elseif($params[1] == "setpreent"){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $post_body = file_get_contents('php://input');

            $user_face = json_decode($post_body);

            $usuario = new Usuario();

    			  $usuario->entatual = $user_face->entrevista_id;
            $usuario->facebook_id = $user_face->user_id;

            $success = $this->usuarioModel->setPreEnt($usuario);
        }
      }
    }
    header('Content-type: application/json');
    echo json_encode(array('retorno' =>  $success));
	}
}
