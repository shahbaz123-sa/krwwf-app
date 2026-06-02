<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
            $table->index('user_id', 'idx_user_id');
        });
    }
    public function down()
    {
        Schema::dropIfExists('chatbot_conversations');
    }
};

