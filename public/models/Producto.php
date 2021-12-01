<?php

class Producto
{
    public $id_producto;
    public $nombre;
    public $precio;
    public $stock;
    public $codigo_producto;
    public $tipo;

    public function crearProducto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (nombre,precio,stock,codigo_producto,tipo) VALUES (:nombre,:precio,:stock,:codigo_producto,:tipo)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_producto', $this->codigo_producto, PDO::PARAM_STR);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, nombre, precio , stock , codigo_producto , tipo FROM productos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
    }

    public static function obtenerProductoID($producto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, nombre, precio , stock , codigo_producto , tipo FROM productos WHERE nombre = :producto");
        $consulta->bindValue(':producto', $producto, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Producto');
    }

    public  function modificarProductoStock()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE productos SET stock = :stock  WHERE id_producto = :id");
        $consulta->bindValue(':stock', $this->stock, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id_producto, PDO::PARAM_INT);
        $consulta->execute();
    }

    // public static function borrarProducto($usuario)
    // {
    //     $objAccesoDato = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
    //     $fecha = new DateTime(date("d-m-Y"));
    //     $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
    //     $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
    //     $consulta->execute();
    // }

    public static function calcularMonto($arrCant, $arrProd)
    {
        $final = 0;
        $arrProductos = Producto::obtenerTodos();

        for ($i = 0; $i < count($arrProd); $i++) {

            foreach ($arrProductos as $productos) {
                if ($productos->nombre == $arrProd[$i]) {
                    $final += $productos->precio * $arrCant[$i];
                }
            }
        }

        return $final;
    }






    
}
