<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nikah_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Basic info
            $table->integer('age');
            $table->string('height')->nullable(); // e.g. "5'8""
            $table->enum('marital_status', ['never_married', 'divorced', 'widowed', 'separate', 'married'])->default('never_married');
            $table->string('sect')->nullable();
            $table->string('caste')->nullable();

            // Education & profession
            $table->string('education')->nullable();
            $table->string('profession')->nullable();
            $table->string('city');
            $table->string('country')->default('Pakistan');

            // Family info
            $table->string('family_type')->nullable(); // joint/nuclear
            $table->string('guardian_name');
            $table->string('guardian_contact');
            $table->string('guardian_relation')->nullable(); // father/brother/etc

            // About
            $table->text('about')->nullable();
            $table->text('expectations')->nullable();

            // Verification & media
            $table->string('photo')->nullable(); // path, hidden until accepted
            $table->string('cnic_number')->nullable();
            $table->string('cnic_front_image')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            // Visibility & status
            $table->enum('visibility', ['public', 'private'])->default('public');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nikah_profiles');
    }
};
