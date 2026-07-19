<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add SoftDeletes for data safety
        $softDeleteTables = ['users', 'customers', 'products', 'orders', 'service_orders', 'inventories', 'brands'];
        foreach ($softDeleteTables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }

        // 2. Audit Trail for Inventory Transactions
        if (Schema::hasTable('inventory_transactions')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_transactions', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
                }
            });
        }

        // 3. Accounting Uniformity
        if (Schema::hasTable('accounting_services')) {
            Schema::table('accounting_services', function (Blueprint $table) {
                if (Schema::hasColumn('accounting_services', 'service_date')) {
                    $table->renameColumn('service_date', 'transaction_date');
                }
            });
        }
        if (Schema::hasTable('accounting_sales')) {
            Schema::table('accounting_sales', function (Blueprint $table) {
                if (Schema::hasColumn('accounting_sales', 'sale_date')) {
                    $table->renameColumn('sale_date', 'transaction_date');
                }
            });
        }

        // 4. Missing Performance Indexes
        if (Schema::hasTable('orders')) {
            $indexes = Schema::getIndexes('orders');
            $hasIndex = collect($indexes)->contains(fn($index) => $index['name'] === 'orders_service_order_id_index');
            
            if (!$hasIndex && Schema::hasColumn('orders', 'service_order_id')) {
                Schema::table('orders', function (Blueprint $table) {
                    $table->index('service_order_id', 'orders_service_order_id_index');
                });
            }
        }

        if (Schema::hasTable('users')) {
            $indexes = Schema::getIndexes('users');
            $hasIndex = collect($indexes)->contains(fn($index) => $index['name'] === 'users_phone_index');
            
            if (!$hasIndex && Schema::hasColumn('users', 'phone')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->index('phone', 'users_phone_index');
                });
            }
        }

        // 5. Ensure all prices have high precision and proper constraints
        if (Schema::hasTable('products')) {
            // Fix existing null values
            \Illuminate\Support\Facades\DB::table('products')->whereNull('price')->update(['price' => 0]);
            \Illuminate\Support\Facades\DB::table('products')->whereNull('sale_price')->update(['sale_price' => 0]);
            
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('price', 15, 2)->default(0)->change();
                $table->decimal('sale_price', 15, 2)->default(0)->change();
                
                if (Schema::hasColumn('products', 'purchase_price')) {
                    \Illuminate\Support\Facades\DB::table('products')->whereNull('purchase_price')->update(['purchase_price' => 0]);
                    $table->decimal('purchase_price', 15, 2)->default(0)->change();
                }

                // Add missing foreign key for brand if it exists
                if (Schema::hasColumn('products', 'brand_id') && Schema::hasTable('brands')) {
                    $foreignKeys = Schema::getForeignKeys('products');
                    $hasBrandFK = collect($foreignKeys)->contains(fn($fk) => $fk['columns'][0] === 'brand_id');
                    
                    if (!$hasBrandFK) {
                        $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('sale_price', 10, 2)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_phone_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_service_order_id_index');
        });

        if (Schema::hasTable('accounting_sales')) {
            Schema::table('accounting_sales', function (Blueprint $table) {
                if (Schema::hasColumn('accounting_sales', 'transaction_date')) {
                    $table->renameColumn('transaction_date', 'sale_date');
                }
            });
        }

        if (Schema::hasTable('accounting_services')) {
            Schema::table('accounting_services', function (Blueprint $table) {
                if (Schema::hasColumn('accounting_services', 'transaction_date')) {
                    $table->renameColumn('transaction_date', 'service_date');
                }
            });
        }

        if (Schema::hasTable('inventory_transactions')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        $softDeleteTables = ['users', 'customers', 'products', 'orders', 'service_orders', 'inventories', 'brands'];
        foreach ($softDeleteTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
