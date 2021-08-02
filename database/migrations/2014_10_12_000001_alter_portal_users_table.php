<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPortalUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public
	function up ()
	{
		Schema::table( 'users', function ( Blueprint $table ) {
			$table->dropColumn( 'id' );
			$table->uuid( 'user_id' )->first()->primary();
			$table->string( 'name' )->nullable()->change();
			$table->dropColumn('password');
		} );
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public
	function down ()
	{
		Schema::table( 'users', function ( Blueprint $table ) {
			$table->dropColumn( 'user_id' );
			$table->id()->first();
			$table->string( 'name' )->change();
			$table->string('password');
		} );
	}
}
