<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** Tabel yang dipantau otomatis oleh trigger database (khusus PostgreSQL/Supabase). */
    private array $audited = ['mitras', 'bookings', 'cats', 'profiles'];

    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->string('actor_id')->nullable()->index();
            $t->string('actor_name')->nullable();
            $t->string('actor_role', 40)->nullable();
            // auth | user | data | transaction | admin
            $t->string('category', 20)->index();
            $t->string('action', 60);
            $t->string('subject_type', 60)->nullable();
            $t->string('subject_id')->nullable();
            $t->text('description')->nullable();
            $t->jsonb('old_values')->nullable();
            $t->jsonb('new_values')->nullable();
            $t->string('source', 20)->default('app'); // app | database
            $t->string('ip_address', 45)->nullable();
            $t->timestamp('created_at')->useCurrent()->index();
        });

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Tabel publik di Supabase terbuka lewat REST API; kunci dengan RLS tanpa policy.
        // Laravel (koneksi langsung sebagai role postgres) tetap bisa membaca/menulis.
        DB::unprepared('ALTER TABLE public.activity_logs ENABLE ROW LEVEL SECURITY');

        DB::unprepared(<<<'SQL'
CREATE OR REPLACE FUNCTION public.log_row_change() RETURNS trigger
LANGUAGE plpgsql SECURITY DEFINER SET search_path = public AS $$
DECLARE
    v_cat   text;
    v_actor text;
    v_name  text;
    v_role  text;
    v_old   jsonb;
    v_new   jsonb;
    v_id    text;
    v_label text;
    v_verb  text;
    v_act   text;
BEGIN
    -- Dilewati bila aksi sudah dicatat oleh aplikasi (lihat updatePetshopStatus).
    IF current_setting('app.skip_audit', true) = '1' THEN
        RETURN NULL;
    END IF;

    IF TG_OP = 'UPDATE' AND to_jsonb(OLD) = to_jsonb(NEW) THEN
        RETURN NULL;
    END IF;

    v_cat := CASE TG_TABLE_NAME
        WHEN 'bookings' THEN 'transaction'
        WHEN 'profiles' THEN 'user'
        ELSE 'data'
    END;

    BEGIN
        v_actor := auth.uid()::text;
    EXCEPTION WHEN OTHERS THEN
        v_actor := NULL;
    END;

    IF v_actor IS NOT NULL THEN
        SELECT coalesce(full_name, username), role INTO v_name, v_role
        FROM public.profiles WHERE id::text = v_actor;
    END IF;

    IF TG_OP = 'DELETE' THEN
        v_old := to_jsonb(OLD); v_new := NULL;        v_id := OLD.id::text;
        v_verb := 'Menghapus'; v_act := 'deleted';
    ELSIF TG_OP = 'UPDATE' THEN
        v_old := to_jsonb(OLD); v_new := to_jsonb(NEW); v_id := NEW.id::text;
        v_verb := 'Mengubah'; v_act := 'updated';
    ELSE
        v_old := NULL;          v_new := to_jsonb(NEW); v_id := NEW.id::text;
        v_verb := 'Menambahkan'; v_act := 'created';
    END IF;

    v_label := coalesce(
        coalesce(v_new, v_old)->>'name',
        coalesce(v_new, v_old)->>'full_name',
        coalesce(v_new, v_old)->>'username',
        v_id
    );

    INSERT INTO public.activity_logs
        (actor_id, actor_name, actor_role, category, action, subject_type, subject_id,
         description, old_values, new_values, source, created_at)
    VALUES
        (v_actor, v_name, v_role, v_cat, v_act, TG_TABLE_NAME, v_id,
         v_verb || ' data ' || TG_TABLE_NAME || ' "' || v_label || '"',
         v_old, v_new, 'database', now());

    RETURN NULL;
END;
$$;
SQL);

        foreach ($this->audited as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            DB::unprepared("DROP TRIGGER IF EXISTS trg_audit ON public.{$table}");
            DB::unprepared("CREATE TRIGGER trg_audit AFTER INSERT OR UPDATE OR DELETE ON public.{$table} FOR EACH ROW EXECUTE FUNCTION public.log_row_change()");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            foreach ($this->audited as $table) {
                if (Schema::hasTable($table)) {
                    DB::unprepared("DROP TRIGGER IF EXISTS trg_audit ON public.{$table}");
                }
            }
            DB::unprepared('DROP FUNCTION IF EXISTS public.log_row_change()');
        }

        Schema::dropIfExists('activity_logs');
    }
};