<?php
/*
 *   Author: Samuel de Souza Silva
 */
 
class UsuarioModel{

	private $conn = null;

	public function __construct(){
		$this->conn = ConnectionFactory::conectar();
	}

	public function insert($usuario){

		$sql = "INSERT INTO usuario (nome, facebook_id, facebook_img) VALUES (?,?,?)" .
						" ON DUPLICATE KEY UPDATE nome = ?, facebook_id = ?, facebook_img = ?";

		try{
			$inserir = $this->conn->prepare($sql);
			$inserir->bindValue(1, $usuario->nome);
			$inserir->bindValue(2, $usuario->facebook_id);
			$inserir->bindValue(3, $usuario->facebook_img);
			$inserir->bindValue(4, $usuario->nome);
			$inserir->bindValue(5, $usuario->facebook_id);
			$inserir->bindValue(6, $usuario->facebook_img);
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

	public function setPreEnt($usuario){

		$sql = "UPDATE usuario SET preent = ? WHERE facebook_id = ?";

		try{
			$inserir = $this->conn->prepare($sql);
			$inserir->bindValue(1, $usuario->entatual);
			$inserir->bindValue(2, $usuario->facebook_id);
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
}

?>
