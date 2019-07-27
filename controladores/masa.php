<?php

class masa
{
	public function post($peticion)
	{
		return self::guardar_informacion();
	}
	public function get($peticion)
	{
		return self::obtener_informacion();
	}
	private function guardar_informacion()
	{
		$idUsuario = usuarios::autorizar();        
		$body = file_get_contents('php://input');
        $informacion = json_decode($body);
        /**/
        $peso = $informacion->peso;
        $estatura = $informacion->estatura;
        $imc = $informacion->imc;
        $resultado = $informacion->resultado;
        $fecha = $informacion->fecha;
        /**/
        try {

            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();

            // Sentencia INSERT
            $comando = "INSERT INTO informacion_lista VALUES(null,?,?,?,?,?,?)";
            $sentencia = $pdo->prepare($comando);

            $sentencia->bindParam(1, $idUsuario);
            $sentencia->bindParam(2, $peso);
            $sentencia->bindParam(3, $estatura);
            $sentencia->bindParam(4, $imc);
			$sentencia->bindParam(5, $resultado);
            $sentencia->bindParam(6, $fecha);


            $resultado = $sentencia->execute();

            if ($resultado) {
            	http_response_code(200);
                return [
                	"estado" => 1,
                	"mensaje" => "Datos han sido guardados correctamente"
                ];
            } else {
            	http_response_code(400);
                return [
                	"estado" => 2,
                	"mensaje" => "Error al guardar los datos"
                ];
            }
        } catch (PDOException $e) {
            throw new ExcepcionApi(3, $e->getMessage());
        }
	}
	private function obtener_informacion()
	{
		$idUsuario = usuarios::autorizar();
        $comando = "SELECT * FROM informacion_lista WHERE idUsuario=?";

        $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
        $sentencia->bindParam(1, $idUsuario, PDO::PARAM_INT);           
        if ($sentencia->execute()) {
            http_response_code(200);
            return
            [
                "estado" => 1,
                "datos" => $sentencia->fetchAll(PDO::FETCH_ASSOC)
            ];
        }
	}
}
