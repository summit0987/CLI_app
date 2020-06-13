<?php 

require __DIR__ . '/vendor/autoload.php';

Class PHP_CLA {

	public function check_validation($argc,$argv){

		if(isset($argc) && $argc == 3){
			
			$message = "";
			
			/* if using url and file name seperate then use this */
			//$url = $argv[1].$argv[2];
			
			$url = $argv[1]; 
			
			$explodeurl_for_check_file = explode('/',$url);
			$explodeurl_for_check_file = end($explodeurl_for_check_file);
			
			if($explodeurl_for_check_file != $argv[2]){
				/* checking url file name and actual file name matching */
				$message = 'File Not match with URL File Name';
			}else{
				
				// Use get_headers() function to reponse from domain status and ping 
				$headers = @get_headers($url); 
				  
				// Use condition to check the existence of URL 
				
				$headers = $this->check_url_header($url); 
				
				if($headers) {
					
					$status = "URL Exist"; 
				
					/* SECOND Way to find out if data received from url and decoded properly */
					//if(file_get_contents($proper_url)){}
						
						$json = json_decode(file_get_contents($url),true);
						
						if(is_array($json)){
							
							$data = json_decode(file_get_contents($url));

							// used JSON SCHEMA Validate library
							$validator = new JsonSchema\Validator();
							$validator->check($data, (object) array('$ref' => 'file://' . realpath('schema/schema.json')));

							if ($validator->isValid()) {
								$message = "Valid JSON";
								var_dump($data);
								
							} else {
								echo "JSON does not validate. </br>";
								foreach ($validator->getErrors() as $error) {
									echo sprintf("[%s] %s\n", $error['property'], $error['message']).'</br>';
								}
								
							}
							
						}else{
							$message = 'Invalid json';
						}
				}else{
					$message = 'Invalid URL';
				}
			}
		}else{
			$message = 'Invalid Variables kindly pass 2 variables in Command line';
		}

		return $message;
	}
	
	
	/* check header function if 200 ok */
	public function check_url_header($url){
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 1);

		$response = curl_exec($ch);
		  
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, 30);
		  
		curl_close($ch);

		if(strpos($headers, '200 OK')){
			return true;
		}else{
			return false;
		}
	}

}

// call function
$result = new PHP_CLA();
echo $result->check_validation($argc,$argv);


?>