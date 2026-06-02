<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('sender', ['user', 'bot', 'admin']);
            $table->text('message');
            $table->string('source_type', 100)->nullable();
            $table->json('source_ids')->nullable();
            $table->tinyInteger('is_helpful')->nullable();
            $table->timestamps();
            $table->foreign('conversation_id')
                ->references('id')->on('chatbot_conversations')
                ->onDelete('cascade');
            $table->index('conversation_id', 'idx_conversation_id');
            $table->index('user_id', 'idx_user_id');
            $table->index('sender', 'idx_sender');
        });
    }
    public function down()
    {
        Schema::dropIfExists('chatbot_messages');
    }
};

