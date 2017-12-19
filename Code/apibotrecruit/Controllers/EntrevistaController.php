<?php
/*
 *   Author: Samuel de Souza Silva
 */
 
class EntrevistaController{

  private $entrevistaModel = null;

  public function __construct($params){
    $this->entrevistaModel = new EntrevistaModel();
    $result = 'error';

    if(count($params) >= 2){
      if($params[1] == "botadd"){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $post_body = file_get_contents('php://input');

            $user_face = json_decode($post_body);

            $result = $this->entrevistaModel->insert($user_face->user_id, $user_face->vaga_id);
        }
      }elseif($params[1] == "nextquestion" && count($params) >= 4){
        $result = $this->entrevistaModel->getNextQuestion($params[2], $params[3]);
      }
    }

    header('Content-type: application/json');
    echo json_encode(array('retorno' =>  $result));
	}
}
