<?php ob_start();
add_shortcode('user-login', 'user_login');

function user_login() {
$verification_code = substr(str_shuffle("ABCDEabcdefg0123456789!@#$%^&*()hijklmnopqrstuvwxyzFGHIJKLMNOPQRS0123456789!@#$%^&*()TUVWXYZ"), -20);
$verifurl=site_url()."/index.php/validacao/";

require_once('general-setting.php');

  if(isset($_POST['user_login']))

  {

    $name=$_POST['umail']; 
    $upassword=$_POST['upassword'];

    if($name!='' || $upassword!='')

    {

        // ============= for get currently added user ================
        $url= $model_servic_url;

      if(strrchr($name,"@") && strrchr($name,"."))
      {
        $post_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                        xmlns:_0="http://idempiere.org/ADInterface/1_0">
                        <soapenv:Header/>
                        <soapenv:Body>
                              <_0:queryData>
                                 <_0:ModelCRUDRequest>
                                    <_0:ModelCRUD>
                                          <_0:serviceType>wp-queryUsers</_0:serviceType>
                                          <_0:DataRow>
                                             <_0:field column="EMail">
                                                <_0:val>'. $name .'</_0:val>
                                             </_0:field>
                                          </_0:DataRow>
                                    </_0:ModelCRUD>'.$login_request.'</_0:ModelCRUDRequest>
                              </_0:queryData>
                        </soapenv:Body>
                     </soapenv:Envelope>';
      }
      else
      {
        $post_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
           <soapenv:Header/>
           <soapenv:Body>
              <_0:queryData>
                 <_0:ModelCRUDRequest>
                    <_0:ModelCRUD>
                       <_0:serviceType>wp-queryUsers</_0:serviceType>
                        <_0:DataRow>
                          <!--Zero or more repetitions:-->
                           <_0:field column="Value">
                            <_0:val>'. $name .'</_0:val>
                          </_0:field>
                       </_0:DataRow>
                    </_0:ModelCRUD>
                    '.$login_request.'
                 </_0:ModelCRUDRequest>
              </_0:queryData>
           </soapenv:Body>
        </soapenv:Envelope>';
      }

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
                elseif ($listvalue['column']=="EMail" )
                     $getemail=$listvalue['column_value']; 
                elseif ($listvalue['column']=="Value" )
                     $getuser_name=$listvalue['column_value']; 
                 elseif ($listvalue['column']=="EMailVerify" )
                     $getemail_verify=$listvalue['column_value'];                    
     		    elseif ($listvalue['column']=="Name" )
                     $getname=$listvalue['column_value'];
                elseif ($listvalue['column']=="Password" )
                     $getpass=$listvalue['column_value'];
                elseif ($listvalue['column']=="IsLocked" )
                     $getverify_status=$listvalue['column_value'];
	     }      
    
      $err = curl_error($soap_do);
     
     curl_close($soap_do);
      if($name == $getemail || $name == $getuser_name)
      { 
        if($upassword == $getpass)

        { 
          if($getverify_status=="N" || $getverify_status=="n")
          {  
           session_start();
           $_SESSION['admin_user']=$getemail;
           $_SESSION['admin_user_id']=$getid;
            header('Location: '.site_url().'/index.php/perfil-do-usuario');
          }
          else
            {
              //-----------send verification mail --------------------
              $verifurl=site_url()."/index.php/validacao?username=".$getuser_name;
              $welcome_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                                 xmlns:_0="http://idempiere.org/ADInterface/1_0">
                                 <soapenv:Header/>
                                 <soapenv:Body>
                                       <_0:createData>
                                          <_0:ModelCRUDRequest>
                                             <_0:ModelCRUD>
                                                   <_0:serviceType>wp-userRQ</_0:serviceType>
                                                   <_0:DataRow>
                                                      <_0:field column="R_RequestType_ID">
                                                         <_0:val>'. $user_login_R_RequestType_ID .'</_0:val>
                                                      </_0:field>
                                                      <_0:field column="R_Category_ID">
                                                         <_0:val>'. $user_login_R_Category_ID .'</_0:val>
                                                      </_0:field>
                                                      <_0:field column="Summary">
                                                         <_0:val>Caro '. $getname .', Obrigado por se registrar. Por favor, clique no link para validar o registo: \n
                                             '. $verifurl .'&code='. $getemail_verify .'</_0:val>
                                                      </_0:field>
                                                      <_0:field column="SalesRep_ID">
                                                         <_0:val>'. $user_login_SalesRep_ID .'</_0:val>
                                                      </_0:field>
                                                      <!--
                                                               <_0:field column="C_BPartner_ID">
                                                                  <_0:val>'. $user_login_C_BPartner_ID .'</_0:val>
                                                               </_0:field>
                                                               -->
                                                      <_0:field column="AD_Table_ID">
                                                         <_0:val>'. $user_login_AD_Table_ID .'</_0:val>
                                                      </_0:field>
                                                      <_0:field column="Record_ID">
                                                         <_0:val>'. $getid . '</_0:val>
                                                      </_0:field>
                                                      <_0:field column="AD_User_ID">
                                                         <_0:val>'. $getid . '</_0:val>
                                                      </_0:field>
                              
                                                   </_0:DataRow>
                                             </_0:ModelCRUD>
                                                         '.$login_request.'
                                          </_0:ModelCRUDRequest>
                                       </_0:createData>
                                 </soapenv:Body>
                              </soapenv:Envelope>';
              $soap_dowel = curl_init(); 

              curl_setopt($soap_dowel, CURLOPT_URL,            $url );   
              curl_setopt($soap_dowel, CURLOPT_CONNECTTIMEOUT, 10); 
              curl_setopt($soap_dowel, CURLOPT_TIMEOUT,        10); 
              curl_setopt($soap_dowel, CURLOPT_RETURNTRANSFER, true );
              curl_setopt($soap_dowel, CURLOPT_SSL_VERIFYPEER, FALSE);  
              curl_setopt($soap_dowel, CURLOPT_SSL_VERIFYHOST, FALSE); 
              curl_setopt($soap_dowel, CURLOPT_POST,           true ); 
              curl_setopt($soap_dowel, CURLOPT_POSTFIELDS,    $welcome_string); 
              curl_setopt($soap_dowel, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($welcome_string) )); 

              $resultwel = curl_exec($soap_dowel);
              
              echo "<br/>";
              

              $pwel = xml_parser_create();
              xml_parse_into_struct($pwel, $resultwel, $valswel, $index);
              xml_parser_free($pwel);

              $welcome_output= $valswel[3]['attributes']['RECORDID'];

              if($welcome_output!='')
              {
                echo "<p class='success'>Usuário não validado. Por favor, verifique seu e e-mail e complete o processo de validação.</p>";
              }  
              
            }  
        } 
        else{echo "<p class='error'>Please try again or reset password</p>";}
      }  
      else { echo "<p class='error'>You are not registered.Please register first</p>";}
    }  
    else{ echo "<p class='error'>Please enter valid Email or Password </p>";}

  } 

  if(isset($_POST['reset']))
  {
    $loginurl=site_url()."/index.php/entrar/";

    $reset_pass = substr(str_shuffle("ABCDEabcdefg0123456789!@#$%^&*()hijklmnopqrstuvwxyzFGHIJKLMNOPQRS0123456789!@#$%^&*()TUVWXYZ"), -5);

    $url=$composit_servic_url;

    $email=$_POST['umail']; 
   $resetpost_string= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
   <soapenv:Header/>
   <soapenv:Body>
      <_0:compositeOperation>
         <_0:CompositeRequest>
            '.$login_request.'
            <_0:serviceType>wp-compositeWrapper</_0:serviceType>
            <!--1 or more repetitions:-->
            <_0:operations>
               <!--1 or more repetitions:-->
               <_0:operation preCommit="false" postCommit="false">
                  <_0:TargetPort>createUpdateData</_0:TargetPort>
                  <!--Optional:-->
                  <_0:ModelCRUD>
                  <_0:serviceType>wp-createUpdateUser</_0:serviceType>
                  <_0:DataRow>
                        <_0:field column="Password">
                           <_0:val>'. $reset_pass .'</_0:val>
                        </_0:field>
                        <_0:field column="EMail">
                           <_0:val>'. $email .'</_0:val>
                        </_0:field>
                        <_0:field column="IsExpired">
                     		<_0:val>Y</_0:val>
                  		</_0:field>                         
                  </_0:DataRow>
                   </_0:ModelCRUD>
               </_0:operation>
               
               <_0:operation>
                  <_0:TargetPort>createData</_0:TargetPort>
                  <!--Optional:-->
                   <_0:ModelCRUD>
               <_0:serviceType>wp-userRQ</_0:serviceType>
                <_0:DataRow>
                  <_0:field column="R_RequestType_ID">
                     <_0:val>'. $user_login_R_RequestType_ID .'</_0:val>
                  </_0:field>                 
                  <_0:field column="R_Category_ID">
                     <_0:val>'. $user_login_R_Category_ID .'</_0:val>
                  </_0:field>
                  <_0:field column="Summary">
                     <_0:val>Dear User
We have received your request to reset your password and it has 
been temporarily reset to "'. $reset_pass .'"
Please login to your account through the application and reset
'. $loginurl .'</_0:val>
                  </_0:field>
                  <_0:field column="SalesRep_ID">
                     <_0:val>'. $user_login_SalesRep_ID .'</_0:val>
                  </_0:field>
                  <_0:field column="C_BPartner_ID">
                     <_0:val>'. $user_login_C_BPartner_ID .'</_0:val>
                  </_0:field>
                  <_0:field column="AD_Table_ID">
                     <_0:val>'. $user_login_AD_Table_ID .'</_0:val>
                  </_0:field>
                  <_0:field column="Record_ID">
                     <_0:val>@AD_User.AD_User_ID</_0:val>
                  </_0:field>                  
                  <_0:field column="AD_User_ID">
                     <_0:val>@AD_User.AD_User_ID</_0:val>
                  </_0:field> 
                                    
                 </_0:DataRow>
            </_0:ModelCRUD>
               </_0:operation>
            </_0:operations>
         </_0:CompositeRequest>
      </_0:compositeOperation>
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
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $resetpost_string); 
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($resetpost_string) )); 
      
        $result = curl_exec($soap_do);
        
        echo "<br/>";

      $p = xml_parser_create();
      xml_parse_into_struct($p, $result, $vals, $index);
      xml_parser_free($p);
    
      $output= $vals[5]['attributes']['RECORDID'];
      
       if($output!='')

        { 

            $to="$email";
           
            $from="Admin";
            $subject="Reset Password";
            // Create email headers

             $headers = array('Content-Type: text/html; charset=UTF-8','From: admin ');

            $message = '<html><body>';
            $message .= '<h2>Your new password is: </h2><p>'.$reset_pass.'</p>Thank You for registration. Please click on the URL: <a href="'.$loginurl.'" style="color:blue" >Click For Login</a> ';
            $message .= '</body></html>';
            if(wp_mail($to, $subject, $message , $headers))

            {
               echo "<p class='success'>Thank You. Please check your Email</p>";
            }
        } 
        else{ echo "<p class='error'>Please enter registered Email</p>";}
  }
  $HTML='<form  id="rg"  class="block1" name="loginform" action=""  method="POST"  onsubmit="return validateform_login();">
           
          <input type="text" required="required" name="umail" placeholder="User Name or Email" value=""><br>
          <input type="password" required="required" name="upassword" id="pass" placeholder="Password" value="" ><br/>           

          <div>
            <input type="submit" name="user_login" value="Login"  />
            <div class="nr"><a href="javascript:void(0)" id="block2" > Reset Password</a></div>
          </div>
      </form>';

