<?php

class PerguntaController{

  private $perguntaModel = null;

  public function __construct($params){
    $this->perguntaModel = new PerguntaModel();
    $result = 'error';

    if(count($params)  >= 2){
      if($params[1] == "setsent"){

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $post_body = file_get_contents('php://input');

            $user_face = json_decode($post_body);

            $result = $this->perguntaModel->setQuestionSent($user_face->question_id, $user_face->entrevista_id);
        }
      }elseif($params[1] == "resposta"){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $post_body = file_get_contents('php://input');

          $user_face = json_decode($post_body);

          $result = $this->perguntaModel->sendResponse($user_face->texto, $user_face->user_id);
        }
      }
    }

    header('Content-type: application/json');
    echo json_encode(array('result' => $result));
	}
}
