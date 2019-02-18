<?php ob_start();
add_shortcode('puser-registration', 'puser_registration');
function puser_registration() 
{
  $email_verify = substr(str_shuffle("ABCDEabcdefg0123456789hijklmnopqrstuvwxyzFGHIJKLMNOPQRS0123456789TUVWXYZ"), -20);

  $verification_code = substr(str_shuffle("ABCDEabcdefg0123456789hijklmnopqrstuvwxyzFGHIJKLMNOPQRS0123456789TUVWXYZ"), -4);
require_once('general-setting.php');
  $verifurl=site_url()."/index.php/validacao/";
  //----------------------- C_B_Partners ------------------------------------------
     $url=$model_servic_url;
      $partner_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
       <soapenv:Header/>
       <soapenv:Body>
          <_0:queryData>
             <_0:ModelCRUDRequest>
                <_0:ModelCRUD>
                   <_0:serviceType>wp-queryBP</_0:serviceType>
                    <_0:DataRow>
                      <!--Zero or more repetitions:-->
                      <!-- if you want to show a certain bpartner group
                      <_0:field column="C_BP_Group_ID">
                         <_0:val>'.$c_BP_Group_ID.'</_0:val>
                      </_0:field>
                      <_0:field column="AD_OrgBP_ID">
                        <_0:val></_0:val>
                      </_0:field>
                    -->
                   </_0:DataRow>
                </_0:ModelCRUD>
                '.$login_request.'
             </_0:ModelCRUDRequest>
          </_0:queryData>
       </soapenv:Body>
    </soapenv:Envelope>';
      
        $soap_dopartner = curl_init(); 

        curl_setopt($soap_dopartner, CURLOPT_URL,            $url );   
        curl_setopt($soap_dopartner, CURLOPT_CONNECTTIMEOUT, 10); 
        curl_setopt($soap_dopartner, CURLOPT_TIMEOUT,        10); 
        curl_setopt($soap_dopartner, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_dopartner, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($soap_dopartner, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($soap_dopartner, CURLOPT_POST,           true ); 
        curl_setopt($soap_dopartner, CURLOPT_POSTFIELDS,    $partner_string); 
        curl_setopt($soap_dopartner, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($partner_string) )); 
       
        $resultpartner = curl_exec($soap_dopartner);        

      $part = xml_parser_create();
      xml_parse_into_struct($part, $resultpartner, $valspart, $index);
      xml_parser_free($part);
      
    
      $furniture_arr= array();
      $f=0;
      foreach ($valspart as $key => $valu) 
      {
        @$ty=@$valu['attributes']['COLUMN'];

        if($ty == "C_BPartner_ID")
        {
          $k=$key;
          $furniture_arr[$f]['attributes']= $valu['attributes']['COLUMN'];           
          $furniture_arr[$f]['partner_id']= $valspart[$k+1]['value'];  
          $furniture_arr[$f++]['partner_name']= $valspart[$k+4]['value'];  
        }
      }
      
     $err = curl_error($soap_dopartner);
     curl_close($soap_dopartner);
  //----------------------- C_B_Partners ------------------------------------------

  if(isset($_POST['reg']))

  {
    session_start();

   $_SESSION['uname']= $name=$_POST['uname']; 
     $_SESSION['login_name']= $login_name=$_POST['login_name'];
     $_SESSION['upassword']= $upassword=$_POST['upassword'];
     $_SESSION['em']= $em= $_POST['em'];
     $_SESSION['user_phone1']= $user_phone1=$_POST['user_phone1'];
     $_SESSION['user_phone2']= $user_phone2=$_POST['user_phone2'];
     $email_verify = $_POST['email_verify'];
    $cb_partner=$_POST['cb_partner'];
     if($cb_partner==0)
     {
      echo "<p class='error'>Please select partner.</p>";
     }
    if($name!='' && $upassword!='' && $em!='' && $user_phone1!='' && $user_phone2!='' && $cb_partner!=0)

    {
       $url=$composit_servic_url;
       $post_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:_0="http://idempiere.org/ADInterface/1_0">
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
                        <_0:field column="C_BPartner_ID">
                              <_0:val>'. $cb_partner .'</_0:val>
                            </_0:field>
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
                            <_0:field column="EMailVerify">
                              <_0:val>'. $email_verify .'</_0:val>
                            </_0:field>    
                            <_0:field column="IsLocked">
                              <_0:val>Y</_0:val>
                            </_0:field>   
                      </_0:DataRow>
                        </_0:ModelCRUD>
                     </_0:operation>
                     
                     <_0:operation>
                        <_0:TargetPort>createData</_0:TargetPort>
                         <!--Add new user to role -External Partner User- 1000000-->
                        <_0:ModelCRUD>
                        <_0:serviceType>wp-AddUserRole</_0:serviceType>
                        <_0:DataRow>
                            <_0:field column="AD_User_ID">
                            <_0:val>@AD_User.AD_User_ID</_0:val>
                            </_0:field>                 
                            <_0:field column="AD_Role_ID">
                            <_0:val>' .$p_AD_Role_ID.'</_0:val>
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
                           <_0:val>'.$p_R_RequestType_ID.'</_0:val>
                        </_0:field>                 
                        <_0:field column="R_Category_ID">
                           <_0:val>'.$p_R_Category_ID.'</_0:val>
                        </_0:field>
                        <_0:field column="Summary">
                        <_0:val>Dear '. $name .'
Thank you for registering with us
To complete the registration process, you will be required to verify your email by
visiting this link: '.$verifurl.'?username='.$login_name.'&amp;code='.$email_verify.'</_0:val>
                        </_0:field>
                        <_0:field column="SalesRep_ID">
                           <_0:val>'.$p_SalesRep_ID.'</_0:val>
                        </_0:field>
                        <_0:field column="C_BPartner_ID">
                           <_0:val>'. $cb_partner .'</_0:val>
                        </_0:field>
                        <_0:field column="AD_Table_ID">
                           <_0:val>114</_0:val>
                        </_0:field>
                        <_0:field column="Record_ID">
                           <_0:val>@AD_User.AD_User_ID</_0:val>
                        </_0:field>                  
                        <_0:field column="AD_User_ID">
                           <_0:val>@AD_User.AD_User_ID</_0:val>
                        </_0:field> 
                        <_0:field column="Priority">
                           <_0:val>3</_0:val>
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
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $post_string); 
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($post_string) )); 

        $result = curl_exec($soap_do);
        
        echo "<br/>";
        
        echo $result;

      $p = xml_parser_create();
      xml_parse_into_struct($p, $result, $vals, $index);
      xml_parser_free($p);

      @$output= @$vals[5]['attributes']['RECORDID'];
      @$newuser_role_id= @$vals[6]['attributes']['RECORDID'];
      @$wel_request_id= @$vals[7]['attributes']['RECORDID'];
      @$uid=$vals[5]['attributes']['RECORDID'];
      //------------- same email but differnt login name ------
      $sameemail_error=$vals[7]['tag'];
      //------------- differnt email but same login name ------
      $samelogin_error=$vals[6]['tag'];
      echo "<br/>";

      $err = curl_error($soap_do);

      print_r($err);  

      curl_close($soap_do);
      //---------------- Verification -----------------
      $url=$model_servic_url;
      $verfy_string='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                      xmlns:_0="http://idempiere.org/ADInterface/1_0">
                      <soapenv:Header/>
                      <soapenv:Body>
                          <_0:queryData>
                              <_0:ModelCRUDRequest>
                                  <_0:ModelCRUD>
                                      <_0:serviceType>wp-queryUsers</_0:serviceType>
                                      <_0:DataRow>
                                          <_0:field column="EMail">
                                              <_0:val>'. $em .'</_0:val>
                                          </_0:field>
                                      </_0:DataRow>
                                  </_0:ModelCRUD>'.$login_request.'</_0:ModelCRUDRequest>
                          </_0:queryData>
                      </soapenv:Body>
                  </soapenv:Envelope>';
       $soap_doverify = curl_init(); 

        curl_setopt($soap_doverify, CURLOPT_URL,            $url );   
        curl_setopt($soap_doverify, CURLOPT_CONNECTTIMEOUT, 10); 
        curl_setopt($soap_doverify, CURLOPT_TIMEOUT,        10); 
        curl_setopt($soap_doverify, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_doverify, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($soap_doverify, CURLOPT_SSL_VERIFYHOST, FALSE); 
        curl_setopt($soap_doverify, CURLOPT_POST,           true ); 
        curl_setopt($soap_doverify, CURLOPT_POSTFIELDS,    $verfy_string); 
        curl_setopt($soap_doverify, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($verfy_string) )); 

        $result_verify = curl_exec($soap_doverify);
        
        echo "<br/>";
        
      $pver = xml_parser_create();
      xml_parse_into_struct($pver, $result_verify, $valsverify, $index);
      xml_parser_free($pver);

      $verfication_status=$valsverify[34]['value'];

      curl_close($soap_doverify);
      //---------------- End Verification -----------------
        if($output!='' && $newuser_role_id!='' && $wel_request_id!='')

        {
            echo "<p class='success'>Thank You for registration.Please check your Email</p>";
            session_destroy(); 
        } 
        
        if($sameemail_error=="ERROR")
        {
          echo "<p class='error'>Email ID is already in use please choose a different email id.</p>";
          echo'<style type="text/css">#ema{border-color:red;}</style>';
        } 
        else if($output=='' && $newuser_role_id=='' && $wel_request_id=='')

        {
            echo "<p class='error'>Record contains an error, please check your data.</p>";
        }
        else if($samelogin_error=="ERROR")
        {
          echo "<p class='error'>User name is already in use please choose a different username .</p>"; 
          echo'<style type="text/css">#login_name{border-color:red;}</style>';
        }
  }  

 }  


  $HTML='<form  id="rg" name="myform" action=""  method="POST"  onsubmit="return validateform_reg();">
 <Table> <thead> <tr><th colspan="2" style="text-align: center; padding: 35px 0px;background:#007acc; color:#fff;text-transform: uppercase;font-size: 32px;"> Registro de Usuário <br></th></tr> 
       </thead> 
				<tr>	
                    <td>Parceiro de Negócios </td>
                    <td><select name="cb_partner" id="cb_partner"><option value="0">Selecione o Parceiro</option>';
        foreach ($furniture_arr as $pkey13 => $privacycountry_value) 
        {
            $HTML.='<option value="'. $privacycountry_value['partner_id'] .'">'. $privacycountry_value['partner_name'].'</option>';
        } 
        $HTML.='</select></td>
                </tr>
                <tr>	
                    <td> Nome Completo</td>
                    <td> <input type="text" name="uname" id="uname" placeholder="Nome Completo" value="'. @$_SESSION['uname'] .'" required="required" ></td>
                </tr>
                <tr>	
                    <td> Usuário</td>
                    <td><input type="text" required="required" name="login_name" id="login_name" placeholder="Usuário" value="'. @$_SESSION['login_name'] .'" > </td>
                </tr>
                <tr>	
                    <td> Senha</td>
                    <td> <input type="password" required="required" name="upassword" id="pass" placeholder="Senha" value="'. @$_SESSION['upassword'] .'" ></td>
                </tr>
                <tr>	
                    <td> Email</td>
                    <td> <input type="text" id="ema" name="em" placeholder="Email" value="'. @$_SESSION['em'] .'" required="required" ></td>
                </tr>
                <tr>	
                    <td> Telefone 1</td>
                    <td> <input type="text" id="user_phone1" name="user_phone1" placeholder="Telefone 1" required="required" value="'. @$_SESSION['user_phone1'] .'" ></td>
                </tr>
                <tr>	
                    <td> Telefone 2</td>
                    <td> <input type="text" id="user_phone2" name="user_phone2" placeholder="Telefone 2" required="required" value="'. @$_SESSION['user_phone2'] .'" ></td>
                </tr>
                <tr>
                    <td colspan="2">
                    <input type="hidden" name="email_verify" value="'. $email_verify .'" readonly>
                    <input type="submit" name="reg" value="Registrar"  />
                    </td>
                </tr>
			
		  </table>
      </form>';

  echo $HTML;
}

