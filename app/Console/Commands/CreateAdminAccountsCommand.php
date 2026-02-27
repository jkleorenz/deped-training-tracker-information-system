<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminAccountsCommand extends Command
{
    protected $signature = 'users:create-admin-accounts
                            {--password= : Password for both accounts (default: password)}
                            {--force : Update existing accounts (e.g. reset password)}';

    protected $description = 'Create or update the Admin and Mini Admin (Sub-Admin) accounts.';

    public function handle(): int
    {
        $password = $this->option('password') ?: 'password';
        $force = $this->option('force');

        $accounts = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@deped.local',
                'role' => User::ROLE_ADMIN,
                'label' => 'Admin',
            ],
            [
                'name' => 'Sub-Administrator',
                'email' => 'subadmin@deped.local',
                'role' => User::ROLE_SUB_ADMIN,
                'label' => 'Mini Admin (Sub-Admin)',
                'extra' => [
                    'employee_id' => 'SUBADMIN001',
                    'designation' => 'Sub-Administrator',
                    'department' => 'HRDD',
                    'school' => 'DepEd Division Office',
                ],
            ],
        ];

        foreach ($accounts as $data) {
            $extra = $data['extra'] ?? [];
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                if (! $force) {
                    $this->line("  [<comment>exists</comment>] {$data['label']}: {$data['email']}");
                    continue;
                }
                $user->password = Hash::make($password);
                $user->name = $data['name'];
                $user->role = $data['role'];
                foreach ($extra as $key => $value) {
                    $user->{$key} = $value;
                }
                $user->save();
                $this->info("  [updated] {$data['label']}: {$data['email']}");
            } else {
                User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($password),
                    'role' => $data['role'],
                    ...$extra,
                ]);
                $this->info("  [created] {$data['label']}: {$data['email']}");
            }
        }

        $this->newLine();
        $this->info('Admin and Mini Admin accounts are ready.');
        $this->line('  Admin:       admin@deped.local');
        $this->line('  Mini Admin:  subadmin@deped.local');
        $this->line('  Password:    ' . $password);
        $this->newLine();

        return self::SUCCESS;
    }
}
