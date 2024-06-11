<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApprovableNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('approvable-notifications.table_name', 'approvable_notifications'), function (Blueprint $table) {
            $table->increments('id');
            $table->nullableMorphs('notifier');
            $table->nullableMorphs('notifiable');
            $table->nullableMorphs('actionable');
            $table->string('title')->nullable();
            $table->string('message')->nullable();
            $table->json('data')->nullable();
            $table->enum('status', [0, 1, 2])->default(1)->comment('[0: Rejected, 1: Pending, 2: Approved]');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(config('approvable-notifications.table_name', 'approvable_notifications'));
    }
}