function brokerreg_script() {

?>
<script>  
  function validateform_brokerreg(){  

    var name=document.myform.uname.value;
    var login_name=document.myform.login_name.value;  
    var password=document.myform.pass.value
    var user_email=document.myform.em.value; 
    var atposition=user_email.indexOf("@");  
    var dotposition=user_email.lastIndexOf(".");  
    var user_phone1=document.myform.user_phone1.value;  
    var user_phone2=document.myform.user_phone2.value;
    
    if (name==null || name=="" || login_name=="" || password=="" || user_email=="" || user_phone1=="" || user_phone2==""){  
      alert("Fields can't be blank"); 
      jQuery('#uname, #pass,#user_email,#user_phone1,#user_phone2').css({'border-color': 'red'}); 
      return false;  
    }
    else if(name=="")
    {
      alert("User name can't be blank"); 
      jQuery('#uname').css({'border-color': 'red'}); 
      return false; 
    }
    else if(login_name=="")
    {
      alert("Login Name can't be blank"); 
      jQuery('#login_name').css({'border-color': 'red'}); 
      return false; 
    }
    else if(password=="")
    {
      alert("Password can't be blank"); 
      jQuery('#password').css({'border-color': 'red'}); 
      return false; 
    }
     else if(user_phone1=="")
    {
      alert("Phone1 can't be blank"); 
      jQuery('#user_phone1').css({'border-color': 'red'}); 
      return false; 
    }
     else if(user_phone2=="")
    {
      alert("Phone2 can't be blank"); 
      jQuery('#user_phone2').css({'border-color': 'red'}); 
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
add_action( 'wp_enqueue_scripts', 'brokerreg_script' );
?> 
