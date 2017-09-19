<?php

/*********************************************************
 * Name:		Twitch API
 * Version:		v0.1-alpha
 * Author:		Gaizka González Graña (gaizka.gzgr@gmail.com)
 * License:		MIT (https://github.com/Gaizka-gzgr/Multiple-Options-Open-Source-Toolkit-MOOST-/blob/master/LICENSE)
 * Copyright 2017 - i++
 ************************************/

class Twitch_functions
{	

	################################################################################################
	############################# API VARS #########################################################
	################################################################################################
	private $api_oauth2_url 	= 'https://api.twitch.tv/kraken/oauth2/authorize?response_type=code';
	private $api_client_id 		= 'YOUR CLIENT ID';
	private $api_client_secret	= 'YOUR CLIENT SECRET';
	private $api_redirect_uri 	= 'YOUR REDIRECT URL, U NEED SET IT ON TWITCH AND HERE';
	private $api_scope 			= 'user_read+user_follows_edit+channel_read';	
	private $api_code;
	private $api_access_token;
	private $api_user_id;
	private $api_channel_id;
	################################################################################################
	################################################################################################

	public function __construct()
	{
		//We can't auto start gettoken() because we need get the code first from link redirect.
	}
	
	//We use this function for create a link for login to twitch and get the autorization code. U can use this function on the template page.
	public function login_template()
	{
		echo '
			<a class="login-twitch" href="'.$this->api_oauth2_url.'&client_id='.$this->api_client_id.'&redirect_uri='.$this->api_redirect_uri.'&scope='.$this->api_scope.'">Login</a><br/>
			';	
	}
	
	//Now, once we got the code from the last link, we make a login function for get token, user information and channel information.
	public function login()
	{	
		$this->api_code = $_GET["code"]; //We get the code from url.
		$this->gettoken(); //Get autorization code.
		$this->getuser(); //User information
		$this->getchannel(); //Channel from user information
		$_SESSION['user_logged'] = 1; //OPTIONAL: U need a var like this if you want appear/disappear login button on index.
		header("Location: /"); //OPTIONAL: Redirect to home for dont see the code in url and diferent things. 
	}
	