echo $HTML;
echo  $resetHTML='<form  id="rg"  class="block2" name="loginform" action=""  method="POST"  onsubmit="return validateform_login();">
           
          <input type="text" required="required" name="umail" placeholder="Email" value=""><br>
          <div>
            <input type="submit" name="reset" value="Reset"  />
          </div>
      </form>';
}  
?>
<?php function our_script() {?>
<script>
//----------------validation ----------------
  function validateform_login(){     
    var password=document.loginform.upassword.value
    var user_email=document.loginform.umail.value; 
    var atposition=user_email.indexOf("@");  
    var dotposition=user_email.lastIndexOf(".");  
        
    if (password=="" || user_email==""){  
      alert("Fields can't be blank"); 
      jQuery('#uname, #pass,#user_email,#user_phone1,#user_phone2').css({'border-color': 'red'}); 
      return false;  
    }

    else if (atposition<1 || dotposition<atposition+2 || dotposition+2>=user_email.length){  
      alert("Please enter a valid e-mail address \n atpostion:"+atposition+"\n dotposition:"+dotposition);  
      jQuery('#user_email').css({'border-color': 'red'});
      return false;  
      } 

    else{
        return true;  
    } 

  }  

</script> 
<?php 
}
add_action( 'wp_enqueue_scripts', 'our_script' );
?>
