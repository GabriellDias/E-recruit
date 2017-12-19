<?php
/*
 *   Author: Samuel de Souza Silva
 */
 
class AreaController{

  private $areaModel = null;

  public function __construct($params){
    $this->areaModel = new AreaModel();

    if(count($params)  >= 2){
      if($params[1] == "showall"){
        header('Content-type: application/json');
        echo json_encode(array('areas' => $this->areaModel->getAll()));
      }
    }
	}
}
