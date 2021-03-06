<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<title>BaseDatos</title>
<link rel="stylesheet" href="Ejercicio6.css"/>
<meta name="viewport" content="width=device-width, initial-scale=1">


</head>
<body>

 <h1/>Base de Datos</h1>

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
			$this ->database="ejercicio6";
			$this ->tabla="PruebasUsabilidad";
			

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
		
		protected function findDNI($db,$datos){
			$calcularTotal=$db->prepare("SELECT dni FROM ".$this ->tabla." WHERE dni=?");
			$calcularTotal->bind_param('s',$datos["dni"]);
			$calcularTotal->execute();
			$calcularTotal->store_result();
			$calcularTotal -> fetch();
			$total = $calcularTotal->num_rows;
			if($total==0){
				$this->mensajes.= "No se ha encontrado el DNI \n";
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
			
			$crearTabla="CREATE TABLE IF NOT EXISTS ". $this ->tabla." (dni VARCHAR(9) NOT NULL, nombre VARCHAR(255) NOT NULL, 
						apellidos VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telefono VARCHAR(9) NOT NULL, 
						edad INT NOT NULL CHECK(edad>0), sexo VARCHAR(10) NOT NULL CHECK(sexo='masculino' OR sexo='femenino'),
						pericia INT NOT NULL CHECK(pericia>=0 AND pericia<=10), 
						tiempo INT NOT NULL CHECK(tiempo>0), realizada VARCHAR(2) NOT NULL CHECK(realizada='no' OR realizada='si'),
						comentarios VARCHAR(510) NOT NULL, propuestas VARCHAR(510) NOT NULL, valoracion INT NOT NULL CHECK(valoracion>=0 AND valoracion<=10), 
						PRIMARY KEY(dni))";
			if($db->query($crearTabla)===TRUE){
				$this->mensajes.="Tabla ".$this ->tabla." creada con éxito";
			}
			else{
				$this->mensajes.="ERROR en la creación de la tabla persona. Error:".$db->error."";
				return;
			}
			$db->close();
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
		
			$consultaPre=$db->prepare("INSERT INTO " .$this ->tabla." VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
			$consultaPre->bind_param('sssssisiisssi',$datos["dni"],$datos["nombre"],$datos["apellidos"],$datos["email"], $datos["telefono"],
						  $datos["edad"],$datos["sexo"],$datos["pericia"],$datos["tiempo"],$datos["realizada"],$datos["comentarios"],
						  $datos["propuestas"],$datos["valoracion"]);
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
			
			$this->findDNI($db,$datos);
				
			
			if(!empty($this -> mensajes)) return;
			
			
			$consultaPre=$db->prepare("UPDATE " .$this ->tabla." SET nombre=?,apellidos=?,email=?,telefono=?,edad=?,sexo=?,pericia=?,
						tiempo=?,realizada=?,comentarios=?,propuestas=?,valoracion=? WHERE dni=?");
			$consultaPre->bind_param('ssssisiisssis',$datos["nombre"],$datos["apellidos"],$datos["email"], $datos["telefono"],
						  $datos["edad"],$datos["sexo"],$datos["pericia"],$datos["tiempo"],$datos["realizada"],$datos["comentarios"],
						  $datos["propuestas"],$datos["valoracion"],$datos["dni"]);
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
			
			$consultaPre=$db->prepare("DELETE FROM " .$this ->tabla." WHERE dni=?");
			$consultaPre->bind_param('s',$datos["dni"]);
			$this->execute($db,$consultaPre,"borrada");
			$consultaPre->close();
			$db->close();
		
		}
		
		public function buscarDatos($datos){
			
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "Los datos no se pueden buscar \n";
				return;
			}
			$this->existeTabla($db);
			$this->camposVacios($datos);
			$this->findDNI($db,$datos);
			if(!empty($this -> mensajes)) return;
			
			$consultaPre=$db->prepare("SELECT * FROM " .$this ->tabla." WHERE dni=?");
			$consultaPre->bind_param('s',$datos["dni"]);
			$this->execute($db,$consultaPre,"encontrada");
			$resultado=$consultaPre->get_result();
			if($resultado->fetch_assoc()!=NULL){
				$resultado->data_seek(0);
				while($fila=$resultado->fetch_assoc()){
					$this->mensajes.="Nombre=".$fila["nombre"].". Apellidos=".$fila["apellidos"].". Email=".$fila["email"].". Telefono=".$fila["telefono"].
									". Edad=".$fila["edad"].". Sexo=".$fila["sexo"].". Pericia=".$fila["pericia"].". Tiempo=".$fila["tiempo"].
									". Realizada=".$fila["realizada"].". Comentarios=".$fila["comentarios"].". Propuestas=".$fila["propuestas"].
									". Valoracion=".$fila["valoracion"];
				}
			}
			else{
				$this->mensajes.="Sin resultados";
			}
			
			$consultaPre->close();
			$db->close();
		
		}
		
		public function generarInforme(){
			$db=null;
			try{
				$db=$this->getConexion(); 
			}
			catch(ErrorException $e){
				$this->mensajes.= "Error en la conexión \n";
			}
			
			if($db===null){
				$this->mensajes.= "N se puede generar el informe \n";
				$informe=array();
				$informe["edadMedia"]="NA";
				$informe["psexo"]="NA";
				$informe["mediaPericia"]="NA";
				$informe["mediaTiempo"]="NA";
				$informe["pcorrecto"]="NA";
				$informe["mediaValoracion"]="NA";
				return $informe;
			}
			if(!empty($this -> mensajes)) return;
			$informe=array();
			$total=$db->query("SELECT COUNT(*) as total FROM ".$this ->tabla)->fetch_assoc()["total"];
			if($total==0){
				$this->mensajes.= "N se puede generar el informe, no hay datos en la tabla \n";
				$informe=array();
				$informe["edadMedia"]="NA";
				$informe["psexo"]="NA";
				$informe["mediaPericia"]="NA";
				$informe["mediaTiempo"]="NA";
				$informe["pcorrecto"]="NA";
				$informe["mediaValoracion"]="NA";
				return $informe;
			}
			
			$informe["edadMedia"]=round($db->query("SELECT AVG(edad) as mediaedad FROM ".$this ->tabla)->fetch_assoc()["mediaedad"],2);
			$masculino=$db->query("SELECT COUNT(*) as masculino FROM ".$this ->tabla." WHERE sexo='masculino'")->fetch_assoc()["masculino"];
			$femenino=$db->query("SELECT COUNT(*) as femenino FROM ".$this ->tabla." WHERE sexo='femenino'")->fetch_assoc()["femenino"];
			$informe["psexo"]="M->".round(($masculino/$total*100),2)."%, F->".round(($femenino/$total*100),2)."%";
			$informe["mediaPericia"]=round($db->query("SELECT AVG(pericia) as mediapericia FROM ".$this ->tabla)->fetch_assoc()["mediapericia"],2);
			$informe["mediaTiempo"]=round($db->query("SELECT AVG(tiempo) as mediatiempo FROM ".$this ->tabla)->fetch_assoc()["mediatiempo"],2);
			$correcto=$db->query("SELECT COUNT(*) as correcto FROM ".$this ->tabla." WHERE realizada='si'")->fetch_assoc()["correcto"];
			$informe["pcorrecto"]=round(($correcto/$total*100),2)."%";
			$informe["mediaValoracion"]=round($db->query("SELECT AVG(valoracion) as mediavaloracion FROM ".$this ->tabla)->fetch_assoc()["mediavaloracion"],2);
			
			return $informe;
			
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
				$dni = "";
				if (isset($column[0])) {
					$dni = mysqli_real_escape_string($db, $column[0]);
				}
			
				$nombre = "";
				if (isset($column[1])) {
					$nombre = mysqli_real_escape_string($db, $column[1]);
				}
				$apellidos = "";
				if (isset($column[2])) {
					$apellidos = mysqli_real_escape_string($db, $column[2]);
				}
				$email = "";
				if (isset($column[3])) {
					$email = mysqli_real_escape_string($db, $column[3]);
				}
				$telefono = "";
				if (isset($column[4])) {
					$telefono = mysqli_real_escape_string($db, $column[4]);
				}
				$edad = "";
				if (isset($column[5])) {
					$edad = mysqli_real_escape_string($db, $column[5]);
				}
				$sexo = "";
				if (isset($column[6])) {
					$sexo = mysqli_real_escape_string($db, $column[6]);
				}
				$pericia = "";
				if (isset($column[7])) {
					$pericia = mysqli_real_escape_string($db, $column[7]);
				}
				$tiempo = "";
				if (isset($column[8])) {
					$tiempo = mysqli_real_escape_string($db, $column[8]);
				}
				$realizada = "";
				if (isset($column[9])) {
					$realizada = mysqli_real_escape_string($db, $column[9]);
				}$comentarios = "";
				if (isset($column[10])) {
					$comentarios = mysqli_real_escape_string($db, $column[10]);
				}
				$propuestas = "";
				if (isset($column[11])) {
					$propuestas = mysqli_real_escape_string($db, $column[11]);
				}$valoracion = "";
				if (isset($column[12])) {
					$valoracion = mysqli_real_escape_string($db, $column[12]);
				}
				$consultaPre=$db->prepare("INSERT INTO " .$this ->tabla." VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
				$consultaPre->bind_param('sssssisiisssi',$dni,$nombre,$apellidos,$email, $telefono,
							  $edad,$sexo,$pericia,$tiempo,$realizada,$comentarios,
							  $propuestas,$valoracion);
				$this->execute($db,$consultaPre,"agregada");
				
				$consultaPre->close();
			}
			$db->close();
		}
		
		public function exportarCSV(){
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
			$this->existeTabla($db);
			if(!empty($this -> mensajes)) return;
			$filename ="tabla.csv";
			$result = $db->query('SELECT * FROM '.$this ->tabla);
			
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
		protected $buscar;
		public function __construct(){
			$this->insertar=array("dni"=>"","nombre"=>"","apellidos"=>"","email"=>"","telefono"=>"","edad"=>"",
							"sexo"=>"","pericia"=>"", "tiempo"=>"","realizada"=>"","comentarios"=>"",
							"propuestas"=>"","valoracion"=>"");
			$this->modificar=array("dni"=>"","nombre"=>"","apellidos"=>"","email"=>"","telefono"=>"","edad"=>"",
							"sexo"=>"","pericia"=>"", "tiempo"=>"","realizada"=>"","comentarios"=>"",
							"propuestas"=>"","valoracion"=>"");
			$this->borrar=array("dni"=>"");
			$this->buscar=array("dni"=>"");
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
		public function setBuscar($array){
			$this -> buscar=$array;
		}
		public function getBuscar(){
			return $this -> buscar;
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
	 $buscar = $formulario -> getBuscar();
	 $informe=array("edadMedia"=>"","psexo"=>"","mediaPericia"=>"","mediaTiempo"=>"","pcorrecto"=>"","mediaValoracion"=>"");

	 if (count($_POST)>0) 
    {  	
		
		if(isset($_POST['btCrearBase'])) $base->crearBase();
		if(isset($_POST['btCrearTabla'])) $base->crearTabla();
		if(isset($_POST['btInsertar'])){ 
			$datos=array("dni"=>$_POST["dni"],"nombre"=>$_POST["nombre"],"apellidos"=>$_POST["apellidos"],"email"=>$_POST["email"],
				  "telefono"=>$_POST["telefono"],"edad"=>$_POST["edad"],"sexo"=>$_POST["sexo"],"pericia"=>$_POST["pericia"],
				  "tiempo"=>$_POST["tiempo"],"realizada"=>$_POST["realizada"],"comentarios"=>$_POST["comentarios"],
				  "propuestas"=>$_POST["propuestas"],"valoracion"=>$_POST["valoracion"]);
			$base->insertarDatos($datos);
			$formulario -> setInsertar($datos);
			
			}
		if(isset($_POST['btModificar'])){ 
			$datos=array("dni"=>$_POST["dni"],"nombre"=>$_POST["nombre"],"apellidos"=>$_POST["apellidos"],"email"=>$_POST["email"],
				  "telefono"=>$_POST["telefono"],"edad"=>$_POST["edad"],"sexo"=>$_POST["sexo"],"pericia"=>$_POST["pericia"],
				  "tiempo"=>$_POST["tiempo"],"realizada"=>$_POST["realizada"],"comentarios"=>$_POST["comentarios"],
				  "propuestas"=>$_POST["propuestas"],"valoracion"=>$_POST["valoracion"]);
			$base->ModificarDatos($datos);
			$formulario -> setModificar($datos);
			
		}
			
		if(isset($_POST['btBorrar'])){ 
			$datos=array("dni"=>$_POST["dni"]);
			$base->borrarDatos($datos);
			$formulario -> setBorrar($datos);
		}	
		
		if(isset($_POST['btBuscar'])){ 
			$datos=array("dni"=>$_POST["dni"]);
			$base->buscarDatos($datos);
			$formulario -> setBuscar($datos);
		}	
		
		if(isset($_POST['btInforme'])){ 
			
			$informe=$base->generarInforme();
			
		}
		
		if(isset($_POST['btExpCsv'])){ 
			
			$base->exportarCSV();
			
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
	<p>Operaciones generales de la base de datos</p>
	<form action='#' method='post' name='generales'>
		<input type='submit'  value='Crear base' name='btCrearBase' >
		<input type='submit'  value='Crear tabla' name='btCrearTabla' >
	</form>
	<h3>Insertar Datos</h3>
	<p>Insertar una persona en la base de datos</p>
	<form action='#' method='post' name='insercion'>
		<label for='campo1'>DNI:</label>
		<input id='campo1' type='text' name='dni' value='$insertar[dni]'>
		<label for='campo2'>Nombre:</label>
		<input id='campo2' type='text' name='nombre' value='$insertar[nombre]'>
		<label for='campo3'>Apellidos:</label>
	    <input id='campo3' type='text' name='apellidos' value='$insertar[apellidos]'>
		<label for='campo4'>Email:</label>
		<input id='campo4' type='text' name='email' value='$insertar[email]'>
		<label for='campo5'>Telefono:</label>
	    <input id='campo5' type='text' name='telefono' value='$insertar[telefono]'>
		<label for='campo6'>Edad (>0):</label>
		<input id='campo6' type='text' name='edad' value='$insertar[edad]'>
		<label for='campo7'>Sexo(masculino/femenino):</label>
		<input id='campo7' type='text' name='sexo' value='$insertar[sexo]'>
		<label for='campo8'>Pericia informática:</label>
		<input id='campo8' type='text' name='pericia' value='$insertar[pericia]'>
		<label for='campo9'>Tiempo(seg)(>0):</label>
		<input id='campo9' type='text' name='tiempo' value='$insertar[tiempo]'>
		<label for='campo10'>Tarea realizada (si/no):</label>
		<input id='campo10' type='text' name='realizada' value='$insertar[realizada]'>
		<label for='campo11'>Comentarios:</label>
		<input id='campo11' type='text' name='comentarios' value='$insertar[comentarios]'>
		<label for='campo12'>Propuestas:</label>
		<input id='campo12' type='text' name='propuestas' value='$insertar[propuestas]'>
		<label for='campo13'>Valoracion (0-10):</label>
		<input id='campo13' type='text' name='valoracion' value='$insertar[valoracion]'>
		<input type='submit' value='Insertar Datos' name='btInsertar' >
	</form>
	<h3>Buscar Datos</h3>
	<p>Busca el campo con el dni = ?</p>
	<form action='#' method='post' name='busqueda'>
		<label for='campo14'>DNI:</label>
		<input id='campo14' type='text' name='dni' value='$buscar[dni]'>
		<input type='submit' value='Buscar Datos' name='btBuscar' >
	</form>
	
	<h3>Borrar Datos</h3>
	<p>Borrar el campo con el dni = ?</p>
	<form action='#' method='post' name='borrado'>
		<label for='campo15'>DNI:</label>
		<input id='campo15' type='text' name='dni' value='$borrar[dni]'>
		<input type='submit' value='Borrar Datos' name='btBorrar' >
	</form>
	
	<h3>Modificar Datos</h3>
	<p>Modifica el campo con el dni = ?</p>
	<form action='#' method='post' name='modificion'>
		<label for='campo16'>DNI:</label>
		<input id='campo16' type='text' name='dni' value='$modificar[dni]'>
		<label for='campo17'>Nombre:</label>
		<input id='campo17' type='text' name='nombre' value='$modificar[nombre]'>
		<label for='campo18'>Apellidos:</label>
	    <input id='campo18' type='text' name='apellidos' value='$modificar[apellidos]'>
		<label for='campo19'>Email:</label>
		<input id='campo19' type='text' name='email' value='$modificar[email]'>
		<label for='campo20'>Telefono:</label>
	    <input id='campo20' type='text' name='telefono' value='$modificar[telefono]'>
		<label for='campo21'>Edad (>0):</label>
		<input id='campo21' type='text' name='edad' value='$modificar[edad]'>
		<label for='campo22'>Sexo(masculino/femenino):</label>
		<input id='campo22' type='text' name='sexo' value='$modificar[sexo]'>
		<label for='campo23'>Pericia informática:</label>
		<input id='campo23' type='text' name='pericia' value='$modificar[pericia]'>
		<label for='campo24'>Tiempo(seg)(>0):</label>
		<input id='campo24' type='text' name='tiempo' value='$modificar[tiempo]'>
		<label for='campo25'>Tarea realizada (si/no):</label>
		<input id='campo25' type='text' name='realizada' value='$modificar[realizada]'>
		<label for='campo26'>Comentarios:</label>
		<input id='campo26' type='text' name='comentarios' value='$modificar[comentarios]'>
		<label for='campo27'>Propuestas:</label>
		<input id='campo27' type='text' name='propuestas' value='$modificar[propuestas]'>
		<label for='campo28'>Valoracion (0-10):</label>
		<input id='campo28' type='text' name='valoracion' value='$modificar[valoracion]'>
		<input type='submit' value='Modificar Datos' name='btModificar' >
	</form>
	
	<h3>Mensajes</h3>
	<p>Tanto mensajes de error como información de las consultas</p>
	<label for='mensajes'>Mensajes:</label> 
	<textarea id='mensajes'>$mensajes</textarea>
	<h3>Informe</h3>
	
	<form action='#' method='post' name='modificion'>
		<label for='campo29'>Media de edad:</label>
		<input id='campo29' type='text' disabled='disabled' value='$informe[edadMedia]'>
		<label for='campo30'>Frecuencia tipo de sexo:</label>
		<input id='campo30' type='text' disabled='disabled' value='$informe[psexo]'>
		<label for='campo31'>Media pericia: </label>
		<input id='campo31' type='text' disabled='disabled' value='$informe[mediaPericia]'>
		<label for='campo32'>Media tiempo tarea:</label>
		<input id='campo32' type='text' disabled='disabled' value='$informe[mediaTiempo]'>
		<label for='campo33'>Porcentaje de completado:</label>
		<input id='campo33' type='text' disabled='disabled' value='$informe[pcorrecto]'>
		<label for='campo34'>Puntuacion media: </label>
		<input id='campo34' type='text' disabled='disabled' value='$informe[mediaValoracion]'>
		<input type='submit' value='Generar informe' name='btInforme' >
	</form>
	
	<h3>Exportar a csv</h3>
	<p>Exportar archivo a formato csv</p>
	<form action='#' method='post' name='exportar'>
		<input type='submit' value='Exportar' name='btExpCsv' >
	</form>
	
	<h3>Importar de csv</h3>
	<p>Importar archivo csv</p>
	<form action='#' method='post' name='importar' enctype='multipart/form-data'>
		<label for='campo35'>Elige un archivo csv:</label>
		<input id='campo35' type='file' name='file' accept='.csv'>
		<input type='submit' value='Importar' name='btImpCsv' >
		
	</form>
	
	

	
	";
?>
</main>



</body>
</html>