<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrateur;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
   
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            // Créer l'utilisateur
            $user = User::create([
                'name' => $this->argument('name'),
                'email' => $this->argument('email'),
                'role' => User::ROLE_ADMIN,
            ]);

           
            $admin = Administrateur::create([
                'user_id' => $user->id,
                'password' => bcrypt($this->argument('password')),
                
            ]);

            DB::commit();

            $this->info("Administrator {$user->name} created successfully.");
            $this->info("Email: {$user->email}");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to create administrator: " . $e->getMessage());
        }
    }
    }

