<?php



class Pedido{

    // public $id_producto;
    public $id_mesa;
    // public $id_usuario;
    public $mozo;
    public $nombre_producto;
    public $cantidad;
    public $estado;
    public $numero_pedido;
    // public $precio_final;
    public $tiempo;


    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (id_producto,id_mesa,id_usuario,mozo,nombre_producto,cantidad,estado,numero_pedido,precio_final,tiempo , fechamodificacion)
         VALUES (:id_producto,:id_mesa,:id_usuario,:mozo,:nombre_producto,:cantidad,:estado,:numero_pedido,:precio_final,:tiempo,:fechamodificacion)");
        $consulta->bindValue(':id_producto', $this->id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':id_usuario', $this->id_usuario, PDO::PARAM_INT);
        // $consulta->bindValue(':estado', $this->id_pedido, PDO::PARAM_INT);
        $consulta->bindValue(':numero_pedido', $this->numero_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':nombre_producto', $this->nombre_producto, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':mozo', $this->mozo, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $this->tiempo, PDO::PARAM_STR);
        $consulta->bindValue(':fechamodificacion', $this->fechamodificacion, PDO::PARAM_STR);
        $consulta->bindValue(':precio_final', $this->precio_final, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa , id_pedido ,nombre_producto,fecha_de_pedido, cantidad , estado , numero_pedido , tiempo , mozo  FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($numero_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        // $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, id_mesa, id_usuario , nombre_producto, cantidad , estado , numero_pedido FROM 'pedidos'
        // INNER JOIN productos ON 'productos.id_productos = pedidos.id_productos' WHERE numero_pedido = :pedido");
        $consulta = $objAccesoDatos->prepararConsulta('SELECT id_usuario , codigo_producto , numero_pedido , estado , tiempo , mozo
        FROM pedidos U INNER JOIN productos P' .' ON  U.id_producto = P.id_producto' .
        ' WHERE numero_pedido = :pedido  ');
        $consulta->bindValue(':pedido', $numero_pedido, PDO::PARAM_STR);
        $consulta->execute();

        // con fetchALL es todo el arr
        return $consulta->fetch(PDO::FETCH_OBJ);
    }

    public static function obtenerPedidoCuenta($numero_pedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        // $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, id_mesa, id_usuario , nombre_producto, cantidad , estado , numero_pedido FROM 'pedidos'
        // INNER JOIN productos ON 'productos.id_productos = pedidos.id_productos' WHERE numero_pedido = :pedido");
        $consulta = $objAccesoDatos->prepararConsulta('SELECT precio , cantidad , nombre
        FROM pedidos U INNER JOIN productos P' .' ON  U.id_producto = P.id_producto' .
        ' WHERE numero_pedido = :pedido  ');
        $consulta->bindValue(':pedido', $numero_pedido, PDO::PARAM_STR);
        $consulta->execute();

        // con fetchALL es todo el arr
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerTiempo($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT nombre_producto,fecha_de_pedido, cantidad , estado , tiempo , mozo  FROM pedidos WHERE numero_pedido = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerPedidoCocinero()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        // $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, id_mesa, id_usuario , nombre_producto, cantidad , estado , numero_pedido FROM 'pedidos'
        // INNER JOIN productos ON 'productos.id_productos = pedidos.id_productos' WHERE numero_pedido = :pedido");
        $consulta = $objAccesoDatos->prepararConsulta('SELECT nombre_producto , id_mesa , cantidad , estado , id_pedido , tiempo , mozo
        FROM pedidos U INNER JOIN productos P' .' ON  U.id_producto = P.id_producto' .
        ' WHERE codigo_producto = "Cocinero" AND estado = "abierto" ');
        $consulta->execute();

        // con fetchALL es todo el arr
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerPedidoBartender()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        // $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, id_mesa, id_usuario , nombre_producto, cantidad , estado , numero_pedido FROM 'pedidos'
        // INNER JOIN productos ON 'productos.id_productos = pedidos.id_productos' WHERE numero_pedido = :pedido");
        $consulta = $objAccesoDatos->prepararConsulta('SELECT nombre_producto , id_mesa , cantidad , estado , id_pedido , tiempo , mozo
        FROM pedidos U INNER JOIN productos P' .' ON  U.id_producto = P.id_producto' .
        ' WHERE codigo_producto = "Bartender" AND estado = "abierto" ');
        $consulta->execute();

        // con fetchALL es todo el arr
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerPedidoCervecero()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        // $consulta = $objAccesoDatos->prepararConsulta("SELECT id_producto, id_mesa, id_usuario , nombre_producto, cantidad , estado , numero_pedido FROM 'pedidos'
        // INNER JOIN productos ON 'productos.id_productos = pedidos.id_productos' WHERE numero_pedido = :pedido");
        $consulta = $objAccesoDatos->prepararConsulta('SELECT nombre_producto , id_mesa , cantidad , estado , id_pedido , tiempo , mozo
        FROM pedidos U INNER JOIN productos P' .' ON  U.id_producto = P.id_producto' .
        ' WHERE codigo_producto = "Cervecero" AND estado = "abierto" ');
        $consulta->execute();

        // con fetchALL es todo el arr
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function AlfanumericoRandom($length)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($chars), 0, $length);
    }


    public  function modificarPedido()
    {
        // var_dump(date('Y-m-d H:i:s'));
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado , tiempo = :tiempo , fechamodificacion = :horaModificacion WHERE id_pedido =:pedido ");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':pedido', $this->numero_pedido, PDO::PARAM_STR);
        $consulta->bindValue(':tiempo', $this->tiempo, PDO::PARAM_STR);
        $consulta->bindValue(':horaModificacion',date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function obtenerFechasPedido($codigo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT fecha_de_pedido, tiempo , fechamodificacion  FROM pedidos WHERE id_pedido = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);

        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_OBJ);
    }
    
















}