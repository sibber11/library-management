<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\CheckOut;
use App\Models\Member;
use App\Models\Reservation;
use Database\Seeders\ReservationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->actingAs(\App\Models\User::factory()->admin()->create());
    }

    public function test_table_can_be_created()
    {
        $this->assertTrue(true);
    }

    public function test_table_can_be_seeded()
    {
        // $this->seed(ReservationSeeder::class);
        Reservation::factory()->create();
        $count = Reservation::count();
        $this->assertGreaterThan(0, $count);
    }

    /**
     * test the index action
     */
    public function test_index()
    {
        $response = $this->get(route('reservations.index'));
        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('Admin/Reservation/Index')
                ->has('reservations')
        );
    }


    /**
     * test the store action
     */
    public function test_store()
    {
        $member = Member::factory()->create();
        $book = Book::factory()->create();
        $response = $this->post(route('reservations.store'), [
            'member_id' => $member->id,
            'book_id' => $book->id,
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'member_id' => $member->id,
            'book_id' => $book->id,
            'status' => 'pending'
        ]);
    }


    /**
     * test the update action
     */
    public function test_update()
    {
        $reservation = Reservation::factory()->create();
        $response = $this->put(route('reservations.update', $reservation), [
            'status' => 'canceled',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'canceled',
        ]);
    }

    /**
     * test the destroy action
     */
    public function test_destroy()
    {
        $reservation = Reservation::factory()->create();
        $response = $this->delete(route('reservations.destroy', $reservation));
        $response->assertStatus(302);
        $response->assertRedirect(route('reservations.index'));
        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation->id,
        ]);
    }

    /**
     * test the reservation
     */
    public function test_reservation_is_reserved_on_checkin()
    {
        $checkOut = CheckOut::factory()->create([
            'is_checked_in' => false
        ]);

        $reservation = Reservation::factory()->create([
            'book_id' => $checkOut->book_id,
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'pending'
        ]);

        $checkOut->checkIn();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'reserved'
        ]);
    }

    /**
     * test complete reservation on checkout
     */
    public function test_reservation_is_completed_on_checkout()
    {
        $checkOut = CheckOut::factory()->create([
            'is_checked_in' => true
        ]);

        $reservation = Reservation::factory()->create([
            'book_id' => $checkOut->book_id,
            'status' => 'reserved'
        ]);

        $response = $this->post(route('check-outs.store'), [
            'book_id' => $checkOut->book_id,
            'member_id' => $reservation->member_id,
            'due_date' => now()->addMonth()
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'completed'
        ]);
    }
}
