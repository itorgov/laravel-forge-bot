<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('message_id')->comment('Telegram\'s message ID to update.');
            $table->foreignId('token_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->unsignedBigInteger('server_id')->nullable()->comment('Laravel Forge\'s server ID.');
            $table->string('server_name')->nullable()->comment('Laravel Forge\'s server name.');
            $table->unsignedBigInteger('site_id')->nullable()->comment('Laravel Forge\'s site ID.');
            $table->string('site_name')->nullable()->comment('Laravel Forge\'s site name.');
            $table->string('waiting_message_for')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
