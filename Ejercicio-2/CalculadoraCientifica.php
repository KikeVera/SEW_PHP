<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<title>CalculadoraCientifica</title>
<link rel="stylesheet" href="CalculadoraCientifica.css"/>



</head>
<body>

<h1>Calculadora</h1>

<main>
<section>
<h2>Calculadora Cientifica</h2>
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
	
	class CalculadoraCientifica extends CalculadoraBasica{
		protected $ang;
		protected $operacion;
		protected $resultados;
		public function __construct(){
			parent::__construct();
			$this->ang = "RAD";
			$this->operacion = "";
			$this->resultados= array();
			
		}
		
		public function evaluar(){
			
			parent::evaluar();
			array_push($this->resultados, $this->valor);
		}
		
		public function borrar(){
			parent::borrar();
			$this->resultados=array();
		}
		
		
		
		public function calculaCuadrado(){
			
			$this->evaluar();
			if(is_numeric($this->valor)){
				$this->valor=pow($this->valor,2);
			}
			
			
		}
		public function calcularPEnesima(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				$this->escribir('e+');
			}
		}
		
		public function getTipAng(){
			return $this->ang;
		}
		
		public function cambiaAng(){
			if($this->ang=="RAD"){
				$this->ang="DEG";
			}
			else if($this->ang=="DEG"){
				$this->ang="RAD";
			}
		}
		
		protected function calculaAng($angulo){
			if($this->ang=="DEG"){
				$angulo=$angulo*'0.0174533';
			}
			
			return $angulo;
		}
		
		public function cambiaTipOperacion(){
			if($this->operacion==""){
				$this->operacion="h";
			}
			else if($this->operacion=="h"){
				$this->operacion="";
			}
		}
		
		public function getTipOperacion(){
			return $this->operacion;
		}
		
		public function calcularSeno(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				if($this->operacion=="h"){
					$this->valor=sinh($this->calculaAng($this->valor));
				}
				else{
					$this->valor=sin($this->calculaAng($this->valor));
				}
			}	
		}
		
		public function calcularCoseno(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				if($this->operacion=="h"){
					$this->valor=cosh($this->calculaAng($this->valor));
				}
				else{
					$this->valor=cos($this->calculaAng($this->valor));
				}
			}	
		}
		
		public function calcularTangente(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				if($this->operacion=="h"){
					$this->valor=tanh($this->calculaAng($this->valor));
				}
				else{
					$this->valor=tan($this->calculaAng($this->valor));
				}
			}	
		}
		
		public function calculaRaiz(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				$this->valor=sqrt($this->valor);
			}
			
		}
		
		public function calculaPotencia10(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				$this->valor=pow(10,$this->valor);
			}
			
		}
		
		public function calculaLogaritmo(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				$this->valor=log($this->valor);
			}
			
		}
		
		public function calculaResto(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				$this->escribir('%');
			}
			
		}
		
		
		
		public function resultadoAnterior(){
			 if (!empty($this->resultados)) {
				 $cima=array_pop($this->resultados);
				 if( $this->valor!=$cima){
					$this->valor=$cima;
				 }
				 else{
					$this->valor=array_pop($this->resultados);
				 }
			 }
		}
		
		public function back(){
			$this->valor= substr($this->valor, 0, strlen($this->valor)-1 );
		}
		
		public function factorial(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				$result=$this->valor;
				for($i=$this->valor-1;$i>0;$i--){
					$result=$result*$i;
				}
				$this->valor=$result;
			}
		}
		
		public function cambiarSigno(){
			$this->evaluar();
			if(is_numeric($this->valor)){
				$this->valor=-$this->valor;
			}
		}
		
		
		
	
		
		
	}
	
	if( isset( $_SESSION['calculadora'] ) ) {
		$calculadora =$_SESSION['calculadora'];
	}
	 else{
		 $calculadora = new CalculadoraCientifica();
	 }
	 
	
	
	$valor="";
	$tipAng=$calculadora->getTipAng();
	$tipOperacion=$calculadora->getTipOperacion();
	
	
	
	 
	 if (count($_POST)>0) 
    {  	
		if(isset($_POST[')'])) $calculadora->escribir(")");
		if(isset($_POST['('])) $calculadora->escribir("(");
		if(isset($_POST['+-'])) $calculadora->cambiarSigno();
		if(isset($_POST['n!'])) $calculadora->factorial();
		if(isset($_POST['back'])) $calculadora->back();
		if(isset($_POST['CE'])) $calculadora->resultadoAnterior();
		if(isset($_POST['e'])) $calculadora->escribir("2.71828182846");
		if(isset($_POST['Mod'])) $calculadora->calculaResto();
		if(isset($_POST['Exp'])) $calculadora->escribir("e+");
		if(isset($_POST['log'])) $calculadora->calculaLogaritmo();
		if(isset($_POST['10n'])) $calculadora->calculaPotencia10();
		if(isset($_POST['raiz'])) $calculadora->calculaRaiz();
		if(isset($_POST['sin'])) $calculadora->calcularSeno();
		if(isset($_POST['cos'])) $calculadora->calcularCoseno();
		if(isset($_POST['tan'])) $calculadora->calcularTangente();
		if(isset($_POST['ang'])) $calculadora->cambiaAng();
		if(isset($_POST['HYP'])) $calculadora->cambiaTipOperacion();
		if(isset($_POST['xn'])) $calculadora->calcularPEnesima();
		if(isset($_POST['x2'])) $calculadora->calculaCuadrado();
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
		if(isset($_POST['='])) $calculadora->evaluar();
		$tipAng=$calculadora->getTipAng();
		$tipOperacion=$calculadora->getTipOperacion();
		$valor=$calculadora->getValor();
		$_SESSION['calculadora'] = $calculadora;
		
    
    }


	echo " 
	
	<label for='ans'>Ans:</label> 
	<input disabled='disabled' type='text' id='ans' value='$valor'/>
	<form action='#' method='post' name='botones'>
		
			<input type='submit' value='$tipAng' name='ang'>
			<input type='submit' value='HYP' name='HYP'>
			
			
			<input type='submit' value='mr' name='mr'>
			<input type='submit' value='mc' name='mc'>
			<input type='submit' value='m-' name='m-' >
			<input type='submit' value='m+' name='m+'>
			<input type='submit' value='ms' name='ms'>
		
			<input type='submit' value='x&#178;' name='x2' >
			<input type='submit' value='x&#8319;' name='xn' >
			<input type='submit' value='sin$tipOperacion' name='sin' >
			<input type='submit' value='cos$tipOperacion' name='cos' >
			<input type='submit' value='tan$tipOperacion' name='tan' >
		
			<input type='submit' value='√' name='raiz' >
			<input type='submit' value='10&#8319;' name='10n' >
			<input type='submit' value='log' name='log' >
			<input type='submit' value='Exp' name='Exp' >
			<input type='submit' value='Mod' name='Mod' >
			
		
			<input type='submit' value='e' name='e' >
			<input type='submit' value='CE' name='CE' >
			<input type='submit' value='C' name='C' >
			<input type='submit' value='&#8656;' name='back' >
			<input type='submit' value='/' name='/' >
		
			<input type='submit' value='π' name='π' >
			<input type='submit' value='7' name='7' >
			<input type='submit' value='8' name='8' >
			<input type='submit' value='9' name='9' >
			<input type='submit' value='x' name='x' >
		
			<input type='submit' value='n!' name='n!' >
			<input type='submit' value='4' name='4' >
			<input type='submit' value='5' name='5' >
			<input type='submit' value='6' name='6'>
			<input type='submit' value='-' name='-' >
		
			<input type='submit' value='+-' name='+-' >
			<input type='submit' value='1' name='1' >
			<input type='submit' value='2' name='2' >
			<input type='submit' value='3' name='3' >
			<input type='submit' value='+' name='+' >
		
			<input type='submit' value='(' name='(' >
			<input type='submit' value=')' name=')' >
			<input type='submit' value='0' name='0' >
			<input type='submit' value='.' name='punto' >
			<input type='submit' value='=' name='=' >
		
	</form>
	
	
	";
?>
</section>
</main>



</body>
</html>