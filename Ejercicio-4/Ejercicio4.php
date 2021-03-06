<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">

<title>Petróleo</title>
<link rel="stylesheet" href="Ejercicio4.css"/>
<meta name="viewport" content="width=device-width, initial-scale=1">


</head>
<body>



<h1>Petróleo</h1>
<?php 
	session_start();
	class Petroleo {
		protected $table;
	
		
		public function __construct(){
			
			
			$this->table= "";
			

		}
		
		public function getData($start_date,$end_date,$base,$zona){
			$endpoint = 'timeseries';
			$access_key = 'kf04g0ueyek9klm1fv1hw10u9g83r2xy3gvd8m7gagx6g11s9exfx711u7k2';
			$symbols='';
			if($zona==="América"){
				$symbols='WTIOIL';
			}
			
			else if($zona==="Europa"){
				$symbols='BRENTOIL';
			}
			
			
			$ch = curl_init('https://www.commodities-api.com/api/'.$endpoint.'?access_key='.$access_key.'&start_date='.$start_date.'&end_date='.$end_date.'&base='.$base.'&symbols='.$symbols);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$json = curl_exec($ch);
			curl_close($ch);
			$conversionResult = json_decode($json, true);
			
			
			
			
			return $conversionResult;
			
			
		}
		
		
		public function getTable(){
			
			return $this->table;
		}
		
		
		
		public function setTable($start_date,$end_date,$base,$zona){
			$data=$this->getData($start_date,$end_date,$base,$zona);
			$table='<table><thead><tr><th>Fecha</th><th>Precio</th></tr></thead>';
			$table.='<tbody>';
			if(!isset($data['data']['rates'])){
				$table.="<tr>";
				$table.="<td>NA</td>";
				$table.="<td>NA</td>";
				$table.="</tr>";
			}
			else{
			
				foreach($data['data']['rates'] as $key=>$value){
					$table.="<tr>";
					$table.="<td>".$key."</td>";
					$unit="";
					$price="";
					foreach($data['data']['rates'][$key] as $key2=>$value2){
						if($key2==='BRENTOIL'||$key2==='WTIOIL'){
							$price=number_format(1/$value2,2);
						}
						
					
					}
					$table.="<td>".($price)." ";
					$table.=$data['data']['base']."/barril</td>";
					$table.="</tr>";
				}
			}
			$table.="</tbody>";
			$table.="</table>";
			$this->table= $table;
		}
		
		
		
	
			
	}
	
	
	if( isset( $_SESSION['petroleo'] ) ) {
		$petroleo =$_SESSION['petroleo'];
	}
	 else{
		 $petroleo = new Petroleo();
	 }
	 
	
	
	$datos="";
	
	

	
	 
	 if (count($_POST)>0) 
    {  	
		
		if(isset($_POST['bt'])){
			$petroleo->setTable($_POST["datei"],$_POST["datef"],$_POST["moneda"],$_POST["zona"]);
			
		} 
		
		$datos=$petroleo->getTable();
		
		
		$_SESSION['petroleo'] = $petroleo;
		
    
    }


	echo " 
		
	<h2>Filtros</h2>
	<p>Solo se proporcionan datos a partir del 2021-09-22 y para BrentOil y WTIOil, estandars muy usados en Europa y América respectivamente</p>
	
	<form action='#' method='post' name='español'>
		
		<label for='start'>Fecha inicio:</label>
		<input type='date' id='start' min='2021-09-22' name='datei'>
		<label for='end'>Fecha final:</label>
		<input type='date' id='end' min='2021-09-23' name='datef'>
		<label for='zona'>Zona:</label>
		<input list='zonaList' id='zona' name='zona'>
		<datalist id='zonaList'>
			<option value='Europa'>
			<option value='América'>
		</datalist>
		<label for='moneda'>Unidad monetaria:</label>
		<input list='monedaList' id='moneda' name='moneda'>
		<datalist id='monedaList'>
			<option value='USD'>
			<option value='EUR'>
			<option value='GBP'>
		</datalist>
		
		
		<input type='submit' value='Buscar' name='bt'>
		
	</form>
	<section>
			<h2>Precio del petróleo</h2>
			<p>Datos del precio del petróleo en la zona, fecha y unidad monetaria seleccionada</p>
			$datos
		</section>
	
	
		
	
	
	
	";
?>




</body>
</html>