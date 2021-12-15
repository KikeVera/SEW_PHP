<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<title>BaseDatos</title>
<link rel="stylesheet" href="Ejercicio7.css"/>



</head>
<body>

 <h1/>Cines</h1>

<main>
<h2>Busqueda de películas</h2>
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
			$this ->tablaCine="Cine";
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
		protected function existeTabla($db){
			if(!$db->query("DESCRIBE ". $this ->tablaCine)){
				$this->mensajes.="Tabla cine no creada.";
				
			}
			if(!$db->query("DESCRIBE ". $this ->tablaPeli)){
				$this->mensajes.="Tabla pelicula no creada.";
				
			}
			if(!$db->query("DESCRIBE ". $this ->tablaSala)){
				$this->mensajes.="Tabla sala no creada.";
				
			}
			if(!$db->query("DESCRIBE ". $this ->tablaSesion)){
				$this->mensajes.="Tabla sesnion no creada.";
				
			}
		}
		
		protected function camposVacios($datos){
			$campo=false;
			foreach($datos as $x => $x_value) {
				if(!empty($x_value)){
					$campo=true;
					
				}	
			}
			
			if(!$campo){
				$this->mensajes.="Al menos un campo no debe estar vacio \n";
			}
		}
		
		protected function execute($db,$consultaPre,$operacion){
			if($consultaPre->execute()){
				$this->mensajes.="Resultados ".$operacion."."."\n\n";
			}
			else{
				$this->mensajes.="ERROR SQL:".$db->error."\n";
			}
			
		}
		
	
		

	
		
		
		public function busquedaPersonalizada($datos){
			
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

			if(!empty($this -> mensajes)) return;
			
			$sentencia="SELECT titulo,nombre,numero,fecha,hora FROM " .$this ->tablaCine.", ".$this ->tablaPeli.", ".$this ->tablaSala.", ".$this ->tablaSesion.
						" WHERE ".$this ->tablaCine.".id=".$this ->tablaSala.".idCine and ".$this ->tablaSala.".id=".$this ->tablaSesion.".idSala and ".
						$this ->tablaSesion.".idPelicula=".$this ->tablaPeli.".id";
			
			$types="";
			$binds=[];
			if(!$datos["cine"]==""){
				$sentencia.=" and nombre=?";
				$types.="s";
				array_push($binds,$datos["cine"]);
			}
			
			if(!$datos["genero"]==""){
				$sentencia.=" and genero=?";
				$types.="s";
				array_push($binds,$datos["genero"]);
			}
			
			if(!$datos["ciudad"]==""){
				$sentencia.=" and ciudad=?";
				$types.="s";
				array_push($binds,$datos["ciudad"]);
			}
			
			if(!$datos["fecha"]==""){
				$sentencia.=" and fecha=?";
				$types.="s";
				array_push($binds,$datos["fecha"]);
			}
			
			if(!$datos["hora"]==""){
				$sentencia.=" and hora=?";
				$types.="s";
				array_push($binds,$datos["hora"]);
			}
			
			$consultaPre=$db->prepare($sentencia);
			$consultaPre->bind_param($types,...$binds);
			$this->execute($db,$consultaPre,"encontrados");
			$resultado=$consultaPre->get_result();
			if($resultado->fetch_assoc()!=NULL){
				$resultado->data_seek(0);
				while($fila=$resultado->fetch_assoc()){
					$this->mensajes.="Pelicula: ".$fila["titulo"].". En el cine ".$fila["nombre"].". Sala ".$fila["numero"].". El dia ".$fila["fecha"].
					". A las ".$fila["hora"]."\n\n";
				}
			}
			else{
				$this->mensajes.="Sin resultados";
			}
			
			$consultaPre->close();
			$db->close();
		
		}
		
		public function busquedaPeli($datos){
			
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
			if($datos==""){
				$this->mensajes.= "Datos no encontrados \n";
			}

			if(!empty($this -> mensajes)) return;
			
			$sentencia="SELECT nombre,numero,fecha,hora FROM " .$this ->tablaCine.", ".$this ->tablaPeli.", ".$this ->tablaSala.", ".$this ->tablaSesion.
						" WHERE ".$this ->tablaCine.".id=".$this ->tablaSala.".idCine and ".$this ->tablaSala.".id=".$this ->tablaSesion.".idSala and ".
						$this ->tablaSesion.".idPelicula=".$this ->tablaPeli.".id and titulo=?";
			
			
			
			$consultaPre=$db->prepare($sentencia);
			$consultaPre->bind_param("s",$datos);
			$this->execute($db,$consultaPre,"encontrados");
			$resultado=$consultaPre->get_result();
			if($resultado->fetch_assoc()!=NULL){
				$resultado->data_seek(0);
				while($fila=$resultado->fetch_assoc()){
					$this->mensajes.="La pelicula se emite en el cine ".$fila["nombre"].". Sala ".$fila["numero"].". El dia ".$fila["fecha"].
					". A las ".$fila["hora"]."\n\n";
				}
			}
			else{
				$this->mensajes.="Sin resultados";
			}
			
			$consultaPre->close();
			$db->close();
		
		}
		
		public function busquedaCine($datos){
			
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
			if($datos==""){
				$this->mensajes.= "Datos no encontrados \n";
			}

			if(!empty($this -> mensajes)) return;
			
			$sentencia="SELECT titulo FROM " .$this ->tablaCine.", ".$this ->tablaPeli.", ".$this ->tablaSala.", ".$this ->tablaSesion.
						" WHERE ".$this ->tablaCine.".id=".$this ->tablaSala.".idCine and ".$this ->tablaSala.".id=".$this ->tablaSesion.".idSala and ".
						$this ->tablaSesion.".idPelicula=".$this ->tablaPeli.".id and nombre=?";
			
			
			
			$consultaPre=$db->prepare($sentencia);
			$consultaPre->bind_param("s",$datos);
			$this->execute($db,$consultaPre,"encontrados");
			$this->mensajes.="El cine tiene en cartelera las siguientes películas: \n\n";
			$resultado=$consultaPre->get_result();
			if($resultado->fetch_assoc()!=NULL){
				$resultado->data_seek(0);
				while($fila=$resultado->fetch_assoc()){
					$this->mensajes.=$fila["titulo"]."\n";
				}
			}
			else{
				$this->mensajes.="Sin resultados";
			}
			
			$consultaPre->close();
			$db->close();
		
		}
		
		
	
	
	}
	
	class Formulario {
		protected $personalizada;
		protected $cine;
		protected $pelicula;
		public function __construct(){
			$this->personalizada=array("cine"=>"","genero"=>"","ciudad"=>"","fecha"=>"","hora"=>"");
			$this -> cine="";
			$this -> pelicula="";
		}
		public function setPersonalizada($array){
			$this -> personalizada=$array;
		}
		public function getPersonalizada(){
			return $this -> personalizada;
		}
		public function setCine($array){
			$this -> cine=$array;
		}
		public function getCine(){
			return $this -> cine;
		}
		public function setPelicula($array){
			$this -> pelicula=$array;
		}
		public function getPelicula(){
			return $this -> pelicula;
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
	 
	 $personalizada = $formulario -> getPersonalizada();
	 $cine = $formulario -> getCine();
	 $pelicula = $formulario -> getPelicula();
	
	

	 if (count($_POST)>0) 
    {  	
		
		
		if(isset($_POST['btBuscarPersonalizada'])){ 
			$datos=array("cine"=>$_POST["cine"],"genero"=>$_POST["genero"],"ciudad"=>$_POST["ciudad"],"fecha"=>$_POST["fecha"],
				  "hora"=>$_POST["hora"]);
			$base->busquedaPersonalizada($datos);
			$formulario -> setPersonalizada($datos);
			
			}
		if(isset($_POST['btBuscarCine'])){ 
			$datos=$_POST["cine"];
			$base->busquedaCine($datos);
			$formulario -> setCine($datos);
			
		}
			
		if(isset($_POST['btBuscarPeli'])){ 
			$datos=$_POST["pelicula"];
			$base->busquedaPeli($datos);
			$formulario -> setPelicula($datos);
		}	
		
		
		
		$mensajes=$base->getMensajes();
		
		
		$personalizada = $formulario -> getPersonalizada();
		$cine = $formulario -> getCine();
		$pelicula = $formulario -> getPelicula();
		$_SESSION['formulario'] = $formulario;
		
    
    }


	echo " 
		
	<h3>Buscar una pelicula</h3>
	<form action='#' method='post' name='insercion'>
		<label>Titulo: <input type='text' name='pelicula' value='$pelicula'></label>
		<input type='submit' value='Buscar pelicula' name='btBuscarPeli' >
	</form>
	
	
	<h3>Cartelera</h3>
	<form action='#' method='post' name='insercion'>
		<label>Cine: <input type='text' name='cine' value='$cine'></label>
		<input type='submit' value='Ver cartelera' name='btBuscarCine' >
	</form>
	
	
	<h3>Busqueda personalizada</h3>
	<form action='#' method='post' name='personalizada'>
		<label>Cine: <input type='text' name='cine' value='$personalizada[cine]'></label>
		<label>Genero: <input type='text' name='genero' value='$personalizada[genero]'></label>
		<label>Ciudad: <input type='text' name='ciudad' value='$personalizada[ciudad]'></label>
		<label>Fecha: <input type='date' name='fecha' value='$personalizada[fecha]'></label>
		<label>Hora: <input type='text' name='hora' list='horas' value='$personalizada[hora]'></label>
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
		<input type='submit' value='Buscar' name='btBuscarPersonalizada'>
	</form>
	
	<h3>Resultado de la busqueda</h3>
	<textarea >$mensajes</textarea>
	
	

	

	
	";
?>
</main>



</body>
</html>