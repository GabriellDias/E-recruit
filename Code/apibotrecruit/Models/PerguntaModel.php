<?php

class PerguntaModel{

	private $conn = null;

	public function __construct(){
		$this->conn = ConnectionFactory::conectar();
	}

	public function setQuestionSent($question_id, $entrevista_id){

		$sql = "INSERT INTO entrevista_has_resposta (fk_pergunta, fk_entrevista, datahora) VALUES (?,?,now())";

		try{
			$inserir = $this->conn->prepare($sql);
			$inserir->bindValue(1, $question_id);
			$inserir->bindValue(2, $entrevista_id);
			$inserir->execute();

			if($inserir->rowCount() == 1){
				return true;
			}else{
				return false;
			}

		}catch (PDOException $e){
			return "Erro: ".$e->getMessage();
		}
	}


public function sendResponse($texto, $user_id){
		$sql = "SELECT preent entrevista FROM usuario WHERE facebook_id = ?";

		$entrevista = 0;

		try{

			$busca = $this->conn->prepare($sql);
			$busca->bindValue(1, $user_id);
			$busca->execute();

			$dados = $busca->fetch(PDO::FETCH_ASSOC);

			if($busca->rowCount() == 1){
			$entrevista = $dados['entrevista'];
			}

		}catch(PDOException $e){
			return "Erro: ".$e->getMessage();
		}

		if($entrevista != 0){
			$sql = " UPDATE entrevista_has_resposta SET resposta = ? WHERE fk_entrevista = ? AND resposta IS NULL";

			try{
				$update = $this->conn->prepare($sql);
				$update->bindValue(1, $texto);
				$update->bindValue(2, $entrevista);
				$update->execute();

				if($update->rowCount() >= 1){
					$sql = "SELECT fk_vaga vaga FROM entrevista WHERE id = ?";

					$vaga = 0;

					try{

						$busca = $this->conn->prepare($sql);
						$busca->bindValue(1,$entrevista);
						$busca->execute();

						$dados = $busca->fetch(PDO::FETCH_ASSOC);

						if($busca->rowCount() == 1){
							return $dados['vaga'];
						}

					}catch(PDOException $e){
						return "Erro: ".$e->getMessage();
					}
				}else{
					return "Erro: on update";
				}
			}catch (PDOException $e){
				return "Erro: ".$e->getMessage();
			}
		}
	}
}

?>
