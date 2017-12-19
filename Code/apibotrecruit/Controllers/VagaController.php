<?php

class VagaController{

  private $vagaModel = null;

  public function __construct($params){
    $this->vagaModel = new VagaModel();

    if(count($params)  >= 2){
      if($params[1] == "showall"){
        header('Content-type: application/json');
        echo json_encode(array('vagas' => $this->vagaModel->getAll()));
      }
    }

	}

}
