<?php
include "config.php";
include "utils.php";


$dbConn =  connect($db);

/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
      
  if (isset($_GET['id']))
  {
    $sql = $dbConn->prepare("SELECT * FROM tienda where id=:id");
    $sql->bindValue(':id', $_GET['id']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe la tienda ",$_GET['id'];
      
    }else{

      echo "Si existe la tienda";
      $sql = $dbConn->prepare("SELECT * FROM tienda where id=:id");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
    }

  }
  else {
    if(isset($_GET['tienda_nombre'])){
      $sql = $dbConn->prepare("SELECT * FROM tienda where tienda_nombre=:tienda_nombre");
      $sql->bindValue(':tienda_nombre', $_GET['tienda_nombre']);
      $sql->execute();
      $row_count =$sql->fetchColumn();
      
      if ($row_count==0) {
        header("HTTP/1.1 204 No Content");
        echo "No existe la tienda ",$_GET['tienda_nombre'];
        
      }else{
        
        echo "Si existe la tienda";
        
        $sql = $dbConn->prepare("SELECT tienda.tienda_nombre, tienda.tienda_telefono, tienda.tienda_direccion, productros.prod_nombre, productros.prod_precio FROM productros JOIN tienda ON productros.tienda_id = tienda.id where tienda_nombre=:tienda_nombre");
        $sql->bindValue(':tienda_nombre', $_GET['tienda_nombre']);
        $sql->execute();
        header("HTTP/1.1 200 OK");
        // echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
        echo json_encode( $sql->fetchAll(PDO::FETCH_ASSOC)  );
        exit();
      }

    }else{
      //Mostrar lista de post
      $sql = $dbConn->prepare("SELECT * FROM tienda");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
    }
  }

}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (isset($_POST['tienda_nombre'])){
    $sql = $dbConn->prepare("SELECT * FROM tienda where tienda_nombre=:tienda_nombre");
    $sql->bindValue(':tienda_nombre', $_POST['tienda_nombre']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe la tienda ", $_POST['tienda_nombre'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO tienda
            (tienda_nombre, tienda_direccion, tienda_telefono, tienda_encargado)
            VALUES
            (:tienda_nombre, :tienda_direccion, :tienda_telefono, :tienda_encargado)";
      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);
      $statement->execute();
      $postId = $dbConn->lastInsertId();
      if($postId)
      {
        $input['id'] = $postId;
        header("HTTP/1.1 200 OK");
        echo json_encode($input);
        exit();
  	 }
    }
  }else{
    echo "EL campo nombre es obligatorio";
  }

}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['id'])){
  	// $id = $_GET['id'];
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM tienda where id=:id");
    $sql->bindValue(':id', $_GET['id']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['id'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $id = $_GET['id'];
      $statement = $dbConn->prepare("DELETE FROM tienda where id=:id");
      $statement->bindValue(':id', $id);
      $statement->execute();
      echo "Eliminado el registro ",$_GET['id'];
    	header("HTTP/1.1 200 OK");
    	exit();
    }
  }else{
    echo "El parametro id es obligatorio";
  }


}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{


  if (isset($_GET['tienda_nombre'])){
    $sql = $dbConn->prepare("SELECT * FROM tienda where tienda_nombre=:tienda_nombre");
    $sql->bindValue(':tienda_nombre', $_GET['tienda_nombre']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['tienda_nombre'];
      $fields = getParams($input);

      $sql = "
            UPDATE tienda
            SET $fields
            WHERE tienda_nombre='$postId'
             ";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizado Exitosamente la tienda ", $_GET['tienda_nombre'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe la tienda ", $_GET['tienda_nombre'];
    }
  }else{
    echo "El parametro nombre es obligatorio";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>