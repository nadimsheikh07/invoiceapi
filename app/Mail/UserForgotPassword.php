<?php

namespace App\Mail;

use App\Helpers\MyPasswordHelper;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class UserForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = User::where('email', $this->email)->first();

        $password = MyPasswordHelper::make(10);

        $user->update([
            'password' => Hash::make($password)
        ]);

        return $this->to($this->email)->from(env('MAIL_USERNAME'))->view('mail/forgotPassword')->with([
            'name' => $user->name,
            'password' => $password
        ]);
    }
}
