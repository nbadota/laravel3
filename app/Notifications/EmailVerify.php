<?php
namespace App\Notifications;
 
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
 
/**
 * 邮件验证
 *
 * @author  
 *
 */
class EmailVerify extends Notification implements ShouldQueue
{
	use Queueable;
 
	protected $code;
 
	protected $minutes;
 
	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($code, $minutes = 10)
	{
		$this->code = $code;
		$this->minutes = $minutes;
	}
 
	/**
	 * Get the notification's delivery channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return [
			'mail'
		];
	}
 
	/**
	 * Get the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{
		/*$content = view('notifications.verify', [
			'code' => $this->code,
			'minutes' => $this->minutes
		])->render();
		return (new MailMessage())->line($content);*/
             return (new MailMessage)
                ->greeting('你好！这是一封验证码邮件，如非本人操作，请忽略。')
                ->line("你的验证码为  $this->code")
                ->line("验证码的有效时间为  $this->minutes 分钟");
	}
 
	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [];
	}
}
