<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_code')->unique(); // bpevent-xxx
            $table->string('name');
            $table->date('date');
            $table->decimal('price', 10, 2);
            $table->string('location');
            $table->string('type'); // online, offline, hybrid
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('available_slots')->nullable();
            $table->integer('registered_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};