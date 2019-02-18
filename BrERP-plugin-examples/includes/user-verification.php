<?php ob_start();
add_shortcode('user-verification', 'user_verification');
function user_verification()
{

	require_once('general-setting.php');   
	   @$username_verify=$_GET['username'];
	   @$code_verify=$_GET['code'];

		//----------------- get user detail ------------
		$url=$model_servic_url;
		$getpost_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
			   <soapenv:Header/>
			   <soapenv:Body>
			      <_0:queryData>
			         <_0:ModelCRUDRequest>
			            <_0:ModelCRUD>
			               <_0:serviceType>wp-queryUsers</_0:serviceType>
			                <_0:DataRow>
			                  <!--Zero or more repetitions:-->
			                  <!-- <_0:field column="EMail">
			                    <_0:val>test@eexample.com</_0:val>
			                  </_0:field> -->
						
			                  <_0:field column="Value">
			                    <_0:val>'. $username_verify .'</_0:val>
			                  </_0:field>
			               </_0:DataRow>
			            </_0:ModelCRUD>
			            '.$login_request.'
			         </_0:ModelCRUDRequest>
			      </_0:queryData>
			   </soapenv:Body>
			</soapenv:Envelope>';

			

        $soap_doget = curl_init(); 

        curl_setopt($soap_doget, CURLOPT_URL,            $url );   
        curl_setopt($soap_doget, CURLOPT_CONNECTTIMEOUT, 10); 
        curl_setopt($soap_doget, CURLOPT_TIMEOUT,        10); 
        curl_setopt($soap_doget, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_doget, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($soap_doget, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($soap_doget, CURLOPT_POST,           true ); 
        curl_setopt($soap_doget, CURLOPT_POSTFIELDS,    $getpost_string); 
        curl_setopt($soap_doget, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($getpost_string) )); 
       
        $resultget = curl_exec($soap_doget);        

	      $pget = xml_parser_create();
	      xml_parse_into_struct($pget, $resultget, $valsget, $index);
	      xml_parser_free($pget);

	      

	      @$getid=$valsget[7]['value'];             //AD_User_ID
	      @$getemail= $valsget[19]['value'];        //email
	      @$getuser_name=$valsget[34]['value'];     //Value
	      @$getcode_verify=$valsget[31]['value'];    //verifycode
	      @$getverify_status=$valsget[37]['value'];   //isLocked   
	      @$uid=$valsget[7]['value'];
	      @$err = curl_error($soap_doget);

	     curl_close($soap_doget);
	 //----------------- End get user detail ------------    
		if($username_verify== $getuser_name && $uid!='')
		{
			if($code_verify!=$getcode_verify)
                echo "<p style='color:red; text-align:center'>Verification Code Incorrect</p>";
			
			else if($code_verify==$getcode_verify)
			{			
				$url=$model_servic_url;

				// ============= for get currently added user verification ================
				$dat= date("Y-m-d h:i:s");	
			
				$user_verify='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
												xmlns:_0="http://idempiere.org/ADInterface/1_0">
												<soapenv:Header/>
												<soapenv:Body>
														<_0:createUpdateData>
																<_0:ModelCRUDRequest>
																		<_0:ModelCRUD>
																				<_0:serviceType>wp-VerifyUser</_0:serviceType>
																				<_0:DataRow>
																						<_0:field column="AD_User_ID">
																								<_0:val>'. $uid .'</_0:val>
																						</_0:field>
																						<_0:field column="EMailVerifyDate">
																								<_0:val>'. $dat .'</_0:val>
																						</_0:field>
																						<_0:field column="IsLocked">
																								<_0:val>N</_0:val>
																						</_0:field>
																				</_0:DataRow>
																		</_0:ModelCRUD>  '.$login_request.' </_0:ModelCRUDRequest>
														</_0:createData>
												</soapenv:Body>
										</soapenv:Envelope>';
				
				$soap_do = curl_init(); 
				curl_setopt($soap_do, CURLOPT_URL,            $url );   
				curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
				curl_setopt($soap_do, CURLOPT_TIMEOUT,        10); 
				curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
				curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);  
				curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false); 
				curl_setopt($soap_do, CURLOPT_POST,           true ); 
				curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $user_verify); 
				curl_setopt($soap_do, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($user_verify) )); 			
				$result = curl_exec($soap_do);	
				echo "<br/>";
				$p = xml_parser_create();
				xml_parse_into_struct($p, $result, $vals, $index);
				xml_parser_free($p);		
				$output= $vals[3]['attributes']['RECORDID'];

				$err = curl_error($soap_do);	
				
				if($output!=''){
					echo "<p class'success'>Validação de usuário completa. Agora você pode fazer login.  </p>";
				}


				curl_close($soap_do);
			}
			else
			{
			 echo "<p style='color:red; text-align:center'>Falha na validação</p>";
			}	
			
		}	
		
		else
		{
			echo "<p style='color:red; text-align:center'>Você não está registrado.</p>";
		}
}
?>
