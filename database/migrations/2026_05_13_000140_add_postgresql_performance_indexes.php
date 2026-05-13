<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE INDEX organizations_metadata_gin_idx ON organizations USING GIN (metadata)');
        DB::statement('CREATE INDEX users_metadata_gin_idx ON users USING GIN (metadata)');
        DB::statement('CREATE INDEX hotels_metadata_gin_idx ON hotels USING GIN (metadata)');
        DB::statement('CREATE INDEX bookings_metadata_gin_idx ON bookings USING GIN (metadata)');
        DB::statement('CREATE INDEX payments_gateway_response_gin_idx ON payments USING GIN (gateway_response)');
        DB::statement('CREATE INDEX audit_logs_payload_gin_idx ON audit_logs USING GIN (payload)');

        DB::statement("CREATE INDEX rooms_available_lookup_idx ON rooms (hotel_id, room_type_id, room_number) WHERE status = 'available' AND deleted_at IS NULL");
        DB::statement("CREATE INDEX bookings_active_stay_idx ON bookings (hotel_id, check_in_date, check_out_date) WHERE booking_status IN ('pending', 'confirmed', 'checked_in') AND deleted_at IS NULL");
        DB::statement('CREATE INDEX notifications_unread_user_idx ON notifications (user_id, created_at DESC) WHERE read_at IS NULL');
        DB::statement('CREATE INDEX audit_logs_recent_idx ON audit_logs (organization_id, created_at DESC)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS audit_logs_recent_idx');
        DB::statement('DROP INDEX IF EXISTS notifications_unread_user_idx');
        DB::statement('DROP INDEX IF EXISTS bookings_active_stay_idx');
        DB::statement('DROP INDEX IF EXISTS rooms_available_lookup_idx');
        DB::statement('DROP INDEX IF EXISTS audit_logs_payload_gin_idx');
        DB::statement('DROP INDEX IF EXISTS payments_gateway_response_gin_idx');
        DB::statement('DROP INDEX IF EXISTS bookings_metadata_gin_idx');
        DB::statement('DROP INDEX IF EXISTS hotels_metadata_gin_idx');
        DB::statement('DROP INDEX IF EXISTS users_metadata_gin_idx');
        DB::statement('DROP INDEX IF EXISTS organizations_metadata_gin_idx');
    }
};
