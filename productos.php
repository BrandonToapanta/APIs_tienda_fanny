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
    $sql = $dbConn->prepare("SELECT * FROM productros where id=:id");
    $sql->bindValue(':id', $_GET['id']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count==0) {
      header("HTTP/1.1 204 No Content");
      echo "No existe el registro ",$_GET['id'];
      
    }else{
      
      echo "Si existe el registro";
      $sql = $dbConn->prepare("SELECT * FROM productros where id=:id");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
    }
    
  }else {
    //Busqueda por tienda left join
    if(isset($_GET['tienda_id'])){
      $sql = $dbConn->prepare("SELECT * FROM productros where tienda_id=:tienda_id");
      $sql->bindValue(':tienda_id', $_GET['tienda_id']);
      $sql->execute();
      $row_count =$sql->fetchColumn();
      
      if ($row_count==0) {
        header("HTTP/1.1 204 No Content");
        echo "No existe el registro ",$_GET['tienda_id'];
        
      }else{
        
        echo "Si existe el registro";
        
        $sql = $dbConn->prepare("SELECT tienda.tienda_nombre,  tienda.tienda_encargado, productros.prod_nombre, productros.prod_codigo, productros.prod_marca, productros.prod_precio, productros.prod_tmanio FROM productros JOIN tienda ON productros.tienda_id = tienda.id where tienda_id=:tienda_id");
        $sql->bindValue(':tienda_id', $_GET['tienda_id']);
        $sql->execute();
        header("HTTP/1.1 200 OK");
        // echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
        echo json_encode( $sql->fetchAll(PDO::FETCH_ASSOC)  );
        exit();
      }

    }else{
      if(isset($_GET['prod_codigo'])){
        $sql = $dbConn->prepare("SELECT * FROM productros where prod_codigo=:prod_codigo");
        $sql->bindValue(':prod_codigo', $_GET['prod_codigo']);
        $sql->execute();
        $row_count =$sql->fetchColumn();
        
        if ($row_count==0) {
          header("HTTP/1.1 204 No Content");
          echo "No existe el registro ",$_GET['prod_codigo'];
          
        }else{
          
          echo "Si existe el registro";
          
          $sql = $dbConn->prepare("SELECT tienda.tienda_nombre, tienda.tienda_encargado, productros.prod_nombre, productros.prod_codigo, productros.prod_marca, productros.prod_precio, productros.prod_tmanio FROM productros JOIN tienda ON productros.tienda_id = tienda.id where prod_codigo=:prod_codigo");
          $sql->bindValue(':prod_codigo', $_GET['prod_codigo']);
          $sql->execute();
          header("HTTP/1.1 200 OK");
          // echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
          echo json_encode( $sql->fetchAll(PDO::FETCH_ASSOC)  );
          exit();
        }
  
      }else{

        //Mostrar lista de post
        $sql = $dbConn->prepare("SELECT * FROM productros");
        
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode( $sql->fetchAll()  );
        exit();
      }

    }
  }

}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
  if (isset($_POST['prod_codigo'])){
    $sql = $dbConn->prepare("SELECT * FROM productros where prod_codigo=:prod_codigo");
    $sql->bindValue(':prod_codigo', $_POST['prod_codigo']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      header("HTTP/1.1 204 No Content");
      echo "Ya existe el codigo ", $_POST['prod_codigo'];
    }else{
      echo "Guardado Exitosamente";
      $input = $_POST;
      $sql = "INSERT INTO productros
            (prod_nombre, prod_codigo, prod_marca, prod_precio, prod_tmanio, tienda_id)
            VALUES
            (:prod_nombre, :prod_codigo, :prod_marca, :prod_precio, :prod_tmanio, :tienda_id)";
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
    echo "EL campo codigo es obligatorio";
  }

}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
  if (isset($_GET['id'])){
  	// $id = $_GET['id'];
    $sql = $dbConn->prepare("SELECT COUNT(*) FROM productros where id=:id");
    $sql->bindValue(':id', $_GET['id']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    // echo $row_count;
    if ($row_count == 0) {
      echo "No existe el registro ",$_GET['id'];
      header("HTTP/1.1 400 Bad Request"); //error 400 por no ejecutar el delete

    }else{
      $id = $_GET['id'];
      $statement = $dbConn->prepare("DELETE FROM productros where id=:id");
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


  if (isset($_GET['prod_codigo'])){
    $sql = $dbConn->prepare("SELECT * FROM productros where prod_codigo=:prod_codigo");
    $sql->bindValue(':prod_codigo', $_GET['prod_codigo']);
    $sql->execute();
    $row_count =$sql->fetchColumn();
    if ($row_count>0) {
      $input = $_GET;
      $postId = $input['prod_codigo'];
      $fields = getParams($input);

      $sql = "
            UPDATE productros
            SET $fields
            WHERE prod_codigo='$postId'
             ";

      $statement = $dbConn->prepare($sql);
      bindAllValues($statement, $input);

      $statement->execute();
      header("HTTP/1.1 200 OK");
      echo "Actualizado Exitosamente el producto ", $_GET['prod_codigo'];
      exit();
    }else{
      header("HTTP/1.1 204 No Content");
      echo "No existe el codigo ", $_GET['prod_codigo'];
    }
  }else{
    echo "El parametro codigo es obligatorio";
  }
}


//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>