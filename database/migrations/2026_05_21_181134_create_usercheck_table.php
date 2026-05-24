<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usercheck', function (Blueprint $table) {
            $table->id('id');
            $table->integer('uid');
            $table->integer('cat')->default(0);
            $table->integer('scat')->default(0);
            $table->integer('brand')->default(0);
            $table->integer('prod')->default(0);
            $table->integer('mprod')->default(0);
            $table->integer('purc')->default(0);
            $table->integer('mpurc')->default(0);
            $table->integer('astock')->default(0);
            $table->integer('slist')->default(0);
            $table->integer('sprice')->default(0);
            $table->integer('cinv')->default(0);
            $table->integer('minv')->default(0);
            $table->integer('linvc')->default(0);
            $table->integer('quot')->default(0);
            $table->integer('mquot')->default(0);
            $table->integer('estm')->default(0);
            $table->integer('mestm')->default(0);
            $table->integer('ord')->default(0);
            $table->integer('sord')->default(0);
            $table->integer('dord')->default(0);
            $table->integer('cord')->default(0);
            $table->integer('expen')->default(0);
            $table->integer('expd')->default(0);
            $table->integer('agent')->default(0);
            $table->integer('apay')->default(0);
            $table->integer('areport')->default(0);
            $table->integer('breport')->default(0);
            $table->integer('sreport')->default(0);
            $table->integer('preport')->default(0);
            $table->integer('stockr')->default(0);
            $table->integer('phistory')->default(0);
            $table->integer('excel')->default(0);
            $table->integer('auser')->default(0);
            $table->integer('usett')->default(0);
            $table->integer('csett')->default(0);
            $table->integer('backup')->default(0);
            $table->integer('restore')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usercheck');
    }
};