<?php

namespace Modules\Admin\Console;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Action;
use Modules\Admin\Entities\Role;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AddAdminRole extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'addRole:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add role admin to first user.';

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
        //если нет, то добавляем роль admin в таблицу roles
        $role_id = Role::firstOrCreate(['code'=>'admin','name'=>'Администратор']);
        if($role_id){
            $this->info("Role admin added!");
        }
        $role_id = Role::where(['code'=>'admin'])->first()->id;
        $actions = Action::firstOrCreate(['code'=>'admin','name'=>'Полный доступ']);
        if($actions){
            $this->info("Actions admin added!");
        }
        $action_id = Action::where(['code'=>'admin'])->first()->id;
        $role_user = DB::table('role_user')->insert(['role_id'=>$role_id,'user_id'=>1, 'created_at'=>date('Y-m-d H:i:s')]);
        $action_role = DB::table('action_role')->insert(['action_id'=>$action_id, 'role_id'=>$role_id, 'created_at'=>date('Y-m-d H:i:s')]);
        if($role_user && $action_role){
            $this->info("All links in tables are added.");
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
