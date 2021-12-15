<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<title>CalculadoraBasica</title>
<link rel="stylesheet" href="CalculadoraBasica.css"/>



</head>
<body>

<h1>Calculadora</h1>
<main>
<section>
<h2>Calculadora Básica</h2>
<?php 
	session_start();
	
	class CalculadoraBasica{
		protected $valor;
		protected $memoria;
		
		 public function __construct(){
			 
			
			$this->valor = "";
			$this->memoria = "";
			 
			
		}
		
		public function storage(){
			if(is_numeric($this->valor)){
				$this->memoria=$this->valor;
			}
			
			else{
				$this->valor="Error: imposible almacenar en memoria ";
			}
		}
		
		public function sumMem(){
			if(is_numeric($this->valor) and $this->memoria!=""){
				$this->memoria=$this->memoria+$this->valor;
			}
			
			else{
				$this->valor="Error: imposible sumar a la memoria ";
			}
		}
		
		public function resMem(){
			if(is_numeric($this->valor) and $this->memoria!="" ){
				$this->memoria=$this->memoria-$this->valor;
			}
			
			else{
				$this->valor="Error: imposible restar de la memoria ";
			}
		}
		
		public function recuperate(){
			$this->valor=$this->memoria;
		}
		
		public function clear(){
			$this->memoria="";
		}
		
		public function getValor(){
			
			return $this->valor;
		}
		
		public function escribir($entrada){
			$this->valor .= $entrada;
		}
		
		public function borrar(){
			$this->valor = "";
		}
		
		
		public function evaluar(){
			try {
				$this->valor = eval("return $this->valor ;"); 
			}
			catch (Exception $e) {
				$this->valor = "Error: " .$e->getMessage();
			} 
		    catch (ParseError $e) {
				$this->valor = "Error: " .$e->getMessage();	
		    }
			
		}
		
	}
	if( isset( $_SESSION['calculadora'] ) ) {
		$calculadora =$_SESSION['calculadora'];
	}
	 else{
		 $calculadora = new CalculadoraBasica();
	 }
	 
	
	 
	$valor="";
	 
	 if (count($_POST)>0) 
    {  
		if(isset($_POST['m+'])) $calculadora->sumMem();
		if(isset($_POST['m-'])) $calculadora->resMem();
		if(isset($_POST['ms'])) $calculadora->storage();
		if(isset($_POST['mc'])) $calculadora->clear();
		if(isset($_POST['mr'])) $calculadora->recuperate();
		if(isset($_POST['+'])) $calculadora->escribir("+");
		if(isset($_POST['-'])) $calculadora->escribir("-");
		if(isset($_POST['x'])) $calculadora->escribir("*");
		if(isset($_POST['/'])) $calculadora->escribir("/");
		if(isset($_POST['punto'])) $calculadora->escribir(".");
		if(isset($_POST['0'])) $calculadora->escribir("0");
		if(isset($_POST['1'])) $calculadora->escribir("1");
        if(isset($_POST['2'])) $calculadora->escribir("2");
        if(isset($_POST['3'])) $calculadora->escribir("3");
		if(isset($_POST['4'])) $calculadora->escribir("4");
		if(isset($_POST['5'])) $calculadora->escribir("5");
		if(isset($_POST['6'])) $calculadora->escribir("6");
        if(isset($_POST['7'])) $calculadora->escribir("7");
        if(isset($_POST['8'])) $calculadora->escribir("8");
		if(isset($_POST['9'])) $calculadora->escribir("9");
		if(isset($_POST['C'])) $calculadora->borrar();
		if(isset($_POST['='])) $calculadora->evaluar();
		$valor=$calculadora->getValor();
        
		$_SESSION['calculadora'] = $calculadora;
		
    
    }


	echo " 
	<label for='ans'>Ans:</label> 
	<input disabled='disabled' type='text' id='ans' value='$valor'/>
	<form action='#' method='post' name='botones'>
		<input type='submit' value='mr' name='mr' >
		<input type='submit' value='mc' name='mc' >
		
		<input type='submit' value='m-' name='m-'>
		<input type='submit' value='m+' name='m+'>
		<input type='submit' value='ms' name='ms' >
		<input type='submit' value='/'  name='/'>


		<input type='submit' value='7' name='7'>
		<input type='submit' value='8' name='8'>
		<input type='submit' value='9' name='9'>
		<input type='submit' value='x' name='x'>


		<input type='submit' value='4' name='4'>
		<input type='submit' value='5' name='5'>
		<input type='submit' value='6' name='6'>
		<input type='submit' value='-' name='-'>


		<input type='submit' value='1' name='1'>
		<input type='submit' value='2' name='2'>
		<input type='submit' value='3' name='3'>
		<input type='submit' value='+' name='+'>


		<input type='submit' value='0' name='0'>
		<input type='submit' value='.' name='punto'>
		<input type='submit' value='C' name='C'>
		<input type='submit' value='=' name='='>
	</form>
	
	
	";
?>
</section>
</main>


</body>
</html>