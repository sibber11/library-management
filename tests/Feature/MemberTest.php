<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Member;
use Database\Seeders\MemberSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MemberTest extends TestCase
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
        // $this->seed(MemberSeeder::class);
        \App\Models\Member::factory()->count(1)->create();
        $count = \App\Models\Member::count();
        $this->assertGreaterThan(0, $count);
    }

    /**
     * test the index action
     */
    public function test_index()
    {
        $response = $this->get(route('members.index'));
        $response->assertStatus(200);
        $response->assertInertia(
            fn ($assert) => $assert
                ->component('Admin/Member/Index')
                ->has('members')
        );    
    }

    public function test_index_can_be_filtered(){
        Member::factory()->active()->count(5)->create();
        Member::factory()->count(5)->create();

        $response = $this->get(route('members.index', ['filter' => 'active']));
        $response->assertStatus(200);
        $response->assertInertia(
            fn ($assert) => $assert
                ->component('Admin/Member/Index')
                ->has('members')
                ->has('members.data', 5)
        );

        $response = $this->get(route('members.index', ['filter' => 'expired']));
        $response->assertStatus(200);
        $response->assertInertia(
            fn ($assert) => $assert
                ->component('Admin/Member/Index')
                ->has('members')
                ->has('members.data', 5)
        ); 
    }

    public function test_index_can_be_searched(){
        $user = User::factory()->create([
            'name' => 'test',
        ]);

        $response = $this->get(route('members.index', ['search' => 'test']));
        $response->assertStatus(200);
        $response->assertInertia(
            fn ($assert) => $assert
                ->component('Admin/Member/Index')
                ->has('members')
                ->has('members.data',1)
        );
    }

    /**
     * test the create action
     */
    public function test_create()
    {
        $response = $this->get(route('members.create'));
        $response->assertStatus(200);
        $response->assertInertia(
            fn ($assert) => $assert
                ->component('Admin/Member/Fields')
        );
    }

    /**
     * test the store action
     */
    public function test_store()
    {
        $response = $this->post(route('members.store'), [
            'name' => 'buu',
            'email' => 'text@buu.com',
            'membership_duration' => 2,
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'buu',
            'email' => 'text@buu.com',
        ]);
        // $this->assertDatabaseHas('members', [
        //     'membership_due_date' => now()->addMonth()->format('Y-m-d H:i:s'),
        // ]);
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
        $member = Member::factory()->create();
        $response = $this->get(route('members.edit', $member->id));
        $response->assertStatus(200);
        $response->assertInertia(fn ($assert) => $assert
            ->component('Admin/Member/Fields')
            ->has('member')
        );
    }

    /**
     * test the update action
     */
    public function test_update()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->put(route('members.update', $user->member->id), [
            'name' => 'buu',
            'email' => 'buu@buu.com',
            'membership_duration' => 1,
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'membership_due_date' => now()->addMonth()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * test the destroy action
     */
    public function test_destroy()
    {
        $member = \App\Models\Member::factory()->create();
        $response = $this->delete(route('members.destroy', $member));
        $response->assertStatus(302);
        $response->assertRedirect(route('members.index'));
        $this->assertModelMissing($member);
    }

    public function test_membership_can_be_extended()
    {
        $member = \App\Models\Member::factory()->create([
            'membership_due_date' => now()->subMonths(1),
        ]);
        $this->assertFalse($member->membership_status);
        $member->extendMembership(2);
        $this->assertTrue($member->membership_status);
        $this->assertEquals(now()->addMonths(2)->format('d-m-Y'), $member->membership_due_date->format('d-m-Y'));
    }
}
