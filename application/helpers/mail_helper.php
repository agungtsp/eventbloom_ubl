<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function sent_mail($email,$config){
	 $CI=& get_instance();
	 if(USE_API_EXPERIAN){
	 	$CI->load->library('cheetah_library');
		$send = $CI->cheetah_library->sent_email_ebm($email);
		return $send;
	 } else {

		 if(!$config){
			  $config = $CI->db->get('email_config')->row_array();
		 }
		 require_once 'mail/class.phpmailer.php';
		 $mail             = new PHPMailer();
		 if($config['type']=='smtp'){
			 $mail->IsSMTP();
			 $mail->Host       = $config['smtp_host'];
			 $mail->SMTPDebug  = 0;
			 $mail->SMTPAuth   = true;
			 $mail->Port       = $config['port'] ;
			 $mail->Username   = $config['smtp_user'];
			 $mail->Password   = $config['smtp_pass'];
			 $mail->SMTPSecure = '';
			 //$mail->SMTPSecure = "tls";
		 }
		 else{
			  $mail->IsSendmail();
			  //$mail->Sendmail = 'D:/xampp/sendmail/sendmail.exe -t';
			  if($config['sendmail_path']){
				   $mail->Sendmail = $config['sendmail_path'];
			  }
		 }
		 
		 
		 $mail->SetFrom($config['smtp_user_alias'],$config['smtp_user_from']); //buat replynya
		 //$mail->AddReplyTo('agungiskandar@gmail.com','agungs');
		 $mail->Subject    = $email['subject'];
		 $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		 $mail->MsgHTML($email['content']);
		 $emails = is_array($email['to']) ? $email['to'] : explode(',',$email['to']);
		 foreach ($emails as $e) {
			 $mail->AddAddress($e);
		 }
		 
		 if(!$mail->Send()) {
			 $ret['error'] = 1;
			 $ret['message'] = $mail->ErrorInfo;
		 }
		 else{
			 $ret['error'] 	= 0;
			 $to = is_array($email['to']) ? implode(',',$email[to]) : $email[to];
			 $ret['message']	= "email terkirim ke $to";
		 }
		 return $ret;
	}
}

function mail_tpt($id,$replace=array()){
	 $CI 					= & get_instance();
	 $tpt					= $CI->db->get_where('email_tpt',"id = '$id'")->row_array();
	 $data['judul']			= $tpt['subject'];
	 $data['content'] 		= $tpt['content'];
	 foreach($replace as $id=>$val){
		  $data['judul']	= str_replace('{'.$id.'}',$val,$data['judul']);
		  $data['content']	= str_replace('{'.$id.'}',$val,$data['content']);
	 }
	 //print_r($data);
	 //die();
	 return $data;
}
