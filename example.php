<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ua_Auth extends Controller {
	public function action_login()
	{
		if ($_POST) {
			$roles = array('member', 'user');
			
			foreach ($roles as $role) {
				$auth = TAuth::instance($role);
				$remember = isset($_POST['remember']) && $_POST['remember'];
				$auth->login(Arr::get($_POST, 'username'), Arr::get($_POST, 'password'), $remember);
				
				if ($auth->logged_in())
					break;
			}
			
			if ( $auth->logged_in() ) {
				HTTP::redirect('/ua');
			} else {
				$error = 'Неправильный логин/пароль';
			}
		}

		$view = View::factory('/ua/signin/index');
		$view->error = $error;
		$this->response->body( $view->render() );
		
	}
	
	public function action_forgotpwd()
	{
		$error = '';

		if ($_POST) {
			$roles = array('member');
			
			foreach ($roles as $role) {
				$user = TAuth::instance($role)->load_user($_POST['email']);

				if ($user->loaded())
				{
					$password = HZ::generate_password(8);
					$user->set('password',$password);
					$user->save();

					$config = Kohana::$config->load('email');
					email::connect($config);

					$to = $_POST['email'];
					$subject = 'Восстановление пароля в системе CRM Учебный Центр';
					$from = 'info@k-crm.kz';
					$message = View::factory('user/messagepwd');
					$message->login = $user->email;
					$message->password = $password;
					$message->render();

					email::send($to, $from, $subject, $message, $html = true);

					$error = 'На указанный e-mail отправлено письмо с паролем';

					break;
				}
				else
				{
					$error = 'Указанный e-mail отсутствует в системе.';
				}
			}
		}

		$view = View::factory('/ua/signin/forgotpwd');
		$view->error = $error;

		$this->response->body( $view );
	}
	
}
