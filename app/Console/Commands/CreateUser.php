<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $details = $this->getDetails();

        $data = [
            'role_id' => 1,
            'name' => $details['name'],
            'email' => $details['email'],
            'password' => bcrypt($details['password']),
            'status' => 1
        ];
        $admin = User::create($data);

        $this->display($admin);
    }

    private function getDetails(): array
    {
        $details['name'] = $this->ask('Name');
        $details['email'] = $this->ask('Email');
        $details['password'] = $this->secret('Password');
        $details['confirm_password'] = $this->secret('Confirm password');

        while (!$this->isValidPassword($details['password'], $details['confirm_password'])) {
            if (!$this->isRequiredLength($details['password'])) {
                $this->error('Password must be more that six characters');
            }

            if (!$this->isMatch($details['password'], $details['confirm_password'])) {
                $this->error('Password and Confirm password do not match');
            }

            $details['password'] = $this->secret('Password');
            $details['confirm_password'] = $this->secret('Confirm password');
        }

        return $details;
    }

    private function display(User $admin): void
    {
        $headers = ['Name', 'Email'];

        $fields = [
            'name' => $admin->name,
            'email' => $admin->email,
        ];

        $this->info('user created');
        $this->table($headers, [$fields]);
    }

    private function isValidPassword(string $password, string $confirmPassword): bool
    {
        return $this->isRequiredLength($password) &&
            $this->isMatch($password, $confirmPassword);
    }

    private function isMatch(string $password, string $confirmPassword): bool
    {
        return $password === $confirmPassword;
    }

    private function isRequiredLength(string $password): bool
    {
        return strlen($password) > 6;
    }
}
