<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact');
            $table->string('email')->unique();
            $table->string('pancard')->nullable();
            $table->string('gstin')->nullable();
            $table->text('address');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_type');
            $table->string('swift_code')->nullable();
            $table->string('ifsc_code');
            $table->text('bank_address');
            $table->text('sign_image')->nullable();
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('companies');
    }
}
