<?php
/*
 *   Author: Samuel de Souza Silva
 */
 
class AreaModel{

	private $conn = null;

	public function __construct(){
		$this->conn = ConnectionFactory::conectar();
	}

	public function getAll(){
		$areas = Array();

		$sql = "SELECT id, titulo, descricao, img_link FROM area";

		try{

			$search = $this->conn->prepare($sql);
			$search->execute();

			if($search->rowCount() > 0){
				$dados = $search->fetchAll(PDO::FETCH_ASSOC);

				foreach ($dados as $dadosareas) {
					$area = new Area();

					$area->id = $dadosareas['id'];
					$area->title = $dadosareas['titulo'];
					$area->subtitle = $dadosareas['descricao'];
					$area->image_url = $dadosareas['img_link'];

					array_push($areas,$area);
				}
			}

		}catch(PDOException $e){
			echo "Erro: ".$e->getMessage();
		}

		return $areas;
	}
}

?>
