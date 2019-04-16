<?php
require_once realpath(dirname(__FILE__)."/../common/ZohoOAuthUtil.php");
require_once realpath(dirname(__FILE__)."/../common/ZohoOAuthConstants.php");
require_once realpath(dirname(__FILE__)."/../common/ZohoOAuthParams.php");
require_once realpath(dirname(__FILE__)."/../clientapp/ZohoOAuthPersistenceHandler.php");
require_once realpath(dirname(__FILE__)."/../clientapp/ZohoOAuthPersistenceByFile.php");
require_once realpath(dirname(__FILE__)."/../common/OAuthLogger.php");
require_once 'ZohoOAuthClient.php';

class ZohoOAuth
{

	private static $configProperties =array();
	
	public static function initializeWithOutInputStream()
	{
		self::initialize(false);
	}
	
	public static function initialize($configFilePointer)
	{
		try
		{
			$configPath=realpath(dirname(__FILE__)."/../../../../resources/oauth_configuration.properties");
			$filePointer=fopen($configPath,"r");
			self::$configProperties = ZohoOAuthUtil::getFileContentAsMap($filePointer);
			if($configFilePointer!=false)
			{
				$properties=ZohoOAuthUtil::getFileContentAsMap($configFilePointer);
				foreach($properties as $key=>$value)
				{
					self::$configProperties[$key]=$value;
				}
			}
			//self::$configProperties[ZohoOAuthConstants::IAM_URL]= "https://accounts.zoho.com";
			$oAuthParams=new ZohoOAuthParams();
			
			$oAuthParams->setAccessType(self::getConfigValue(ZohoOAuthConstants::ACCESS_TYPE));
			$oAuthParams->setClientId(self::getConfigValue(ZohoOAuthConstants::CLIENT_ID));
			$oAuthParams->setClientSecret(self::getConfigValue(ZohoOAuthConstants::CLIENT_SECRET));
			$oAuthParams->setRedirectURL(self::getConfigValue(ZohoOAuthConstants::REDIRECT_URL));
			ZohoOAuthClient::getInstance($oAuthParams);
		}
		catch (IOException $ioe)
		{
			OAuthLogger::warn("Exception while initializing Zoho OAuth Client.. ". ioe);
			throw ioe;
		}
	}
	
	public static function getConfigValue($key)
	{
		return self::$configProperties[$key];
	}
	
	public static function getAllConfigs()
	{
		return self::$configProperties;
	}
	
	public static function getIAMUrl()
	{
		return self::getConfigValue(ZohoOAuthConstants::IAM_URL);
	}
	
	public static function getGrantURL()
	{
		return self::getIAMUrl()."/oauth/v2/auth";
	}
	
	public static function getTokenURL()
	{
		return self::getIAMUrl()."/oauth/v2/token";
	}
	
	public static function getRefreshTokenURL()
	{
		return self::getIAMUrl()."/oauth/v2/token";
	}
	
	public static function getRevokeTokenURL()
	{
		return self::getIAMUrl()."/oauth/v2/token/revoke";
	}
	
	public static function getUserInfoURL()
	{
		return self::getIAMUrl()."/oauth/user/info";
	}
	
	public static function getClientID()
	{
		return self::getConfigValue(ZohoOAuthConstants::CLIENT_ID);
	}
	
	public static function getClientSecret()
	{
		return self::getConfigValue(ZohoOAuthConstants::CLIENT_SECRET);
	}
	
	public static function getRedirectURL()
	{
		return self::getConfigValue(ZohoOAuthConstants::REDIRECT_URL);
	}
	
	public static function getAccessType()
	{
		return self::getConfigValue(ZohoOAuthConstants::ACCESS_TYPE);
	}
	
	public static function getPersistenceHandlerInstance()
	{
		return new \App\integrations\zohocrm\ZohoOAuthPersistenceHandler();
		
	}
	
	public static function getClientInstance()
	{
		if(ZohoOAuthClient::getInstanceWithOutParam() == null)
		{
			throw new ZohoOAuthException("ZohoOAuth.initializeWithOutInputStream() must be called before this.");
		}
		return ZohoOAuthClient::getInstanceWithOutParam();
	}
	
}
?>