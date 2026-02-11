<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' => '123456',
            'permission_level' => User::PERMISSION_ADMIN,
            'active' => true,
        ]);

        $manager = User::create([
            'name' => 'Gerente Silva',
            'email' => 'manager@test.com',
            'password' => '123456',
            'permission_level' => User::PERMISSION_MANAGER,
            'active' => true,
        ]);

        $user = User::create([
            'name' => 'João Santos',
            'email' => 'user@test.com',
            'password' => '123456',
            'permission_level' => User::PERMISSION_USER,
            'active' => true,
        ]);

        $menu = config('menu', []);

        Order::create([
            'user_id' => $user->_id,
            'mesa' => 2,
            'items' => [
                ['id' => 'prato_1', 'name' => 'Espaguete à Bolonhesa', 'quantity' => 2, 'unit_price' => 32.00],
                ['id' => 'refri_1', 'name' => 'Coca-Cola', 'quantity' => 2, 'unit_price' => 8.00],
            ],
            'status' => Order::STATUS_PENDING,
            'notes' => 'Sem cebola no espaguete.',
        ]);

        Order::create([
            'user_id' => $user->_id,
            'mesa' => 5,
            'items' => [
                ['id' => 'prato_4', 'name' => 'Penne ao Molho Pesto', 'quantity' => 1, 'unit_price' => 35.00],
                ['id' => 'drink_3', 'name' => 'Cerveja Artesanal', 'quantity' => 2, 'unit_price' => 18.00],
            ],
            'status' => Order::STATUS_PROCESSING,
        ]);

        Order::create([
            'user_id' => $manager->_id,
            'mesa' => 1,
            'items' => [
                ['id' => 'prato_2', 'name' => 'Lasanha à Bolonhesa', 'quantity' => 3, 'unit_price' => 38.00],
                ['id' => 'refri_4', 'name' => 'Suco Natural', 'quantity' => 3, 'unit_price' => 12.00],
            ],
            'status' => Order::STATUS_COMPLETED,
        ]);

        echo "✅ Usuários e pedidos de exemplo criados!\n\n";
        echo "=== CREDENCIAIS ===\n";
        echo "Administrador: admin@test.com / 123456 (acesso total; edita usuários; relatórios)\n";
        echo "Gerente: manager@test.com / 123456 (cria usuários; relatórios; não edita usuários)\n";
        echo "Usuário: user@test.com / 123456 (cadastra pedidos)\n";
    }
}
