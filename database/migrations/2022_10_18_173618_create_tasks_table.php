<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->date('deadline')->nullable()->default(NULL);
            $table->text('description')->nullable()->default(NULL);
            $table->unsignedBigInteger('owner_id');
            $table->unsignedBigInteger('assignee_id')->nullable()->default(NULL);
            $table->enum('status', ['prepared', 'in_progress', 'in_test', 'done'])->nullable()->default(NULL);
            $table->timestamps();

            $table->foreign('owner_id')
                ->on('users')
                ->references('id')
                ->onDelete('RESTRICT');

            $table->foreign('assignee_id')
                ->on('users')
                ->references('id')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
