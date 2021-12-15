<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<title>Cine</title>
<link rel="stylesheet" href="Ejercicio6.css"/>



</head>
<body>

 <h1/>Base de Datos Cine</h1>

<main>
<h2>Menu</h2>
<?php 
	session_start();
	class BaseDatos {
		protected $mensajes;
		protected $servername;
		protected $username;
		protected $password;
		protected $database;
		protected $tabla;
		
	
		public function __construct(){
			set_error_handler(function($errno, $errstr, $errfile, $errline) {
			// error was suppressed with the @-operator
			if (0 === error_reporting()) {
				return false;
			}
				throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
			});
			$this->mensajes="";
			$this ->servername="localhost";
			$this ->username="DBUSER2021";
			$this ->password="DBPSWD2021";
			$this ->database="baseDatosCine";
			$this ->tabla="Cine";
			

		}
		
		public function getMensajes(){
			return $this->mensajes;
		}
		protected function getConexion(){
			$db=new mysqli($this ->servername,$this ->username,$this ->password,$this ->database);
			
			if($db->connect_error){
				$this->mensajes.="ERROR de conexión:".$db->connect_error;
				return;
			}
			
			return $db;

		}
		protected function existeTabla($db){
			if(!$db->query("DESCRIBE ". $this ->tabla)){
				$this->mensajes.="Tabla no creada.";
				
			}
		}
		
		protected function camposVacios($datos){
			foreach($datos as $x => $x_value) {
				if(empty($x_value)){
					$this->mensajes.="No se permiten campos vacios.";
				}
				if(!empty($this -> mensajes)) return;
			}
		}
		
		protected function execute($db,$consultaPre,$operacion){
			if($consultaPre->execute()){
				$this->mensajes.="Fila ".$operacion."."."\n";
			}
			else{
				$this->mensajes.="ERROR SQL:".$db->error."\n";
			}
			
		}
		
		protected function findId($db,$datos){
			$calcularTotal=$db->prepare("SELECT id FROM ".$this ->tabla." WHERE id=?");
			$calcularTotal->bind_param('i',$datos["id"]);
			$calcularTotal->execute();
			$calcularTotal->store_result();
			$calcularTotal -> fetch();
			$total = $calcularTotal->num_rows;
			if($total==0){
				$this->mensajes.= "No se ha encontrado el ID \n";
			}
			
		}
		
		
		
		public function crearBase(){
			$db=null;
			try{
				$db=new mysqli($this ->servername,$this ->username,$this ->password);
			
				if($db->connect_error){
					$this->mensajes.="ERROR de conexión:".$db->connect_error."";
				return;
				}
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "La base de datos no se creará \n";
				return;
			}
			
			else{
			
				$cadenaSQL="CREATE DATABASE IF NOT EXISTS ". $this ->database ." COLLATE utf8_spanish_ci";
				if($db->query($cadenaSQL)===TRUE){
					$this->mensajes.="Base de datos ". $this ->database ." creada con éxito";
					}
				else{
					$this->mensajes.="ERROR en la creación de la Base de Datos " .$this ->database.".Error:".$db->error."";
					return;
				}
			}
			$db->close();
		}
		
		public function crearTabla(){
			
			
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "La tabla no se creará \n";
				return;
			}
			if(!empty($this -> mensajes)) return;
			
			$crearTabla="CREATE TABLE IF NOT EXISTS ". $this ->tabla." (id INT NOT NULL, nombre VARCHAR(255) NOT NULL, 
					    ciudad VARCHAR(255) NOT NULL, empresa VARCHAR(9) NOT NULL,  
						PRIMARY KEY(id))";
			if($db->query($crearTabla)===TRUE){
				$this->mensajes.="Tabla ".$this ->tabla." creada con éxito";
			}
			else{
				$this->mensajes.="ERROR en la creación de la tabla persona. Error:".$db->error."";
				return;
			}
			$db->close();
		}
		
		protected function nextId(){
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "No se puede acceder \n";
				return;
			}
			
			$result=$db->query("SELECT MAX(id) FROM".$this ->tabla);
			$row=mysqli_fetch_array($result, MYSQLI_NUM);
			$maxresult=$row[0];
			return $maxresult+1;
			
		}
		
		public function insertarDatos($datos){
				
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "Los datos no se insertarán \n";
				return;
			}
			$this->existeTabla($db);
			$this->camposVacios($datos);
			if(!empty($this -> mensajes)) return;
		
			$consultaPre=$db->prepare("INSERT INTO " .$this ->tabla." VALUES(?,?,?,?)");
			$consultaPre->bind_param('isss',$this->nextId(),$datos["nombre"],$datos["ciudad"], $datos["empresa"]);
			$this->execute($db,$consultaPre,"agregada");
			
			$consultaPre->close();
			$db->close();
				
		}
		
		
		public function modificarDatos($datos){
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "Los datos no se modificarán \n";
				return;
			}
			$this->existeTabla($db);
			$this->camposVacios($datos);
			
			$this->findId($db,$datos);
				
			
			if(!empty($this -> mensajes)) return;
			
			
			$consultaPre=$db->prepare("UPDATE " .$this ->tabla." SET nombre=?,ciudad=?,empresa=? WHERE id=?");
			$consultaPre->bind_param('sssi',$datos["nombre"],$datos["ciudad"], $datos["empresa"],$datos["id"]);
			$this->execute($db,$consultaPre,"modificada");
			$consultaPre->close();
			$db->close();
		
		}
		
		public function borrarDatos($datos){
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "Los datos no se borrarán \n";
				return;
			}
			$this->existeTabla($db,$datos);
			$this->camposVacios($datos);
			$this->findDNI($db,$datos);
			if(!empty($this -> mensajes)) return;
			
			$consultaPre=$db->prepare("DELETE FROM " .$this ->tabla." WHERE id=?");
			$consultaPre->bind_param('s',$datos["id"]);
			$this->execute($db,$consultaPre,"borrada");
			$consultaPre->close();
			$db->close();
		
		}

		
		public function importarCSV($fileName){
			
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "La tabla no se creará \n";
				return;
			}
			if(!empty($this -> mensajes)) return;
			$cadenaSQL="DELETE FROM ". $this ->tabla;
				if($db->query($cadenaSQL)===TRUE){
					$this->mensajes.="Base de datos ". $this ->database ." reiniciada con éxito \n";
				}
				else{
					$this->mensajes.="ERROR en el reinicio de la tabla " .$this ->database.".Error:".$db->error."\n";
					return;
			}
			$file = fopen($fileName, "r");
			$column = fgetcsv($file, 10000, ",");
			while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
				$id = "";
				if (isset($column[0])) {
					$id = mysqli_real_escape_string($db, $column[0]);
				}
			
				$nombre = "";
				if (isset($column[1])) {
					$nombre = mysqli_real_escape_string($db, $column[1]);
				}
				
				$ciudad = "";
				if (isset($column[3])) {
					$ciudad = mysqli_real_escape_string($db, $column[3]);
				}
				$empresa = "";
				if (isset($column[4])) {
					$empresa = mysqli_real_escape_string($db, $column[4]);
				}
				$consultaPre=$db->prepare("INSERT INTO " .$this ->tabla." VALUES(?,?,?,?)");
				$consultaPre->bind_param('isss',$id,$nombre,$ciudad,$empresa);
				$this->execute($db,$consultaPre,"agregada");
				
				$consultaPre->close();
			}
			$db->close();
		}
		
		
	
	protected function mysqli_field_name($result, $field_offset)
		{
			$properties = mysqli_fetch_field_direct($result, $field_offset);
			return is_object($properties) ? $properties->name : null;
		}
	
	}
	
	class Formulario {
		protected $insertar;
		protected $modificar;
		protected $borrar;
		public function __construct(){
			$this->insertar=array("id"=>"","nombre"=>"","ciudad"=>"","empresa"=>"");
			$this->modificar=array("id"=>"","nombre"=>"","ciudad"=>"","empresa"=>"");
			$this->borrar=array("id"=>"");
			
		}
		public function setInsertar($array){
			$this -> insertar=$array;
		}
		public function getInsertar(){
			return $this -> insertar;
		}
		public function setModificar($array){
			$this -> modificar=$array;
		}
		public function getModificar(){
			return $this -> modificar;
		}
		public function setBorrar($array){
			$this -> borrar=$array;
		}
		public function getBorrar(){
			return $this -> borrar;
		}
	
	}

	 $base = new BaseDatos();
	 $mensajes="";
	 if( isset( $_SESSION['formulario'] ) ) {
		$formulario =$_SESSION['formulario'];
		
	}
	 else{
		 $formulario = new Formulario();
		
	 }
	 
	 $insertar = $formulario -> getInsertar();
	 $modificar = $formulario -> getModificar();
	 $borrar = $formulario -> getBorrar();
	 

	 if (count($_POST)>0) 
    {  	
		
		if(isset($_POST['btCrearBase'])) $base->crearBase();
		if(isset($_POST['btCrearTabla'])) $base->crearTabla();
		if(isset($_POST['btInsertar'])){ 
			$datos=array("id"=>$_POST["id"],"nombre"=>$_POST["nombre"],"ciudad"=>$_POST["ciudad"],"empresa"=>$_POST["empresa"]);
			$base->insertarDatos($datos);
			$formulario -> setInsertar($datos);
			
			}
		if(isset($_POST['btModificar'])){ 
			$datos=array("id"=>$_POST["id"],"nombre"=>$_POST["nombre"],"ciudad"=>$_POST["ciudad"],"empresa"=>$_POST["empresa"]);
			$base->ModificarDatos($datos);
			$formulario -> setModificar($datos);
			
		}
			
		if(isset($_POST['btBorrar'])){ 
			$datos=array("id"=>$_POST["id"]);
			$base->borrarDatos($datos);
			$formulario -> setBorrar($datos);
		}	
		
		
		
		
		if(isset($_POST['btImpCsv'])){ 
			
			$fileName = $_FILES["file"]["tmp_name"];
			$base->importarCSV($fileName);
			
		}
		
		$mensajes=$base->getMensajes();
		
		
		$insertar = $formulario -> getInsertar();
		$modificar = $formulario -> getModificar();
		$borrar = $formulario -> getBorrar();
		$buscar = $formulario -> getBuscar();
		$_SESSION['formulario'] = $formulario;
		
    
    }


	echo " 
		
	<h3>Operaciones generales</h3>
	
	<form action='#' method='post' name='generales'>
		<input type='submit'  value='Crear base' name='btCrearBase' >
		<input type='submit'  value='Crear tabla' name='btCrearTabla' >
	</form>
	<h3>Insertar Datos</h3>
	<form action='#' method='post' name='insercion'>
		<label>Nombre: <input type='text' name='nombre' value='$insertar[nombre]'></label>
		<label>Ciudad: <input type='text' name='ciudad' value='$insertar[ciudad]'></label>
		<label>Empresa: <input type='text' name='empresa' value='$insertar[empresa]'></label>
		
		<input type='submit' value='Insertar Datos' name='btInsertar' >
	</form>
	
	<h3>Borrar Datos</h3>
	<p>Borrar el campo con el id = ?</p>
	<form action='#' method='post' name='borrado'>
		<label>Id: <input type='text' name='id' value='$borrar[id]'>
		<input type='submit' value='Borrar Datos' name='btBorrar' ></label>
	</form>
	
	<h3>Modificar Datos</h3>
	<p>Modifica el campo con el id = ?</p>
	<form action='#' method='post' name='modificion'>
		<label>Id: <input type='text' name='id' value='$modificar[id]'></label>
		 <label>Nombre: <input type='text' name='nombre' value='$modificar[nombre]'></label>
		<label>Ciudad: <input type='text' name='ciudad' value='$modificar[ciudad]'></label>
		<label>Empresa: <input type='text' name='empresa' value='$modificar[empresa]'></label>
		<input type='submit' value='Modificar Datos' name='btModificar' >
	</form>
	
	<h3>Mensajes</h3>
	<textarea >$mensajes</textarea>
	
	
	<h3>Importar de csv</h3>
	<form action='#' method='post' name='importar' enctype='multipart/form-data'>
		<label>Elige un archivo csv<input type='file' name='file' accept='.csv'></label>
		<input type='submit' value='Importar' name='btImpCsv' >
		
	</form>
	
	

	
	";
?>
</main>



</body>
</html>