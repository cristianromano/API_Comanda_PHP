<?php

class Usuario
{
    public $id_usuario;
    public $usuario;
    public $clave;
    public $codigo;
    public $email;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave,codigo,email) VALUES (:usuario, :clave,:codigo,:email)");
        if ($this->codigo == 'Cliente') {
            $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        } else {
            $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
            $consulta->bindValue(':clave', $claveHash);
        }
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':email', $this->email, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_usuario, clave , codigo , email FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_usuario, clave , codigo , email FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUsuarioEmail($email)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_usuario, clave , codigo , estado FROM usuarios WHERE email = :email");
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }


    public  function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave WHERE id = :id");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public  function modificarUsuarioEstado()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = :estado WHERE id_usuario = :id");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id_usuario, PDO::PARAM_INT);
        $consulta->execute();
    }


    public static function borrarUsuario($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    public static function Login($usr)
    {

        $obj_usuarios = Usuario::obtenerTodos();

        foreach ($obj_usuarios as $usuarioObj) {
            if ($usr->email == $usuarioObj->email) {
                if (password_verify($usr->clave, $usuarioObj->clave)) {
                    return true;
                }
                if ($usr->clave ==  $usuarioObj->clave) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function obtenerUsuarioMail($email){
        $arrUsuario = Usuario::obtenerTodos();
        foreach ($arrUsuario as $usuario) {
          if ($usuario->email == $email) {
            return $usuario->id_usuario;
          }
        }
      }

      // CODIGO = "TRABAJO , EJ: SOCIO"
      public static function obtenerUsuarioCodigo($email){
        $arrUsuario = Usuario::obtenerTodos();
        foreach ($arrUsuario as $usuario) {
          if ($usuario->email == $email) {
            return $usuario->codigo;
          }
        }
      }
    
}
