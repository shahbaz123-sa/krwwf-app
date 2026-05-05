<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('member_id')->nullable()->unique()->after('profile_picture');
            $table->string('location')->nullable()->after('member_id');
            $table->string('profession')->nullable()->after('location');
            $table->string('company')->nullable()->after('profession');
            $table->string('experience')->nullable()->after('company');
            $table->text('skills')->nullable()->after('experience');
            $table->string('role_in_community')->nullable()->after('skills');
            $table->string('blood_group')->nullable()->after('role_in_community');
            $table->text('interests')->nullable()->after('blood_group');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'member_id',
                'location',
                'profession',
                'company',
                'experience',
                'skills',
                'role_in_community',
                'blood_group',
                'interests',
            ]);
        });
    }
};

