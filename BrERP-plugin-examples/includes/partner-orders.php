<?php ob_start();
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
add_shortcode('partner-orders', 'partner_orders');
function partner_orders() {
	require_once('general-setting.php');
	if(!empty($_SESSION['admin_user']) && isset($_SESSION['admin_user']))
	{
		$url=$model_servic_url;
		//first lets query for the C_BPartner_ID
		$post_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
			   <soapenv:Header/>
			   <soapenv:Body>
			      <_0:queryData>
			         <_0:ModelCRUDRequest>
			            <_0:ModelCRUD>
			               <_0:serviceType>wp-queryUsers</_0:serviceType>
			                <_0:DataRow>
			                  <!--Zero or more repetitions:-->
			                  <_0:field column="EMail">
			                    <_0:val>'. $_SESSION['admin_user'] .'</_0:val>
			                  </_0:field>
			               </_0:DataRow>
			            </_0:ModelCRUD>
			            '.$login_request.'
			         </_0:ModelCRUDRequest>
			      </_0:queryData>
			   </soapenv:Body>
			</soapenv:Envelope>';  
		  $soap_do = curl_init(); 

	        curl_setopt($soap_do, CURLOPT_URL,            $url );   
	        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10); 
	        curl_setopt($soap_do, CURLOPT_TIMEOUT,        10); 
	        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
	        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, FALSE);  
	        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, FALSE); 
	        curl_setopt($soap_do, CURLOPT_POST,           true ); 
	        curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string); 
	        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($post_string) )); 

	        $result = curl_exec($soap_do);
		    $p = xml_parser_create();
		    xml_parse_into_struct($p, $result, $vals, $index);
		    xml_parser_free($p);
	      	
            $getBPartnerID=$vals[22]['value'];
		
		
		$useid=$_SESSION['admin_user_id'];	
		$lurl=$model_servic_url;
				$post_stringl='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
			   <soapenv:Header/>
			   <soapenv:Body>
			      <_0:queryData>
			         <_0:ModelCRUDRequest>
			            <_0:ModelCRUD>
			               <_0:serviceType>wp-queryOrders</_0:serviceType>
			                <_0:DataRow>
			                  <!--Zero or more repetitions:-->
			                  <_0:field column="C_BPartner_ID">
			                     <_0:val>'. $getBPartnerID .'</_0:val>
			                  </_0:field>
			                
			               </_0:DataRow>
			            </_0:ModelCRUD>
			            '.$login_request.'
			         </_0:ModelCRUDRequest>
			      </_0:queryData>
			   	</soapenv:Body>
				</soapenv:Envelope>'; 
		        $soap_dol = curl_init(); 

		        curl_setopt($soap_dol, CURLOPT_URL, $lurl );   
		        curl_setopt($soap_dol, CURLOPT_CONNECTTIMEOUT, 10); 
		        curl_setopt($soap_dol, CURLOPT_TIMEOUT,        10); 
		        curl_setopt($soap_dol, CURLOPT_RETURNTRANSFER, true );
		        curl_setopt($soap_dol, CURLOPT_SSL_VERIFYPEER, FALSE);  
		        curl_setopt($soap_dol, CURLOPT_SSL_VERIFYHOST, FALSE); 
		        curl_setopt($soap_dol, CURLOPT_POST,           true ); 
		        curl_setopt($soap_dol, CURLOPT_POSTFIELDS,    $post_stringl); 
		        curl_setopt($soap_dol, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($post_stringl) )); 

		        $newresult = curl_exec($soap_dol);  
		        	    
	  			     
				 $pl = xml_parser_create();

		        xml_parse_into_struct($pl, $newresult, $valsl, $index);

		        xml_parser_free($pl);

			    
			    $data = array();
			    $i=0;
			    foreach($valsl as $key2 => $value)
			    {	
			    	@$cmn=$value['attributes']['COLUMN'];
			    	if($cmn!='')
			    	{ $lt=$key2;
			      		@$data[$i]['column'] = @$value['attributes']['COLUMN'];
			      		@$data[$i++]['column_value']=@$valsl[@$lt+1]['value'];
			      	}	
			      
			    }
			    
		       	$err = curl_error($soap_dol); 
		        curl_close($soap_dol);
		       
			    	
		        $listingHTml='<div class="newview newview1">
		   
		        <table id="landlord" class="display" width="80%" cellspacing="0">
				<thead> <tr><th colspan="4" style="text-align: center; padding: 35px 0px;background:#007acc; color:#fff;text-transform: uppercase;font-size: 32px;"> Pedidos</th></tr> ';
				$listingHTml.='<tr><th>Nº Documento</th>
									<th>Data do Pedido</th>
									<th>Descrição</th>
									<th>Total</th>';
				
				$listingHTml.='</tr></thead><tbody><tr>';

				$sz=1;
				$arr_size=sizeof($data);
				foreach ($data as  $listvalue)
				{ 
                    $listingHTml.='<td>'. $listvalue['column_value'] .'</td>';
					
					if($listvalue['column']=="GrandTotal")
						{
							if($sz < $arr_size)
							{	
								$listingHTml.='</tr><tr>';

							}	
						}	
					$sz++;	  	
				}  	
				  	
				$listingHTml.='</tr> </tbody>
				
			  </table></div>';
			  echo $listingHTml;
	}
	else
	{
		header('Location: '.site_url().'/index.php/entrar');
	}



}

function liast_script() {
	
	echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">';

	echo '<script type="text/javascript" src="'. plugins_url() .'/soap-admire/includes/js/datatable.js"></script>';

	echo '<script> 


		$(document).ready(function() {
		  	  
          $("#landlord").DataTable( {
              "pagingType": "full_numbers"
          } );
          $("#broker").DataTable( {
              "pagingType": "full_numbers"
          } );
          $("#plandlord").DataTable( {
              "pagingType": "full_numbers"
          } );		
          		    
		} );
  		  </script>';
}  		  
add_action( 'wp_enqueue_scripts', 'liast_script' );

?>
