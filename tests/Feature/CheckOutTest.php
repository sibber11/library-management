<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\CheckOut;
use Database\Seeders\CheckOutSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckOutTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // $this->withoutExceptionHandling();
        $this->actingAs(\App\Models\User::factory()->admin()->create());
    }

    public function test_table_can_be_created()
    {
        $this->assertTrue(true);
    }

    public function test_table_can_be_seeded()
    {
        $this->seed(CheckOutSeeder::class);
        CheckOut::factory()->create();
        $count = \App\Models\CheckOut::count();
        $this->assertGreaterThan(0, $count);
    }

    /**
     * test the index action
     */
    public function test_index()
    {
        // $this->withExceptionHandling();
        $response = $this->get(route('check-outs.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Admin/CheckOut/Index')
            ->has('checkouts')
        );
    }

    /**
     * test the create action
     */
    public function test_create()
    {
        $response = $this->get(route('check-outs.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Admin/CheckOut/Fields')
            ->has('books')
            ->has('members')
        );
    }

    /**
     * test the store action
     */
    public function test_store()
    {
        $dueDate = now()->addMonth();
        $book = \App\Models\Book::factory()->create();
        $member = \App\Models\Member::factory()->create();
        $response = $this->post(route('check-outs.store'), [
            'book_id' => $book->id,
            'member_id' => $member->id,
            // 'check_out_date' => $dueDate,
            'due_date' => $dueDate,
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('check-outs.index'));
        $this->assertDatabaseHas('check_outs', [
            'book_id' => $book->id,
            'member_id' => $member->id,
            // 'check_out_date' => $dueDate->format('Y-m-d H:i:s'),
            'due_date' => $dueDate->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * test the show action
     */
    // public function test_show()
    // {

    // }

    /**
     * test the edit action
     */
    public function test_edit()
    {
        $checkOut = \App\Models\CheckOut::factory()->create();
        $response = $this->get(route('check-outs.edit', $checkOut));
        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Admin/CheckOut/Fields')
            ->has('checkOut')
        );
    }

    /**
     * test the check in action
     */
    public function test_check_in()
    {
        $this->withoutExceptionHandling();
        $checkOut = \App\Models\CheckOut::factory()->create();
        $response = $this->put(route('check-outs.update', $checkOut),[
            'is_checked_in' => true,
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('check-outs.index'));
        $this->assertDatabaseHas('check_outs', [
            'id' => $checkOut->id,
            'is_checked_in' => true,
        ]);
    }

    /**
     * test the check in action
     */
    public function test_extend_due_date()
    {
        $newDueDate = now()->addMonths(2);
        $this->withoutExceptionHandling();
        $checkOut = \App\Models\CheckOut::factory()->create();
        $response = $this->put(route('check-outs.update', $checkOut),[
            'due_date' => $newDueDate,
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('check-outs.index'));
        $this->assertDatabaseHas('check_outs', [
            'id' => $checkOut->id,
            'due_date' => $newDueDate,
        ]);
    }

    /**
     * test the check in action
     */
    public function test_extend_due_date_excluded_with_check_in()
    {
        $newDueDate = now()->addMonths(2);
        $this->withoutExceptionHandling();
        $checkOut = \App\Models\CheckOut::factory()->create();
        $response = $this->put(route('check-outs.update', $checkOut),[
            'is_checked_in' => true,
            'due_date' => $newDueDate,
        ]);
        $response->assertRedirect(route('check-outs.index'));
        $this->assertDatabaseHas('check_outs', [
            'id' => $checkOut->id,
            'is_checked_in' => true,
            'due_date' => $checkOut->due_date->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * test the destroy action
     */
    public function test_destroy()
    {
        $checkOut = \App\Models\CheckOut::factory()->create();
        $response = $this->delete(route('check-outs.destroy', $checkOut));
        $response->assertStatus(302);
        $response->assertRedirect(route('check-outs.index'));
        $this->assertDatabaseMissing('check_outs', [
            'id' => $checkOut->id,
        ]);
    }
}
