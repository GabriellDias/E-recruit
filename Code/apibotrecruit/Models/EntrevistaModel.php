<?php

class EntrevistaModel{

	private $conn = null;

	public function __construct(){
		$this->conn = ConnectionFactory::conectar();
	}

	public function insert($face_user, $vaga_id){

		$sql = "INSERT INTO entrevista (facebook_id, fk_vaga, status) VALUES (?,?,?)";

		try{
			$inserir = $this->conn->prepare($sql);
			$inserir->bindValue(1, $face_user);
			$inserir->bindValue(2, $vaga_id);
			$inserir->bindValue(3, 0);
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

	public function getNextQuestion($face_user, $vaga_id){
		$pergunta = new Pergunta();
		$pergunta->start = 0;
		$pergunta->finish = 0;

		$sql = "SELECT ehr.datahora datahora, ehr.fk_pergunta pergunta, ent.id entrevista FROM entrevista ent ".
					" 	LEFT JOIN entrevista_has_resposta ehr ON  ehr.fk_entrevista = ent.id".
					" WHERE ent.facebook_id = ? AND ent.fk_vaga = ?  ORDER by ehr.datahora desc";

		$question = 0;

		try{

			$search = $this->conn->prepare($sql);
			$search->bindValue(1,$face_user);
			$search->bindValue(2,$vaga_id);
			$search->execute();

			if($search->rowCount() > 0){
				$dados = $search->fetchAll(PDO::FETCH_ASSOC);

				foreach ($dados as $dadosent) {

					if(empty($dadosent['pergunta'])){
						$pergunta->start = 1;
					}else{
						$question = $dadosent['pergunta'];
					}

					$pergunta->entrevista = $dadosent['entrevista'];
					break;
				}
			}else{
				return $pergunta;
			}
		}catch(PDOException $e){
			return "Erro: ".$e->getMessage();
		}

		if($pergunta->start == 1 && $pergunta->has != 1){
			$sql = " SELECT perg.descricao texto, perg.id perg_id  FROM vaga_has_pergunta vhp".
							" INNER JOIN pergunta perg ON perg.id = vhp.fk_pergunta".
							" WHERE vhp.fk_vaga = ? AND vhp.indice = 1";
			try{

				$busca = $this->conn->prepare($sql);
				$busca->bindValue(1,$vaga_id);
				$busca->execute();

				$dados = $busca->fetch(PDO::FETCH_ASSOC);

				if($busca->rowCount() == 1){
					$pergunta->texto = $dados['texto'];
					$pergunta->id = $dados['perg_id'];
					$pergunta->has = 1;
				}

			}catch(PDOException $e){
				echo "Erro: ".$e->getMessage();
			}
		}else{
			$sql = " SELECT perg.descricao texto, perg.id perg_id  FROM vaga_has_pergunta vhp".
							" INNER JOIN pergunta perg ON perg.id = vhp.fk_pergunta".
							" WHERE vhp.fk_vaga = ? AND vhp.indice = (SELECT vhp2.indice FROM vaga_has_pergunta vhp2 WHERE vhp2.fk_vaga = ? AND vhp2.fk_pergunta = ?) + 1";
			try{

				$busca = $this->conn->prepare($sql);
				$busca->bindValue(1,$vaga_id);
				$busca->bindValue(2,$vaga_id);
				$busca->bindValue(3,$question);
				$busca->execute();

				$dados = $busca->fetch(PDO::FETCH_ASSOC);

				if($busca->rowCount() == 1){
					$pergunta->texto = $dados['texto'];
					$pergunta->id = $dados['perg_id'];
					$pergunta->has = 1;
				}else{
					$pergunta->finish = 1;
					return $pergunta;
				}

			}catch(PDOException $e){
				echo "Erro: ".$e->getMessage();
			}
		}

		return $pergunta;
	}
}

?>
