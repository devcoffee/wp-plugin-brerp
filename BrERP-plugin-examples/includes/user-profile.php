<?php ob_start();
session_start();
add_shortcode('user-profile', 'user_profile');
function user_profile() {

	require_once('general-setting.php');
	if(!empty($_SESSION['admin_user']) && isset($_SESSION['admin_user']))
	{
	  echo '<span id="block1"><a href="javascript:void(0)">< Visualizar > </a></span><span id="block2"><a href="javascript:void(0)"> < Editar ></a></span>';
	  
	  $url=$model_servic_url;
	   if(isset($_POST['block2']))
	   {

	     $name=$_POST['uname']; 
	     $login_name=$_POST['login_name'];
	     $upassword=$_POST['upassword'];
	     $oldpassword=$_POST['oldpassword'];
	     $em= $_POST['em'];
	     $user_phone1=$_POST['user_phone1'];
	     $user_phone2=$_POST['user_phone2'];
	     $email_verify = $_POST['email_verify'];
	     $islock = $_POST['islock'];


	       //---------- get user information -----------------
					$post_string='
					<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
						<soapenv:Header/> 
							<soapenv:Body> 
								<_0:queryData>
									<_0:ModelCRUDRequest>
										<_0:ModelCRUD>
											<_0:serviceType>wp-queryUsers</_0:serviceType>
												<_0:DataRow> <!--Zero or more repetitions:--><!-- 
													<_0:field column="AD_User_ID"> 
														<_0:val>'. $user_profile_ad_user_id .'</_0:val>
															</_0:field> -->
															<_0:field column="EMail">
																<_0:val>'. $em .'</_0:val>
															</_0:field> 
												</_0:DataRow>
										</_0:ModelCRUD>'
										.$login_request.'
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
	        
	        echo "<br/>";

	      $p = xml_parser_create();
	      xml_parse_into_struct($p, $result, $vals, $index);
	      xml_parser_free($p);
	      
	      //loop to clean up the result into 2 dimensional array $data = ['column']['column_value']
	      	$data = array();
			$i=0;
			foreach($vals as $key2 => $value)
			{	
				$cmn=$value['attributes']['COLUMN'];
			 	if($cmn!='')
			 	{ 
			 	$lt=$key2;
			 	$data[$i]['column'] = $value['attributes']['COLUMN'];
			 	$data[$i++]['column_value']=$vals[$lt+1]['value'];
			   	}	
			}
			
			
			// loop again to get the values into the correct variable
		    foreach ($data as  $listvalue)
		    {	
     		    if ($listvalue['column']=="AD_User_ID" )
                     $getid=$listvalue['column_value'];
                elseif ($listvalue['column']=="EMail" )
                     $getemail=$listvalue['column_value'];
                elseif ($listvalue['column']=="Name" )
                     $getname=$listvalue['column_value'];     
                elseif ($listvalue['column']=="Password" )
                     $getpass=$listvalue['column_value'];
		    }
      
          
	      $err = curl_error($soap_do);

	     curl_close($soap_do);
		 if($getemail == $em)
		 {  
		    if($oldpassword == $getpass)
		    { 
	    
		  	 $post_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <_0:createUpdateData>
				         <_0:ModelCRUDRequest>
				            <_0:ModelCRUD>
				               <_0:serviceType>wp-createUpdateUser</_0:serviceType>
				               <_0:DataRow>                                
				                  <_0:field column="Name">
				                     <_0:val>'. $name .'</_0:val>
				                  </_0:field>
				                  <_0:field column="Value">
				                     <_0:val>'. $login_name .'</_0:val>
				                  </_0:field>
				                  <_0:field column="Password">
				                     <_0:val>'. $upassword .'</_0:val>
				                  </_0:field>
				                  <_0:field column="EMail">
				                     <_0:val>'. $em .'</_0:val>
				                  </_0:field>                  
				                  <_0:field column="Phone">
				                     <_0:val>'. $user_phone1 .'</_0:val>
				                  </_0:field>
				                   <_0:field column="Phone2">
				                     <_0:val>'. $user_phone2 .'</_0:val>
				                  </_0:field>                        
				               </_0:DataRow>
				            </_0:ModelCRUD>
				            '.$login_request.'
				         </_0:ModelCRUDRequest>
				      </_0:createData>
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
		      	$output= $vals[3]['attributes']['RECORDID'];
		      	if($output!='')
		      	{
		      		echo "<p>Your information updated successfully</p>";
		      		header('Location: '.site_url().'/index.php/perfil-do-usuario');
		      	}	
		    }
		    else{ echo '<p class="error" style="margin-bottom: -50px;margin-top: 47px;">Please enter valid Old Password</p>';
		    		echo '<style type="text/css">.block2{ display:block;}.block1{ display:none;}</style>';
				}
	      }
	      else{ echo '<p class="error" style="margin-bottom: -50px;margin-top: 47px;">Please enter registered Email</p>';
	      		echo '<style type="text/css">.block2{ display:block;}.block1{ display:none;}</style>';
	  		  }   

	  	}
	  	 	 
		 
		  $post_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
			   <soapenv:Header/>
			   <soapenv:Body>
			      <_0:queryData>
			         <_0:ModelCRUDRequest>
			            <_0:ModelCRUD>
			               <_0:serviceType>wp-queryUsers</_0:serviceType>
			                <_0:DataRow>
			                  <!--Zero or more repetitions:-->
			                  <!--
			                  <_0:field column="AD_User_ID">
			                     <_0:val>'. $user_profile_ad_user_id .'</_0:val>
			                  </_0:field>
			                  -->
			                  
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
	      	
	      	//loop to clean up the result into 2 dimensional array $data = ['column']['column_value']
	      	$data = array();
			$i=0;
			foreach($vals as $key2 => $value)
			{	
				@$cmn=$value['attributes']['COLUMN'];
			 	if($cmn!='')
			 	{ 
			 	$lt=$key2;
			 	$data[$i]['column'] = $value['attributes']['COLUMN'];
			 	$data[$i++]['column_value']=$vals[$lt+1]['value'];
			   	}	
			}
			
			@$err = curl_error(@$soap_dol);
		    curl_close(@$soap_dol);
			
			// loop again to get the values into the correct variable
		    foreach ($data as  $listvalue)
		    {	
     		    if ($listvalue['column']=="AD_User_ID" )
                     $getid=$listvalue['column_value'];
     		    elseif ($listvalue['column']=="Name" )
                     $getname=$listvalue['column_value'];
                elseif ($listvalue['column']=="Password" )
                     $getpass=$listvalue['column_value'];
                elseif ($listvalue['column']=="EMail" )
                     $getemail=$listvalue['column_value'];
                elseif ($listvalue['column']=="Phone" )
                     $phone=$listvalue['column_value'];
                elseif ($listvalue['column']=="Phone2" )
                     $Phone2=$listvalue['column_value'];
                elseif ($listvalue['column']=="EMailVerify" )
                     $EMailVerify=$listvalue['column_value'];
                elseif ($listvalue['column']=="Value" )
                     $get_login_name=$listvalue['column_value'];
                elseif ($listvalue['column']=="IsLocked" )
                     $IsLocked=$listvalue['column_value'];
		    }

		  echo $viwhtml='<div class="block1"><table>
			<thead> <tr><th colspan="2" style="text-align: center; padding: 35px 0px;background:#007acc; color:#fff;text-transform: uppercase;font-size: 32px;"> Perfil do Usuário</th></tr> 
				<tr>
			  		<th>ID do Usuário</th>
			  		<td>'. $getid .'</td>
			  	</tr>
			  	<tr class="blue">	
			  		<th>Nome Completo </th>
			  		<td>'. $getname .'</td>
			  	</tr>
			  	<tr class="blue">	
			  		<th>Usuário </th>
			  		<td>'. $get_login_name .'</td>
			  	</tr>
			  	<tr>	
			  		<th>Senha</th>
			  		<td>'. $getpass .'</td>
			  	</tr>
			  	<tr class="blue">	
			  		<th>Email</th>
			  		<td>'. $getemail .'</td>
			  	</tr>
			  	<tr>	
			  		<th>Telefone</th>
			  		<td>'. $phone .'</td>
			  	</tr>
			  	<tr class="blue">	
			  		<th>Telefone 2</th>
			  		<td>'. $Phone2 .'</td>
			  	</tr>
			  	<tr>	
			  		<th>Travado</th>
			  		<td>'. $IsLocked .'</td>
			  	</tr>
			</thead> 
			
		  </table></div>';
      	
		echo $edithtml='<div class="block2">
		<form id="rg" name="myform" action=""  method="POST"  onsubmit="return validateform_profile();">
		<thead> <tr><th colspan="2" style="text-align: center; padding: 35px 0px;background:#007acc; color:#fff;text-transform: uppercase;font-size: 32px;"> Editar Perfil<br></th></tr> 
				<tr>	
                    <td>Nome Completo </td>
                    <td><input type="text" name="uname" id="uname" placeholder="Nome Completo" value="'. $getname .'" required="required" ></td>
                </tr>
                <tr>	
                    <td>Usuário</td>
			  		<td><input type="text" name="login_name" id="login_name" placeholder="Usuário" value="'. $get_login_name .'" required="required" ></td>
                </tr>
                
                <tr>	
                    <td>Nova Senha</td>
			  		<td><input type="password" name="upassword" id="pass" placeholder="Nova Senha" value="'. $getpass .'" ></td>
                </tr>

                <tr>	
                    <td>Senha Antiga</td>
			  		<td><input type="password" name="oldpassword" id="oldpass" placeholder="Senha Antiga" value="" required="required" ></td>
                </tr>

                <tr>	
                    <td>Email</td>
			  		<td><input type="hidden" readonly name="em" placeholder="Email" value="'. $getemail .'"></td>
                </tr>
      
                <tr>	
                    <td>Telefone</td>
			  		<td><input type="text" id="user_phone1" name="user_phone1" placeholder="Telefone" required="required" value="'. $phone .'" ></td>
                </tr>

                <tr>	
                    <td>Telefone 2</td>
			  		<td><input type="text" id="user_phone2" name="user_phone2" placeholder="Telefone 2" required="required" value="'. $Phone2 .'" ></td>
                </tr>

                <input type="submit" name="block2" value="Salvar"  />
                </thead> 
			
		  </table>
          
      </form></div>';  
	}
	else
	{
		header('Location: '.site_url().'/index.php/entrar');
	}
} 


