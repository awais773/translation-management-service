<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('key');
            $table->text('value');
            $table->foreignId('locale_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['group', 'key', 'locale_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('translations');
    }
};