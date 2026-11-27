<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_locale_to_indonesian_and_back_to_english(): void
    {
        $response = $this->from('/')->post(route('locale.switch'), [
            'locale' => 'id',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('locale', 'id');
        $response->assertCookie(SetLocale::COOKIE_NAME, 'id');

        $loginPageId = $this->withSession(['locale' => 'id'])->get('/');
        $loginPageId->assertStatus(200);
        $loginPageId->assertSee('Login ke');
        $loginPageId->assertSee('Masuk ke Akun');
        $loginPageId->assertSee(route('locale.switch'));

        $switchBack = $this->from('/')->post(route('locale.switch'), [
            'locale' => 'en',
        ]);

        $switchBack->assertRedirect('/');
        $switchBack->assertSessionHas('locale', 'en');
        $switchBack->assertCookie(SetLocale::COOKIE_NAME, 'en');

        $loginPageEn = $this->withSession(['locale' => 'en'])->get('/');
        $loginPageEn->assertStatus(200);
        $loginPageEn->assertSee('Sign in to');
        $loginPageEn->assertSee('Sign In to Account');
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $response = $this->from('/')->post(route('locale.switch'), [
            'locale' => 'fr',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('locale');
    }

    public function test_indonesian_locale_persists_via_cookie_without_session(): void
    {
        $response = $this->withCookie(SetLocale::COOKIE_NAME, 'id')->get('/tracking');

        $response->assertStatus(200);
        $response->assertSee('Lacak Kendaraan');
        $response->assertSee('Layanan Logistik Standar Internasional');
        $response->assertSee(route('locale.switch'));
    }

    public function test_indonesian_translation_and_switcher_work_across_all_views(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $carrier = Carrier::create([
            'code' => 'WWL',
            'name' => 'Wallenius Wilhelmsen',
            'service_type' => 'RORO_SEA',
        ]);
        $origin = Location::create([
            'code' => 'IDTPP',
            'name' => 'Jakarta Car Terminal',
            'type' => 'PORT',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'latitude' => -6.0967,
            'longitude' => 106.8858,
        ]);
        $destination = Location::create([
            'code' => 'NLRTM',
            'name' => 'Rotterdam EV Hub',
            'type' => 'PORT',
            'city' => 'Rotterdam',
            'country' => 'Netherlands',
            'latitude' => 51.9054,
            'longitude' => 4.4666,
        ]);
        $shipment = Shipment::create([
            'shipment_number' => 'CT-2026-RORO-9001',
            'customer_id' => $customer->id,
            'carrier_id' => $carrier->id,
            'origin_id' => $origin->id,
            'destination_id' => $destination->id,
            'cargo_type' => 'CBU_EV',
            'total_weight_kg' => 2100,
            'total_volume_cbm' => 14.5,
            'expected_departure' => now(),
            'expected_arrival' => now()->addDays(10),
            'status' => Shipment::STATUS_IN_TRANSIT,
        ]);

        // Guest views in Indonesian
        $this->withSession(['locale' => 'id'])->get('/register')
            ->assertStatus(200)
            ->assertSee('Daftar Akun')
            ->assertSee('Daftar Sekarang')
            ->assertSee(route('locale.switch'))
            ->assertDontSee('ui.');

        $this->withSession(['locale' => 'id'])->get('/tracking')
            ->assertStatus(200)
            ->assertSee('Lacak Kendaraan')
            ->assertSee(route('locale.switch'))
            ->assertDontSee('ui.');

        // Authenticated views in Indonesian & English (no missing translation keys)
        $authenticatedChecks = [
            route('dashboard') => ['Selamat Datang di Dasbor Otomotif CargoTrace', 'Dasbor Operasi Admin', 'Pengiriman Kendaraan'],
            route('shipments.index') => ['Daftar Pengiriman Kendaraan', 'Pemesanan Kendaraan Baru'],
            route('shipments.create') => ['Pemesanan Konsinyasi Kendaraan Baru', 'Konfirmasi & Buat Pengiriman Kendaraan'],
            route('shipments.show', $shipment) => ['Kronologi Riwayat Pelacakan', 'Manifes Unit Kendaraan'],
            route('shipments.edit', $shipment) => ['Ubah Parameter Pengiriman', 'Simpan Perubahan'],
            route('carriers.index') => ['Mitra Ekspedisi Otomotif', 'Tambah Mitra Ekspedisi'],
            route('locations.index') => ['Hub Otomotif, Pelabuhan Ro-Ro', 'Tambah Titik Logistik'],
            route('users.index') => ['Pengguna & Kontrol Akses Peran', 'Tambah Akun Pengguna'],
            route('audit_logs.index') => ['Jejak Audit Keamanan', 'Otorisasi Tata Kelola Admin'],
            route('notifications.index') => ['Pusat Notifikasi', 'Tandai Semua Sudah Dibaca'],
            route('settings.index') => ['Pengaturan Sistem & Akun', 'Pengaturan Umum'],
        ];

        foreach ($authenticatedChecks as $url => $expectedTexts) {
            $resId = $this->actingAs($admin)->withSession(['locale' => 'id'])->get($url);
            $resId->assertStatus(200);
            $resId->assertSee(route('locale.switch'));
            $resId->assertSee('lang="id"', false);
            $resId->assertDontSee('ui.');

            foreach ($expectedTexts as $text) {
                $resId->assertSee($text);
            }

            $resEn = $this->actingAs($admin)->withSession(['locale' => 'en'])->get($url);
            $resEn->assertStatus(200);
            $resEn->assertSee('lang="en"', false);
            $resEn->assertDontSee('ui.');
        }
    }
}
