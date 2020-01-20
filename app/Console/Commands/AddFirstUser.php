<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AddFirstUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addFirst:admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adding the first login for admin';

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
     * @return mixed
     */
    public function handle()
    {
        //проверяем, что в таблице users нет записей
        $count = User::all()->count();
        if($count){
            $this->info("There are already users in the database");
        }
        else{
            User::create(['active'=>1,'login'=>'admin','name'=>'Administrator','email'=>$this->argument('email'),'password' => Hash::make('12345678')]);
            $this->info('New administrator added with login admin and password 12345678');
        }
    }
}
