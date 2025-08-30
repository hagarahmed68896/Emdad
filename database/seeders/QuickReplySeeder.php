<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuickReply;


class QuickReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

public function run()
{
    $replies = [
        ['text' => 'ما هي خيارات التغليف المتاحة؟', 'answer' => 'لدينا عدة خيارات تغليف حسب حجم الطلب.'],
        ['text' => 'كيف سيتم الشحن؟', 'answer' => 'يتم الشحن عن طريق شركات الشحن المحلية والدولية.'],
        ['text' => 'كم يستغرق التوصيل إلى مدينتي؟', 'answer' => 'عادة التوصيل يستغرق 3-5 أيام عمل.'],
        ['text' => 'ما هي طرق الدفع المتوفرة؟', 'answer' => 'يمكن الدفع نقداً عند الاستلام أو عن طريق البطاقة الائتمانية.'],
        ['text' => 'هل يوجد عينة للاختبار؟', 'answer' => 'نعم، يمكن طلب عينة صغيرة قبل الشراء.']
    ];

    foreach ($replies as $reply) {
        QuickReply::create([
            'text' => $reply['text'],
            'answer' => $reply['answer'],
            'active' => true
        ]);
    }
}


}
