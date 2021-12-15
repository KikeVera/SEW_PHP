<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<title>CalculadoraRPN</title>
<link rel="stylesheet" href="CalculadoraRPN.css"/>
<meta name="viewport" content="width=device-width, initial-scale=1">


</head>
<body>

<h1>Calculadora</h1>

<main>
<section>
<h2>Calculadora RPN</h2>
<?php 
	session_start();
	class CalculadoraRPN {
		protected $valor;
		protected $pila;
		
		public function __construct(){
			$this->valor = "";
			$this->pila= array();
		}
		
		public function getValor(){
			
			return $this->valor;
		}
		
		
		
		public function escribir($entrada){
			$this->valor .= $entrada;
		}
		
		public function escribePila(){
			$aux="";
			$cont=count($this->pila)-1;
			foreach ($this->pila as $valor) {
				$aux.="Pila[".$cont."] = ".$valor."\n";
				$cont--;
				
			}
			
			
			return $aux;
		}
		
		
		
		public function introduceNumero(){
			if(is_numeric($this->valor)){
				$this->push($this->valor);
				$this->valor="";
			}
			
		}
		
		public function cambiarSigno(){
			if(is_numeric($this->valor)){
				$this->valor=-$this->valor;
			}
			
		}
		
		public function borrar(){
			$this->valor="";
			$this->pila= array();
		}
		
		public function sumar(){
			if(count($this->pila)>=2){
				$x=$this->pop();
				$y=$this->pop();
				$this->push($y+$x);
			}
		}
		
		public function restar(){
			if(count($this->pila)>=2){
				$x=$this->pop();
				$y=$this->pop();
				$this->push($y-$x);
			}
		}
		
		public function multiplicar(){
			if(count($this->pila)>=2){
				$x=$this->pop();
				$y=$this->pop();
				$this->push($y*$x);
			}
		}
		
		public function dividir(){
			if(count($this->pila)>=2){
				$x=$this->pop();
				$y=$this->pop();
				$this->push($y/$x);
			}
		}
		
		public function calculaCuadrado(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$this->push(pow($x,2));
			}
		}
		
		public function calculaRaiz(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$this->push(sqrt($x));
			}
		}
		

		public function calculaLogaritmo(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$this->push(log($x));
			}
		}
		
		public function factorial(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$result=$x;
				for($i=$x-1;$i>0;$i--){
					$result=$result*$i;
				}
				$this->push(log($result));
			}
		}
		
		
		public function calcularSeno(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$this->push(sin($x));
				
			}
			
		}
		
		public function calcularCoseno(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$this->push(cos($x));
				
			}
			
		}
		
		public function calcularTangente(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$this->push(tan($x));
				
			}
			
		}
		
		public function calcularArcSeno(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$this->push(asin($x));
				
			}
			
		}
		
		public function calcularArcCoseno(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$this->push(acos($x));
				
			}
			
		}
		
		public function calcularArcTangente(){
			if(count($this->pila)>=1){
				$x=$this->pop();
				$this->push(atan($x));
				
			}
			
		}
		
		public function deleteFirst(){
			if(count($this->pila)>=1){
				$this->pop();
			}
		}
		
		
		
		protected function push($v){
			array_push($this->pila, $v);
			
		}
		
		protected function pop(){
			return array_pop($this->pila);
			
		}
		
			
	}
	
	
	
	if( isset( $_SESSION['calculadora'] ) ) {
		$calculadora =$_SESSION['calculadora'];
	}
	 else{
		 $calculadora = new CalculadoraRPN();
	 }
	 
	
	
	$valor="";
	$pila="";
	
	
	
	
	
	 
	 if (count($_POST)>0) 
    {  	
		if(isset($_POST['DEL'])) $calculadora->deleteFirst();
		if(isset($_POST['+-'])) $calculadora->cambiarSigno();
		if(isset($_POST['n!'])) $calculadora->factorial();	
		if(isset($_POST['log'])) $calculadora->calculaLogaritmo();
		if(isset($_POST['raiz'])) $calculadora->calculaRaiz();
		if(isset($_POST['sin'])) $calculadora->calcularSeno();
		if(isset($_POST['cos'])) $calculadora->calcularCoseno();
		if(isset($_POST['tan'])) $calculadora->calcularTangente();
		if(isset($_POST['arcsin'])) $calculadora->calcularArcSeno();
		if(isset($_POST['arccos'])) $calculadora->calcularArcCoseno();
		if(isset($_POST['arctan'])) $calculadora->calcularArcTangente();
		if(isset($_POST['x2'])) $calculadora->calculaCuadrado();
		if(isset($_POST['+'])) $calculadora->sumar();
		if(isset($_POST['-'])) $calculadora->restar();
		if(isset($_POST['x'])) $calculadora->multiplicar();
		if(isset($_POST['/'])) $calculadora->dividir();
		if(isset($_POST['punto'])) $calculadora->escribir(".");
		if(isset($_POST['π'])) $calculadora->escribir("3.14159265359");
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
		if(isset($_POST['Enter'])) $calculadora->introduceNumero();
		
		$valor=$calculadora->getValor();
		$pila= $calculadora->escribePila();
		$_SESSION['calculadora'] = $calculadora;
		
    
    }


	echo " 
	<label for='ans'>Ans:</label> 
	<input disabled='disabled' type='text' id='ans' value='$valor'/>
	<label for='pila'>Pila:</label> 
	<textarea id='pila'>$pila</textarea>
	<form action='#' method='post' name='botones'>
		
			<input type='submit' value='C' name='C' >
			<input type='submit' value='DEL' name='DEL' >
			
			<input type='submit' value='sin' name='sin' >
			<input type='submit' value='cos' name='cos' >
			<input type='submit' value='tan' name='tan' >
			<input type='submit' value='x&#178;' name='x2' >
			
			<input type='submit' value='arcsin' name='arcsin' >
			<input type='submit' value='arccos' name='arccos' >
			<input type='submit' value='arctan' name='arctan' >
			<input type='submit' value='√' name='raiz' >
			
			<input type='submit' value='log' name='log' >
			<input type='submit' value='n!' name='n!' >
			<input type='submit' value='π' name='π' >
			<input type='submit' value='/' name='/' >
		
			<input type='submit' value='7' name='7' >
			<input type='submit' value='8' name='8' >
			<input type='submit' value='9' name='9' >
			<input type='submit' value='x' name='x' >
		
			<input type='submit' value='4' name='4' >
			<input type='submit' value='5' name='5' >
			<input type='submit' value='6' name='6'>
			<input type='submit' value='-' name='-' >
		
		
			<input type='submit' value='1' name='1' >
			<input type='submit' value='2' name='2' >
			<input type='submit' value='3' name='3' >
			<input type='submit' value='+' name='+' >
			
			<input type='submit' value='+-' name='+-' >
			<input type='submit' value='0' name='0' >
			<input type='submit' value='.' name='punto' >
			<input type='submit' value='Enter' name='Enter' >
		
	</form>
	
	
	";
?>
</section>
</main>



</body>
</html>