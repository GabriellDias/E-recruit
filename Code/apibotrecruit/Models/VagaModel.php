<?php
/*
 *   Author: Samuel de Souza Silva
 */
 
class VagaModel{

	private $conn = null;

	public function __construct(){
		$this->conn = ConnectionFactory::conectar();
	}

	public function getAll(){
		$vagas = Array();

		$sql = "SELECT id, nome, salario, descricao, img_link, link_web FROM vaga";

		try{

			$search = $this->conn->prepare($sql);
			$search->execute();

			if($search->rowCount() > 0){
				$dados = $search->fetchAll(PDO::FETCH_ASSOC);

				foreach ($dados as $dadosVagas) {
					$vaga = new Vaga();

					$vaga->id = $dadosVagas['id'];
					$vaga->title = $dadosVagas['nome'];
					$vaga->subtitle = $dadosVagas['descricao'];
					$vaga->salario = $dadosVagas['salario'];
					$vaga->link_web = $dadosVagas['link_web'];
					$vaga->image_url = $dadosVagas['img_link'];

					array_push($vagas,$vaga);
				}
			}

		}catch(PDOException $e){
			echo "Erro: ".$e->getMessage();
		}

		return $vagas;
	}
}

?>
