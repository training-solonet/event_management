<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // bpevent-872862287
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('nik');
            $table->text('address');
            $table->string('payment_method');
            $table->string('payment_status')->default('pending'); // pending, paid, verified
            $table->string('payment_proof')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('wa_notification_sent')->default(false);
            $table->boolean('email_notification_sent')->default(false);
            $table->timestamps();
            
            $table->index(['nik', 'email']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('participants');
    }
};