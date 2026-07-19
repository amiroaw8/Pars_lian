<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\ServiceOrderStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define templates for missing statuses
        $templates = [
            ServiceOrderStatus::ACCOUNTING->value => 'مشتری گرامی، دستگاه شما تعمیر و جهت صدور فاکتور به حسابداری ارجاع شد. پس از تایید نهایی اطلاع‌رسانی می‌گردد. پارس لیان',
            ServiceOrderStatus::REJECTED->value => 'مشتری گرامی، متاسفانه دستگاه شما قابل تعمیر نمی‌باشد. جهت تحویل دستگاه به مرکز مراجعه نمایید. پارس لیان',
            ServiceOrderStatus::ARCHIVED->value => 'مشتری گرامی، سفارش #{id} بایگانی شد. با تشکر از انتخاب شما. پارس لیان',
        ];

        foreach ($templates as $id => $template) {
            // Update using id column which matches enum value
            DB::table('service_order_statuses')
                ->where('id', $id)
                ->update(['sms_template' => $template]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $ids = [
            ServiceOrderStatus::ACCOUNTING->value,
            ServiceOrderStatus::REJECTED->value,
            ServiceOrderStatus::ARCHIVED->value,
        ];

        DB::table('service_order_statuses')
            ->whereIn('id', $ids)
            ->update(['sms_template' => null]);
    }
};