function profile_script() { 
?>
<script>  
  function validateform_profile(){  

    var name=document.myform.uname.value;
    var login_name=document.myform.login_name.value;
    var islock=document.myform.islock.value; 
    var password=document.myform.pass.value
    var user_email=document.myform.em.value; 
    var atposition=user_email.indexOf("@");  
    var dotposition=user_email.lastIndexOf(".");  
    var user_phone1=document.myform.user_phone1.value;  
    var user_phone2=document.myform.user_phone2.value;
    if(islock!="Y" && islock!="N")
    {
      alert("You can enter only Y or N "); 
      jQuery('#islock').css({'border-color': 'red'}); 
      return false;

    }
    if (name==null || name=="" || login_name=="" || password=="" || user_email=="" || user_phone1=="" || user_phone2==""){  
      alert("Fields can't be blank"); 
      jQuery('#uname, #pass,#user_email,#user_phone1,#user_phone2').css({'border-color': 'red'}); 
      return false;  
    }

    else if (atposition<1 || dotposition<atposition+2 || dotposition+2>=user_email.length){  
      alert("Please enter a valid e-mail address \n atpostion:"+atposition+"\n dotposition:"+dotposition);  
      jQuery('#user_email').css({'border-color': 'red'});
      return false;  
      } 

    else if(isNaN(user_phone1) || isNaN(user_phone2)){  
      alert("Enter Numeric value only");  
      jQuery('#user_phone1,#user_phone2').css({'border-color': 'red'});
      return false;  
    }
    else if(user_phone1.length<2 || user_phone2.length<2){  
      alert("Phone Number must be at least 10 characters long."); 
       jQuery('#user_phone1,#user_phone2').css({'border-color': 'red'});
      return false;  
      }     


      else{
        return true;  
      } 

  }  
  </script> 

 <?php 
 }
 add_action( 'wp_enqueue_scripts', 'profile_script' );?>
