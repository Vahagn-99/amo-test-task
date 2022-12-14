<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('price');
            $table->boolean('is_deleted');
            $table->foreignIdFor(\App\Models\Company::class,
                'company_id')->nullable()->constrained()->on('companies')->cascadeOnDelete();
            $table->unsignedBigInteger('lead_id')->unique();
            $table->timestamps();
            // add indexes
            $table->index(['lead_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
