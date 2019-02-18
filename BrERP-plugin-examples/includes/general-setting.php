<?php  
$model_servic_url="http://teste.devcoffee.com.br/ADInterface/services/ModelADService?wsdl";
$composit_servic_url="http://teste.devcoffee.com.br/ADInterface/services/compositeInterface?wsdl";
$auth_user_name="superuser @ brerp.com.br";
$auth_user_pass="sua_senha";
$login_request='<_0:ADLoginRequest>
                <_0:user>'. $auth_user_name .'</_0:user>
			    <_0:pass>'. $auth_user_pass .'</_0:pass>
			    <_0:RoleID>1000000</_0:RoleID>
			    <_0:lang>128</_0:lang>
			    <_0:ClientID>1000000</_0:ClientID>
			    <_0:OrgID>5000003</_0:OrgID>
			    <_0:WarehouseID>5000007</_0:WarehouseID>
			    <_0:stage>0</_0:stage>
			    </_0:ADLoginRequest>';

$p_AD_Role_ID=1000000; // the AD_Role_ID of any new users registered
// Request related parameters, for the request created upon a new user registration
$p_R_RequestType_ID=5000000; 
$p_R_Category_ID=5000000;
$p_SalesRep_ID=101;
$c_BP_Group_ID=1000001;

//User Login Settings
$user_login_R_RequestType_ID=5000000;
$user_login_R_Category_ID=5000000;
$user_login_SalesRep_ID=1000000;
$user_login_C_BPartner_ID=1000004;
$user_login_AD_Table_ID=114;


//User Profile Settings
$user_profile_ad_user_id=1000000;
?>
