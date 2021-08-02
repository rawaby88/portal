<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Rawaby88\Portal\MigrationIndex;

class AlterPortalUsersTable extends Migration
{
	use MigrationIndex;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary();
	        $table->unsignedInteger('id');
	        $table->dropColumn('id');
	        $table->uuid('user_id')->primary();
	        $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
	        $table->dropPrimary();
	        $table->dropColumn('user_id');
	        $table->string('name')->change();
        });
    }
}
