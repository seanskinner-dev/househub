public function up()
{
    Schema::table('students', function (Blueprint $table) {
        $table->foreignId('house_id')->nullable()->constrained()->nullOnDelete();
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropForeign(['house_id']);
        $table->dropColumn('house_id');
    });
}