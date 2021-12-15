<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<title>Cine</title>
<link rel="stylesheet" href="Ejercicio7.css"/>



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
			$this ->tablaPeli="Pelicula";
			$this ->tablaSala="Sala";
			$this ->tablaSesion="Sesion";
			

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
		protected function existeTabla($db,$tabla){
			if(!$db->query("DESCRIBE ". $tabla)){
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
		
		protected function findId($db,$datos,$tabla){
			$calcularTotal=$db->prepare("SELECT id FROM ".$tabla." WHERE id=?");
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
					    ciudad VARCHAR(255) NOT NULL, empresa VARCHAR(255) NOT NULL,  
						PRIMARY KEY(id))";
			if($db->query($crearTabla)===TRUE){
				$this->mensajes.="Tabla ".$this ->tabla." creada con éxito \n";
			}
			else{
				$this->mensajes.="ERROR en la creación de la tabla cine. Error:".$db->error."\n";
				return;
			}
			
			$crearTabla="CREATE TABLE IF NOT EXISTS ". $this ->tablaPeli." (id INT NOT NULL, titulo VARCHAR(255) NOT NULL, 
					    duracion int NOT NULL, genero VARCHAR(255) NOT NULL, PRIMARY KEY(id))";
			if($db->query($crearTabla)===TRUE){
				$this->mensajes.="Tabla ".$this ->tablaPeli." creada con éxito\n";
			}
			else{
				$this->mensajes.="ERROR en la creación de la tabla pelicula. Error:".$db->error."\n";
				return;
			}
			
			$crearTabla="CREATE TABLE IF NOT EXISTS ". $this ->tablaSala." (id INT NOT NULL,idCine INT NOT NULL, numero int NOT NULL, 
					    capacidad int NOT NULL, PRIMARY KEY(id), FOREIGN KEY (idCine) REFERENCES Cine(id))";
			if($db->query($crearTabla)===TRUE){
				$this->mensajes.="Tabla ".$this ->tablaSala." creada con éxito \n";
			}
			else{
				$this->mensajes.="ERROR en la creación de la tabla sala. Error:".$db->error."\n";
				return;
			}
			
			$crearTabla="CREATE TABLE IF NOT EXISTS ". $this ->tablaSesion." (idSala INT NOT NULL, idPelicula INT NOT NULL, 
					    fecha VARCHAR(255) NOT NULL, hora VARCHAR(255) NOT NULL, PRIMARY KEY(idSala,idPelicula),
						FOREIGN KEY (idSala) REFERENCES Sala(id),FOREIGN KEY (idPelicula) REFERENCES Pelicula(id))";
			if($db->query($crearTabla)===TRUE){
				$this->mensajes.="Tabla ".$this ->tablaSesion." creada con éxito \n";
			}
			else{
				$this->mensajes.="ERROR en la creación de la tabla sesion. Error:".$db->error."\n";
				return;
			}
			$db->close();
		}
		
		protected function nextId($tabla){
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
			
			$result=$db->query("SELECT MAX(id) FROM ".$tabla);
			if($result===false){
				$maxresult=0;
			}
			else{
				$row=mysqli_fetch_array($result, MYSQLI_NUM);
				$maxresult=$row[0];
			}
			return $maxresult+1;
			
		}
		
		protected function getDB($datos,$tabla){
			
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "Los datos no se insertarán \n";
				
			}
			$this->existeTabla($db,$this ->tabla);
			$this->camposVacios($datos);
			return $db;
			
		}
		
		public function insertarDatos($datos){
				
			
			$db=$this->getDB($datos,$this ->tabla);
			
			
			if(!empty($this -> mensajes)) return;
		
			$consultaPre=$db->prepare("INSERT INTO " .$this ->tabla." VALUES(?,?,?,?)");
			$id=$this->nextId($this ->tabla);
			$consultaPre->bind_param('isss',$id,$datos["nombre"],$datos["ciudad"], $datos["empresa"]);
			$this->execute($db,$consultaPre,"agregada");
			
			$consultaPre->close();
			$db->close();
				
		}
		
		public function insertarDatosPelicula($datos){
				
			$db=$this->getDB($datos,$this ->tablaPeli);
			if(!empty($this -> mensajes)) return;
		
			$consultaPre=$db->prepare("INSERT INTO " .$this ->tablaPeli." VALUES(?,?,?,?)");
			$id=$this->nextId($this ->tablaPeli);
			$consultaPre->bind_param('isis',$id,$datos["titulo"],$datos["duracion"], $datos["genero"]);
			$this->execute($db,$consultaPre,"agregada");
			
			$consultaPre->close();
			$db->close();
				
		}
		
		public function insertarDatosSala($datos){
				
			$db=$this->getDB($datos,$this ->tablaSala);
			if(!empty($this -> mensajes)) return;
		
			$consultaPre=$db->prepare("INSERT INTO " .$this ->tablaSala." VALUES(?,?,?,?)");
			$id=$this->nextId($this ->tablaSala);
			$consultaPre->bind_param('iiii',$id,$datos["idCine"],$datos["numero"], $datos["capacidad"]);
			$this->execute($db,$consultaPre,"agregada");
			
			$consultaPre->close();
			$db->close();
				
		}
		
		public function insertarDatosSesion($datos){
				
			$db=$this->getDB($datos,$this ->tabla);
			if(!empty($this -> mensajes)) return;
		
			$consultaPre=$db->prepare("INSERT INTO " .$this ->tablaSesion." VALUES(?,?,?,?)");
			$id=$this->nextId($this ->tablaSesion);
			$consultaPre->bind_param('iiss',$datos["idSala"],$datos["idPelicula"],$datos["fecha"], $datos["hora"]);
			$this->execute($db,$consultaPre,"agregada");
			
			$consultaPre->close();
			$db->close();
				
		}
		
		
		public function modificarDatos($datos){
			$db=$this->getDB($datos,$this ->tabla);
	
			$this->findId($db,$datos,$this ->tabla);
			if(!empty($this -> mensajes)) return;
			
			$consultaPre=$db->prepare("UPDATE " .$this ->tabla." SET nombre=?,ciudad=?,empresa=? WHERE id=?");
			$consultaPre->bind_param('sssi',$datos["nombre"],$datos["ciudad"], $datos["empresa"],$datos["id"]);
			$this->execute($db,$consultaPre,"modificada");
			$consultaPre->close();
			$db->close();
		
		}
		
		public function modificarDatosPelicula($datos){
			$db=$this->getDB($datos,$this ->tablaPeli);
			
			$this->findId($db,$datos,$this ->tablaPeli);
				
			if(!empty($this -> mensajes)) return;
			
			$consultaPre=$db->prepare("UPDATE " .$this ->tablaPeli." SET titulo=?,duracion=?,genero=? WHERE id=?");
			$consultaPre->bind_param('sisi',$datos["titulo"],$datos["duracion"], $datos["genero"],$datos["id"]);
			$this->execute($db,$consultaPre,"modificada");
			$consultaPre->close();
			$db->close();
		
		}
		
		public function modificarDatosSala($datos){
			$db=$this->getDB($datos,$this ->tablaSala);
			
			$this->findId($db,$datos,$this ->tablaSala);
				
			
			if(!empty($this -> mensajes)) return;
			
			
			$consultaPre=$db->prepare("UPDATE " .$this ->tablaSala." SET numero=?,capacidad=? WHERE id=?");
			$consultaPre->bind_param('iii',$datos["numero"],$datos["capacidad"],$datos["id"]);
			$this->execute($db,$consultaPre,"modificada");
			$consultaPre->close();
			$db->close();
		
		}
		
		public function modificarDatosSesion($datos){
			$db=$this->getDB($datos,$this ->tablaSesion);
			
			$calcularTotal=$db->prepare("SELECT idSala FROM ".$this ->tablaSesion." WHERE idSala=?");
			$calcularTotal->bind_param('i',$datos["idSala"]);
			$calcularTotal->execute();
			$calcularTotal->store_result();
			$calcularTotal -> fetch();
			$total = $calcularTotal->num_rows;
			if($total==0){
				$this->mensajes.= "No se ha encontrado el ID \n";
			}
			
			$calcularTotal=$db->prepare("SELECT idPelicula FROM ".$this ->tablaSesion." WHERE idPelicula=?");
			$calcularTotal->bind_param('i',$datos["idPelicula"]);
			$calcularTotal->execute();
			$calcularTotal->store_result();
			$calcularTotal -> fetch();
			$total = $calcularTotal->num_rows;
			if($total==0){
				$this->mensajes.= "No se ha encontrado el ID \n";
			}
				
			
			if(!empty($this -> mensajes)) return;
			
			
			$consultaPre=$db->prepare("UPDATE " .$this ->tablaSesion." SET fecha=?,hora=? WHERE idSala=? and idPelicula=?");
			$consultaPre->bind_param('ssii',$datos["fecha"],$datos["hora"], $datos["idSala"],$datos["idPelicula"]);
			$this->execute($db,$consultaPre,"modificada");
			$consultaPre->close();
			$db->close();
		
		}
		
		public function borrarDatos($datos){
			$db=$this->getDB($datos,$this ->tabla);
			$this->findId($db,$datos,$this ->tabla);
			if(!empty($this -> mensajes)) return;
			
			$consultaPre=$db->prepare("DELETE FROM " .$this ->tabla." WHERE id=?");
			$consultaPre->bind_param('i',$datos["id"]);
			$this->execute($db,$consultaPre,"borrada");
			$consultaPre->close();
			$db->close();
		
		}
		
		public function borrarDatosPelicula($datos){
			$db=$this->getDB($datos,$this ->tablaPeli);
			$this->findId($db,$datos,$this ->tablaPeli);
			if(!empty($this -> mensajes)) return;
			
			$consultaPre=$db->prepare("DELETE FROM " .$this ->tablaPeli." WHERE id=?");
			$consultaPre->bind_param('i',$datos["id"]);
			$this->execute($db,$consultaPre,"borrada");
			$consultaPre->close();
			$db->close();
		
		}
		
		public function borrarDatosSala($datos){
			$db=$this->getDB($datos,$this ->tablaSala);
			$this->findId($db,$datos,$this ->tablaSala);
			if(!empty($this -> mensajes)) return;
			
			$consultaPre=$db->prepare("DELETE FROM " .$this ->tablaSala." WHERE id=?");
			$consultaPre->bind_param('i',$datos["id"]);
			$this->execute($db,$consultaPre,"borrada");
			$consultaPre->close();
			$db->close();
		
		}
		
		public function borrarDatosSesion($datos){
			$db=$this->getDB($datos,$this ->tablaSesion);
			$calcularTotal=$db->prepare("SELECT idSala FROM ".$this ->tablaSesion." WHERE idSala=?");
			$calcularTotal->bind_param('i',$datos["idSala"]);
			$calcularTotal->execute();
			$calcularTotal->store_result();
			$calcularTotal -> fetch();
			$total = $calcularTotal->num_rows;
			if($total==0){
				$this->mensajes.= "No se ha encontrado el ID \n";
			}
			
			$calcularTotal=$db->prepare("SELECT idPelicula FROM ".$this ->tablaSesion." WHERE idPelicula=?");
			$calcularTotal->bind_param('i',$datos["idPelicula"]);
			$calcularTotal->execute();
			$calcularTotal->store_result();
			$calcularTotal -> fetch();
			$total = $calcularTotal->num_rows;
			if($total==0){
				$this->mensajes.= "No se ha encontrado el ID \n";
			}
			
			if(!empty($this -> mensajes)) return;
			
			$consultaPre=$db->prepare("DELETE FROM " .$this ->tablaSesion." WHERE idSala=? and idPelicula=?");
			$consultaPre->bind_param('ii',$datos["idSala"],$datos["idPelicula"]);
			$this->execute($db,$consultaPre,"borrada");
			$consultaPre->close();
			$db->close();
		
		}

		
		public function importarCSV($fileName){
			if($fileName==null){
				$this->mensajes.="Ningun archivo seleccionado";
				return;
			}
			$db=$this->getDB(array("id"=>"p"),$this ->tabla);
			if(!empty($this -> mensajes)) return;
			$cadenaSQL="DELETE FROM ". $this ->tabla;
				if($db->query($cadenaSQL)===TRUE){
					$this->mensajes.="Tabla ". $this ->tabla ." reiniciada con éxito \n";
				}
				else{
					$this->mensajes.="ERROR en el reinicio de la tabla " .$this ->database.".Error:".$db->error."\n";
					return;
			}
			$file = fopen($fileName, "r");
			$column = fgetcsv($file, 10000, ",");
			while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
				$id = 0;
				if (isset($column[0])) {
					$id = mysqli_real_escape_string($db, $column[0]);
				}
			
				$nombre = "";
				if (isset($column[1])) {
					$nombre = mysqli_real_escape_string($db, $column[1]);
				}
				
				$ciudad = "";
				if (isset($column[2])) {
					$ciudad = mysqli_real_escape_string($db, $column[2]);
				}
				$empresa = "";
				if (isset($column[3])) {
					$empresa = mysqli_real_escape_string($db, $column[3]);
				}
				$consultaPre=$db->prepare("INSERT INTO " .$this ->tabla." VALUES(?,?,?,?)");
				$consultaPre->bind_param('isss',$id,$nombre,$ciudad,$empresa);
				$this->execute($db,$consultaPre,"agregada");
				
				$consultaPre->close();
			}
			$db->close();
		}
		
		public function importarCSVPelicula($fileName){
			if($fileName==null){
				$this->mensajes.="Ningun archivo seleccionado";
				return;
			}
			$db=$this->getDB(array("id"=>"p"),$this ->tablaPeli);
			if(!empty($this -> mensajes)) return;
			$cadenaSQL="DELETE FROM ". $this ->tablaPeli;
				if($db->query($cadenaSQL)===TRUE){
					$this->mensajes.="Tabla ". $this ->tablaPeli ." reiniciada con éxito \n";
				}
				else{
					$this->mensajes.="ERROR en el reinicio de la tabla " .$this ->database.".Error:".$db->error."\n";
					return;
			}
			$file = fopen($fileName, "r");
			$column = fgetcsv($file, 10000, ",");
			while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
				$id = 0;
				if (isset($column[0])) {
					$id = mysqli_real_escape_string($db, $column[0]);
				}
			
				$titulo = "";
				if (isset($column[1])) {
					$titulo = mysqli_real_escape_string($db, $column[1]);
				}
				
				$duracion = 0;
				if (isset($column[2])) {
					$duracion = mysqli_real_escape_string($db, $column[2]);
				}
				$genero = "";
				if (isset($column[3])) {
					$genero = mysqli_real_escape_string($db, $column[3]);
				}
				$consultaPre=$db->prepare("INSERT INTO " .$this ->tablaPeli." VALUES(?,?,?,?)");
				$consultaPre->bind_param('isis',$id,$titulo,$duracion,$genero);
				$this->execute($db,$consultaPre,"agregada");
				
				$consultaPre->close();
			}
			$db->close();
		}
		
		public function importarCSVSala($fileName){
			if($fileName==null){
				$this->mensajes.="Ningun archivo seleccionado";
				return;
			}
			$db=$this->getDB(array("id"=>"p"),$this ->tablaSala);
			if(!empty($this -> mensajes)) return;
			$cadenaSQL="DELETE FROM ". $this ->tablaSala;
				if($db->query($cadenaSQL)===TRUE){
					$this->mensajes.="Tabla ". $this ->tablaSala ." reiniciada con éxito \n";
				}
				else{
					$this->mensajes.="ERROR en el reinicio de la tabla " .$this ->database.".Error:".$db->error."\n";
					return;
			}
			$file = fopen($fileName, "r");
			$column = fgetcsv($file, 10000, ",");
			while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
				$id = 0;
				if (isset($column[0])) {
					$id = mysqli_real_escape_string($db, $column[0]);
				}
			
				$idCine = 0;
				if (isset($column[1])) {
					$idCine = mysqli_real_escape_string($db, $column[1]);
				}
				
				$numero = 0;
				if (isset($column[2])) {
					$numero = mysqli_real_escape_string($db, $column[2]);
				}
				$capacidad = 0;
				if (isset($column[3])) {
					$capacidad = mysqli_real_escape_string($db, $column[3]);
				}
				$consultaPre=$db->prepare("INSERT INTO " .$this ->tablaSala." VALUES(?,?,?,?)");
				$consultaPre->bind_param('iiii',$id,$idCine,$numero,$capacidad);
				$this->execute($db,$consultaPre,"agregada");
				
				$consultaPre->close();
			}
			$db->close();
		}
		
		public function importarCSVSesion($fileName){
			if($fileName==null){
				$this->mensajes.="Ningun archivo seleccionado";
				return;
			}
			$db=$this->getDB(array("id"=>"p"),$this ->tablaSesion);
			if(!empty($this -> mensajes)) return;
			$cadenaSQL="DELETE FROM ". $this ->tablaSesion;
				if($db->query($cadenaSQL)===TRUE){
					$this->mensajes.="Tabla ". $this ->tablaSesion ." reiniciada con éxito \n";
				}
				else{
					$this->mensajes.="ERROR en el reinicio de la tabla " .$this ->database.".Error:".$db->error."\n";
					return;
			}
			
			$file = fopen($fileName, "r");
			$column = fgetcsv($file, 10000, ",");
			while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
				$idSala = 0;
				if (isset($column[0])) {
					$idSala = mysqli_real_escape_string($db, $column[0]);
				}
			
				$idPelicula = 0;
				if (isset($column[1])) {
					$idPelicula = mysqli_real_escape_string($db, $column[1]);
				}
				
				$fecha = "";
				if (isset($column[2])) {
					$fecha = mysqli_real_escape_string($db, $column[2]);
				}
				$hora = "";
				if (isset($column[3])) {
					$hora = mysqli_real_escape_string($db, $column[3]);
				}
				$consultaPre=$db->prepare("INSERT INTO " .$this ->tablaSesion." VALUES(?,?,?,?)");
				$consultaPre->bind_param('iiss',$idSala,$idPelicula,$fecha,$hora);
				$this->execute($db,$consultaPre,"agregada");
				
				$consultaPre->close();
			}
			$db->close();
		}
		
		public function exportarCSV($tabla,$name){
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "La base de datos no se creará \n";
				return;
			}
			$this->existeTabla($db,$tabla);
			if(!empty($this -> mensajes)) return;
			$filename =$name.".csv";
			$result = $db->query('SELECT * FROM '.$tabla);
			
			$num_fields = mysqli_num_fields($result);
			$headers = array();
			for ($i = 0; $i < $num_fields; $i++) {
				$headers[] = $this-> mysqli_field_name($result , $i);
			}
			ob_end_clean();
			$fp = fopen('php://output', 'w');
			if ($fp && $result) {
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename='.$filename);
				header('Pragma: no-cache');
				header('Expires: 0');
				fputcsv($fp, $headers);
				while ($row = $result->fetch_array(MYSQLI_NUM)) {
					fputcsv($fp, array_values($row));
				}
				exit;
			}
				
				
	}
		
		
	
		protected function mysqli_field_name($result, $field_offset){
			$properties = mysqli_fetch_field_direct($result, $field_offset);
			return is_object($properties) ? $properties->name : null;
		}
	
	}
	
	class Formulario {
		protected $insertar;
		protected $modificar;
		protected $borrar;
		protected $insertarPelicula;
		protected $modificarPelicula;
		protected $borrarPelicula;
		protected $insertarSala;
		protected $modificarSala;
		protected $borrarSala;
		protected $insertarSesion;
		protected $modificarSesion;
		protected $borrarSesion;
		public function __construct(){
			$this->insertar=array("nombre"=>"","ciudad"=>"","empresa"=>"");
			$this->modificar=array("id"=>"","nombre"=>"","ciudad"=>"","empresa"=>"");
			$this->borrar=array("id"=>"");
			$this->insertarPelicula=array("titulo"=>"","duracion"=>"","genero"=>"");
			$this->modificarPelicula=array("id"=>"","titulo"=>"","duracion"=>"","genero"=>"");
			$this->borrarPelicula=array("id"=>"");
			$this->insertarSala=array("idCine"=>"","numero"=>"","capacidad"=>"");
			$this->modificarSala=array("id"=>"","numero"=>"","capacidad"=>"");
			$this->borrarSala=array("id"=>"");
			$this->insertarSesion=array("idSala"=>"","idPelicula"=>"","fecha"=>"","hora"=>"");
			$this->modificarSesion=array("idSala"=>"","idPelicula"=>"","fecha"=>"","hora"=>"");
			$this->borrarSesion=array("idSala"=>"","idPelicula"=>"");
			
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
		
		
		
		public function setInsertarPelicula($array){
			$this -> insertarPelicula=$array;
		}
		public function getInsertarPelicula(){
			return $this -> insertarPelicula;
		}
		public function setModificarPelicula($array){
			$this -> modificarPelicula=$array;
		}
		public function getModificarPelicula(){
			return $this -> modificarPelicula;
		}
		public function setBorrarPelicula($array){
			$this -> borrarPelicula=$array;
		}
		public function getBorrarPelicula(){
			return $this -> borrarPelicula;
		}
		
		
		
		public function setInsertarSala($array){
			$this -> insertarSala=$array;
		}
		public function getInsertarSala(){
			return $this -> insertarSala;
		}
		public function setModificarSala($array){
			$this -> modificarSala=$array;
		}
		public function getModificarSala(){
			return $this -> modificarSala;
		}
		public function setBorrarSala($array){
			$this -> borrarSala=$array;
		}
		public function getBorrarSala(){
			return $this -> borrarSala;
		}
		
		
		
		public function setInsertarSesion($array){
			$this -> insertarSesion=$array;
		}
		public function getInsertarSesion(){
			return $this -> insertarSesion;
		}
		public function setModificarSesion($array){
			$this -> modificarSesion=$array;
		}
		public function getModificarSesion(){
			return $this -> modificarSesion;
		}
		public function setBorrarSesion($array){
			$this -> borrarSesion=$array;
		}
		public function getBorrarSesion(){
			return $this -> borrarSesion;
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
	 
	 $insertarPelicula = $formulario -> getInsertarPelicula();
	 $modificarPelicula = $formulario -> getModificarPelicula();
	 $borrarPelicula = $formulario -> getBorrarPelicula();
	 
	 $insertarSala = $formulario -> getInsertarSala();
	 $modificarSala = $formulario -> getModificarSala();
	 $borrarSala = $formulario -> getBorrarSala();
	 
	 $insertarSesion = $formulario -> getInsertarSesion();
	 $modificarSesion = $formulario -> getModificarSesion();
	 $borrarSesion = $formulario -> getBorrarSesion();
	 

	 if (count($_POST)>0) 
    {  	
		
		if(isset($_POST['btCrearBase'])) $base->crearBase();
		if(isset($_POST['btCrearTabla'])) $base->crearTabla();
		if(isset($_POST['btInsertar'])){ 
			$datos=array("nombre"=>$_POST["nombre"],"ciudad"=>$_POST["ciudad"],"empresa"=>$_POST["empresa"]);
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
		
		if(isset($_POST['btExpCsv'])){ 
			
			$base->exportarCSV("Cine","Cine");
			
		}
		
		
		
		
		
		if(isset($_POST['btInsertarPelicula'])){ 
			$datos=array("titulo"=>$_POST["titulo"],"duracion"=>$_POST["duracion"],"genero"=>$_POST["genero"]);
			$base->insertarDatosPelicula($datos);
			$formulario -> setInsertarPelicula($datos);
			
			}
		if(isset($_POST['btModificarPelicula'])){ 
			$datos=array("id"=>$_POST["idPelicula"],"titulo"=>$_POST["titulo"],"duracion"=>$_POST["duracion"],"genero"=>$_POST["genero"]);
			$base->ModificarDatosPelicula($datos);
			$formulario -> setModificarPelicula($datos);
			
		}
			
		if(isset($_POST['btBorrarPelicula'])){ 
			$datos=array("id"=>$_POST["idPelicula"]);
			$base->borrarDatosPelicula($datos);
			$formulario -> setBorrarPelicula($datos);
		}	
		
		
		
		
		if(isset($_POST['btImpCsvPelicula'])){ 
			
			$fileName = $_FILES["filePelicula"]["tmp_name"];
			$base->importarCSVPelicula($fileName);
			
		}
		
		if(isset($_POST['btExpCsvPelicula'])){ 
			
			$base->exportarCSV("Pelicula","Pelicula");
			
		}
		
		

		if(isset($_POST['btInsertarSala'])){ 
			$datos=array("idCine"=>$_POST["idCine"],"numero"=>$_POST["numero"],"capacidad"=>$_POST["capacidad"]);
			$base->insertarDatosSala($datos);
			$formulario -> setInsertarSala($datos);
			
			}
		if(isset($_POST['btModificarSala'])){ 
			$datos=array("id"=>$_POST["idSala"],"numero"=>$_POST["numero"],"capacidad"=>$_POST["capacidad"]);
			$base->ModificarDatosSala($datos);
			$formulario -> setModificarSala($datos);
			
		}
			
		if(isset($_POST['btBorrarSala'])){ 
			$datos=array("id"=>$_POST["idSala"]);
			$base->borrarDatosSala($datos);
			$formulario -> setBorrarSala($datos);
		}	
		
		
		
		
		if(isset($_POST['btImpCsvSala'])){ 
			
			$fileName = $_FILES["fileSala"]["tmp_name"];
			$base->importarCSVSala($fileName);
			
		}
		
		if(isset($_POST['btExpCsvSala'])){ 
			
			$base->exportarCSV("Sala", "Sala");
			
		}
		
		
		
		if(isset($_POST['btInsertarSesion'])){ 
			$datos=array("idSala"=>$_POST["idSala"],"idPelicula"=>$_POST["idPelicula"],"fecha"=>$_POST["fecha"],"hora"=>$_POST["hora"]);
			$base->insertarDatosSesion($datos);
			$formulario -> setInsertarSesion($datos);
			
			}
		if(isset($_POST['btModificarSesion'])){ 
			$datos=array("idSala"=>$_POST["idSala"],"idPelicula"=>$_POST["idPelicula"],"fecha"=>$_POST["fecha"],"hora"=>$_POST["hora"]);
			$base->ModificarDatosSesion($datos);
			$formulario -> setModificarSesion($datos);
			
		}
			
		if(isset($_POST['btBorrarSesion'])){ 
			$datos=array("idSala"=>$_POST["idSala"],"idPelicula"=>$_POST["idPelicula"]);
			$base->borrarDatosSesion($datos);
			$formulario -> setBorrarSesion($datos);
		}	
		
		
		
		
		if(isset($_POST['btImpCsvSesion'])){ 
			
			$fileName = $_FILES["fileSesion"]["tmp_name"];
			$base->importarCSVSesion($fileName);
			
		}
		
		if(isset($_POST['btExpCsvSesion'])){ 
			
			$base->exportarCSV("Sesion","Sesion");
			
		}
		
		$mensajes=$base->getMensajes();
		
		
		$insertar = $formulario -> getInsertar();
		$modificar = $formulario -> getModificar();
		$borrar = $formulario -> getBorrar();
		
		$insertarPelicula = $formulario -> getInsertarPelicula();
		$modificarPelicula = $formulario -> getModificarPelicula();
		$borrarPelicula = $formulario -> getBorrarPelicula();
		
		$insertarSala = $formulario -> getInsertarSala();
		$modificarSala = $formulario -> getModificarSala();
		$borrarSala = $formulario -> getBorrarSala();
		
		$insertarSesion = $formulario -> getInsertarSesion();
		$modificarSesion = $formulario -> getModificarSesion();
		$borrarSesion = $formulario -> getBorrarSesion();
		$_SESSION['formulario'] = $formulario;
		
    
    }


	echo " 
	
	<h3>Mensajes</h3>
	<textarea >$mensajes</textarea>
		
	<h3>Operaciones generales</h3>
	
	<form action='#' method='post' name='generales'>
		<input type='submit'  value='Crear base' name='btCrearBase' >
		<input type='submit'  value='Crear tablas' name='btCrearTabla' >
	</form>
	<h3>Insertar Datos</h3>
	<h4>Cine</h4>
	<form action='#' method='post' name='insercion'>
		<label>Nombre: <input type='text' name='nombre' value='$insertar[nombre]'></label>
		<label>Ciudad: <input type='text' name='ciudad' value='$insertar[ciudad]'></label>
		<label>Empresa: <input type='text' name='empresa' value='$insertar[empresa]'></label>
		
		<input type='submit' value='Insertar Datos' name='btInsertar' >
	</form>
	
	<h4>Pelicula</h4>
	<form action='#' method='post' name='insercionPelicula'>
		<label>Titulo: <input type='text' name='titulo' value='$insertarPelicula[titulo]'></label>
		<label>Duracion(min): <input type='text' name='duracion' value='$insertarPelicula[duracion]'></label>
		<label>Genero: <input type='text' name='genero' value='$insertarPelicula[genero]'></label>
		
		<input type='submit' value='Insertar Datos' name='btInsertarPelicula' >
	</form>
	
	<h4>Sala</h4>
	<form action='#' method='post' name='insercionSala'>
		<label>idCine: <input type='text' name='idCine' value='$insertarSala[idCine]'></label>
		<label>Numero: <input type='text' name='numero' value='$insertarSala[numero]'></label>
		<label>Capacidad: <input type='text' name='capacidad' value='$insertarSala[capacidad]'></label>
		
		<input type='submit' value='Insertar Datos' name='btInsertarSala' >
	</form>
	
	<h4>Sesion</h4>
	<form action='#' method='post' name='insercionSesion'>
		<label>idSala: <input type='text' name='idSala' value='$insertarSesion[idSala]'></label>
		<label>idPelicula: <input type='text' name='idPelicula' value='$insertarSesion[idPelicula]'></label>
		<label>Fecha: <input type='date' name='fecha' value='$insertarSesion[fecha]'></label>
		<label>Hora: <input type='text' list='horas' name='hora' value='$insertarSesion[hora]'></label>
		<datalist id='horas'>
		  <option>00:00</option><option>00:30</option><option>01:00</option><option>01:30</option><option>02:00</option><option>02:30</option>
		  <option>03:00</option><option>03:30</option><option>04:00</option><option>04:30</option><option>05:00</option><option>05:30</option>
		  <option>06:00</option><option>06:30</option><option>07:00</option><option>07:30</option><option>08:00</option><option>08:30</option>
		  <option>09:00</option><option>09:30</option><option>10:00</option><option>10:30</option><option>11:00</option><option>11:30</option>
		  <option>12:00</option><option>12:30</option><option>13:00</option><option>13:30</option><option>14:00</option><option>14:30</option>
		  <option>15:00</option><option>15:30</option><option>16:00</option><option>16:30</option><option>17:00</option><option>17:30</option>
		  <option>18:00</option><option>18:30</option><option>19:00</option><option>19:30</option><option>20:00</option><option>20:30</option>
		  <option>21:00</option><option>21:30</option><option>22:00</option><option>22:30</option><option>23:00</option><option>23:30</option>
		  
		  
		  
	
		</datalist>
		
		<input type='submit' value='Insertar Datos' name='btInsertarSesion' >
	</form>
	
	<h3>Borrar Datos</h3>
	<h4>Cine</h4>
	<p>Borrar el campo con el id = ?</p>
	<form action='#' method='post' name='borrado'>
		<label>Id: <input type='text' name='id' value='$borrar[id]'></label>
		<input type='submit' value='Borrar Datos' name='btBorrar' >
	</form>
	
	<h4>Pelicula</h4>
	<p>Borrar el campo con el id = ?</p>
	<form action='#' method='post' name='borradoPelicula'>
		<label>Id: <input type='text' name='idPelicula' value='$borrarPelicula[id]'></label>
		<input type='submit' value='Borrar Datos' name='btBorrarPelicula' >
	</form>
	
	<h4>Sala</h4>
	<p>Borrar el campo con el id = ?</p>
	<form action='#' method='post' name='borradoSala'>
		<label>Id: <input type='text' name='idSala' value='$borrarSala[id]'></label>
		<input type='submit' value='Borrar Datos' name='btBorrarSala' >
	</form>
	
	<h4>Sesion</h4>
	<p>Borrar el campo con el idSala = ? y el idPelicula=?</p>
	<form action='#' method='post' name='borradoSesion'>
		<label>IdSala: <input type='text' name='idSala' value='$borrarSesion[idSala]'></label>
		<label>IdPelicula: <input type='text' name='idPelicula' value='$borrarSesion[idPelicula]'></label>
		<input type='submit' value='Borrar Datos' name='btBorrarSesion' >
	</form>
	
	<h3>Modificar Datos</h3>
	<h4>Cine</h4>
	<p>Modifica el campo con el id = ?</p>
	<form action='#' method='post' name='modificacion'>
		<label>Id: <input type='text' name='id' value='$modificar[id]'></label>
		<label>Nombre: <input type='text' name='nombre' value='$modificar[nombre]'></label>
		<label>Ciudad: <input type='text' name='ciudad' value='$modificar[ciudad]'></label>
		<label>Empresa: <input type='text' name='empresa' value='$modificar[empresa]'></label>
		<input type='submit' value='Modificar Datos' name='btModificar' >
	</form>
	
	<h4>Pelicula</h4>
	<p>Modifica el campo con el id = ?</p>
	<form action='#' method='post' name='modificacionPelicula'>
		<label>Id: <input type='text' name='idPelicula' value='$modificarPelicula[id]'></label>
		<label>Titulo: <input type='text' name='titulo' value='$modificarPelicula[titulo]'></label>
		<label>Duracion(min): <input type='text' name='duracion' value='$modificarPelicula[duracion]'></label>
		<label>Genero: <input type='text' name='genero' value='$modificarPelicula[genero]'></label>
		
		<input type='submit' value='Modificar Datos' name='btModificarPelicula' >
	</form>
	
	<h4>Sala</h4>
	<p>Modifica el campo con el id = ?</p>
	<form action='#' method='post' name='modificacionSala'>
		<label>Id: <input type='text' name='idSala' value='$modificarSala[id]'></label>
		<label>Numero: <input type='text' name='numero' value='$modificarSala[numero]'></label>
		<label>Capacidad: <input type='text' name='capacidad' value='$modificarSala[capacidad]'></label>
		
		<input type='submit' value='Modificar Datos' name='btModificarSala' >
	</form>
	
	<h4>Sesion</h4>
	<p>Modifica el campo con el idSala = ? y el idPelicula=?</p>
	<form action='#' method='post' name='modificacionSesion'>
		<label>idSala: <input type='text' name='idSala' value='$modificarSesion[idSala]'></label>
		<label>idPelicula: <input type='text' name='idPelicula' value='$modificarSesion[idPelicula]'></label>
		<label>Fecha: <input type='date' name='fecha' value='$modificarSesion[fecha]'></label>
		<label>Hora: <input type='text' list='horas2' name='hora' value='$modificarSesion[hora]'></label>
		<datalist id='horas2'>
		  <option>00:00</option><option>00:30</option><option>01:00</option><option>01:30</option><option>02:00</option><option>02:30</option>
		  <option>03:00</option><option>03:30</option><option>04:00</option><option>04:30</option><option>05:00</option><option>05:30</option>
		  <option>06:00</option><option>06:30</option><option>07:00</option><option>07:30</option><option>08:00</option><option>08:30</option>
		  <option>09:00</option><option>09:30</option><option>10:00</option><option>10:30</option><option>11:00</option><option>11:30</option>
		  <option>12:00</option><option>12:30</option><option>13:00</option><option>13:30</option><option>14:00</option><option>14:30</option>
		  <option>15:00</option><option>15:30</option><option>16:00</option><option>16:30</option><option>17:00</option><option>17:30</option>
		  <option>18:00</option><option>18:30</option><option>19:00</option><option>19:30</option><option>20:00</option><option>20:30</option>
		  <option>21:00</option><option>21:30</option><option>22:00</option><option>22:30</option><option>23:00</option><option>23:30</option>
		  
		  
		  
	
		</datalist>
		
		<input type='submit' value='Modificar Datos' name='btModificarSesion' >
	</form>
	
	
	
	
	<h3>Importar de csv</h3>
	<h4>Cine</h4>
	<form action='#' method='post' name='importar' enctype='multipart/form-data'>
		<label>Elige un archivo csv<input type='file' name='file' accept='.csv'></label>
		<input type='submit' value='Importar' name='btImpCsv' >
		
	</form>
	
	<h4>Pelicula</h4>
	<form action='#' method='post' name='importarPelicula' enctype='multipart/form-data'>
		<label>Elige un archivo csv<input type='file' name='filePelicula' accept='.csv'></label>
		<input type='submit' value='Importar' name='btImpCsvPelicula' >
		
	</form>
	
	<h4>Sala</h4>
	<form action='#' method='post' name='importarSala' enctype='multipart/form-data'>
		<label>Elige un archivo csv<input type='file' name='fileSala' accept='.csv'></label>
		<input type='submit' value='Importar' name='btImpCsvSala' >
		
	</form>
	
	<h4>Sesion</h4>
	<form action='#' method='post' name='importarSesion' enctype='multipart/form-data'>
		<label>Elige un archivo csv<input type='file' name='fileSesion' accept='.csv'></label>
		<input type='submit' value='Importar' name='btImpCsvSesion' >
		
	</form>
	
	<h3>Exportar a csv</h3>
	<h4>Cine</h4>
	<form action='#' method='post' name='exportar'>
		<input type='submit' value='Exportar' name='btExpCsv' >
	</form>
	
	<h4>Pelicula</h4>
	<form action='#' method='post' name='exportarPelicula'>
		<input type='submit' value='Exportar' name='btExpCsvPelicula' >
	</form>
	
	<h4>Sala</h4>
	<form action='#' method='post' name='exportarSala'>
		<input type='submit' value='Exportar' name='btExpCsvSala' >
	</form>
	
	<h4>Sesion</h4>
	<form action='#' method='post' name='exportarSesion'>
		<input type='submit' value='Exportar' name='btExpCsvSesion' >
	</form>
	
	
	
	

	
	";
?>
</main>



</body>
</html>