	//We need a function for get autorization token for make calls and get information.
	public function gettoken()
	{
		//Url var for make the call.
		$api_url = "https://api.twitch.tv/kraken/oauth2/token";
		
		//Start cUrl
		$ch = curl_init();
		$timeout = 60;
		
		//Options
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE, //	TRUE, for HTTP POST petition
			CURLOPT_SSL_VERIFYPEER => FALSE, // FALSE, Dont verify peer.
			CURLOPT_SSL_VERIFYHOST => 2,	//SSL
			CURLOPT_URL => $api_url,
			CURLOPT_POSTFIELDS => array(
				'client_id' => $this->api_client_id,
				'client_secret' => $this->api_client_secret,
				'grant_type' => 'authorization_code',
				'redirect_uri' => $this->api_redirect_uri,
				'code' => $this->api_code,				
			),
			CURLOPT_RETURNTRANSFER => TRUE, //TRUE for curl_exec work!
		));
		
		//Result & Close
		$result = curl_exec($ch);
		curl_close($ch);
		
		//Decrypt result json 
		$data = json_decode($result);	

		//We set our local variable to the access_toked obtained.
		$this->api_access_token = $data->access_token;
	}
	
	//Once we got the autorization code, we can get information from logged user.
	public function getuser()
	{
		//Url var for make the call.
		$api_url = "https://api.twitch.tv/kraken/user";
		
		//Start cUrl
		$ch = curl_init();
		$timeout = 60;
		
		//Otions		
		curl_setopt_array($ch, array(
			CURLOPT_HTTPGET => TRUE, //	TRUE. for HTTP GET petition
			CURLOPT_SSL_VERIFYPEER => FALSE, // FALSE, Dont verify peer.
			CURLOPT_SSL_VERIFYHOST => 2,	//SSL
			CURLOPT_URL => $api_url,
			CURLOPT_HTTPHEADER => array(
			   'Client-ID: ' . $this->api_client_id,
			   'Authorization: OAuth ' .$this->api_access_token,
				),
			CURLOPT_RETURNTRANSFER => TRUE, //TRUE for curl_exec work!
		));
		
		///Result & Close
		$result = curl_exec($ch);
		curl_close($ch);
		
		//Decrypt result json 
		$data = json_decode($result);
		
		//OPTIONAL: U can use var_dump($data) for get the full information list from json, I only save id, name, logo and email.
		//I use $_SESSION for use it on my needed page. We can make a nav bar with user information.
		$this->api_user_id = $data->_id; //Twitch user ID
		$_SESSION['user'] = $data->display_name; //Twitch display user name
		$_SESSION['logo'] = $data->logo; //Twitch user logo
		$_SESSION['uid'] = $data->_id;	
		$_SESSION['email'] = $data->email; //Twitch user email
	}
	
	//We use this function for get information channel from logged user
	public function getchannel()
	{
		//Url var for make the call.
		$api_url = "https://api.twitch.tv/kraken/channel";
		
		//Start cUrl
		$ch = curl_init();
		$timeout = 60;
		
		//Options	
		curl_setopt_array($ch, array(
			CURLOPT_HTTPGET => TRUE, //	TRUE. for HTTP GET petition
			CURLOPT_SSL_VERIFYPEER => FALSE, // FALSE, Dont verify peer.
			CURLOPT_SSL_VERIFYHOST => 2,	//SSL
			CURLOPT_URL => $api_url,
			CURLOPT_HTTPHEADER => array(
			   'Client-ID: ' . $this->api_client_id,
			   'Authorization: OAuth ' .$this->api_access_token,
				),
			CURLOPT_RETURNTRANSFER => TRUE, //TRUE for curl_exec work!
		));
		
		//Result & Close
		$result = curl_exec($ch);
		curl_close($ch);
		
		//Decrypt result json
		$data = json_decode($result);
		
		//OPTIONAL: U can use var_dump($data) for get the full information list from json, I only save status, views, followers and current game.
		//I use $_SESSION for use it on my needed page. We can make a nav bar with channel information.	
		$_SESSION['status'] = $data->status;
		$_SESSION['views'] = $data->views;
		$_SESSION['followers'] = $data->followers;	
		$_SESSION['game'] = $data->game;
		
	}
	
	//We use this function for get the status channel we need. $channel is the user name from Twitch, for example : TesT124_EU
	public function getstatuschannel($channel)
	{
		//Url var for make the call.
		$api_url = "https://api.twitch.tv/kraken/streams/$channel";
		
		//Start cUrl
		$ch = curl_init();
		$timeout = 60;
		
		//Options	
		curl_setopt_array($ch, array(
			CURLOPT_HTTPGET => TRUE, //	TRUE. for HTTP GET petition
			CURLOPT_SSL_VERIFYPEER => FALSE, // FALSE, Dont verify peer.
			CURLOPT_SSL_VERIFYHOST => 2,	//SSL
			CURLOPT_URL => $api_url,
			CURLOPT_HTTPHEADER => array(
				   'Client-ID: ' . $this->api_client_id
				),
			CURLOPT_RETURNTRANSFER => TRUE, //TRUE for curl_exec work!
		));
		
		//Result & Close
		$result = curl_exec($ch);
		curl_close($ch);
		
		//Decrypt result json
		$data = json_decode($result);
			
		
		//OPTIONAL: U can use var_dump($data) for get the full information list from json, I only save current game, viewers, video height, fps and status.
		//I use $_SESSION for use it on my needed page. We can use it on request channel information page.	
		
		$info = array();
		if(!empty($data->stream)){ //If we have information we set it.
			$info[0] = '<img src="images/online.png">'; //I use a online icon.
			$info[1] = $data->stream->game; //Curent game of user playing in channel.
			$info[2] = '<i class="fa fa-eye" aria-hidden="true"></i> '.$data->stream->viewers; //Viewers of channel. I used too Bootstrap for eye icon.
			$info[3] = ''.$data->stream->video_height.'px@'.number_format($data->stream->average_fps).'fps'; //Video height and fps from stream
			$info[4] = $data->stream->channel->status; //Status of channel.
		}
		else //If we don't have information, the channel is offline.
			$info[0] = '<img src="images/offline.png">'; //Offline icon.

		//Return the last array.
		return $info;
	}
}


?>