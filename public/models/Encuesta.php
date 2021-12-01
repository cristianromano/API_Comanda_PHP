<?php


class Encuesta{

    public $puntuacion_mesa;
    public $puntuacion_mozo;
    public $puntuacion_trabajador;
    public $puntuacion_lugar;
    public $descripcion;
    public $numero_pedido;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (puntuacion_mesa,puntuacion_mozo,puntuacion_trabajador,puntuacion_lugar,
        descripcion,numero_pedido)
         VALUES (:puntuacion_mesa,:puntuacion_mozo,:puntuacion_trabajador,:puntuacion_lugar,:descripcion,:numero_pedido)");
        $consulta->bindValue(':puntuacion_mesa', $this->puntuacion_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacion_mozo', $this->puntuacion_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacion_trabajador', $this->puntuacion_trabajador, PDO::PARAM_INT);
        $consulta->bindValue(':puntuacion_lugar', $this->puntuacion_lugar, PDO::PARAM_INT);
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':numero_pedido', $this->numero_pedido, PDO::PARAM_INT);
      
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }


    public static function obtenerMejoresEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE puntuacion_lugar > :ranking ");
        $consulta->execute();

        $consulta->bindValue(':ranking', 8, PDO::PARAM_INT);
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }










}