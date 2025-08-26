<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employee_votes', function (Blueprint $table) {
            $table->id();
            $table->string('fullName'); // ناوی تەواوی کارمەند
            $table->string('mobile'); // ژ.مۆبایل
            $table->string('address'); // ناونیشانی نیشتەجێبوون
            $table->string('card_number'); // ژمارەی کارت
            $table->string('unit_office'); // یەکە/فەرمانگە
            $table->string('base'); // بنکە
            // $table->integer('base_id'); // بنکە
            $table->integer('base_id')->nullable()->comment('بنکەی هەلبژاردن');
            $table->integer('circle_id')->nullable()->comment('بازنەی هەلبژاردن');
            $table->integer('user_id')->nullable(); // ناوی بەکارهێنەر

            $table->boolean('is_election')->default(0); // ئایادەنگی داوە ؟
            $table->string('note')->nullable(); // تێبینی/هۆکاری دەنگ نەدان
            $table->timestamp('datetime')->nullable(); // کاتی دەنگدان
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_votes');
    }
};
