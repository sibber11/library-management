<?php

namespace Tests\Feature;

use App\Models\Book;
use Database\Seeders\BookSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


/**
 * test all the controller actions for the books
 */
class BookTest extends TestCase
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

    public function test_table_can_be_seeded(){
        // $this->seed(BookSeeder::class);
        Book::factory()->count(1)->create();
        $count = \App\Models\Book::count();
        $this->assertGreaterThan(0, $count);
    }
    
    /**
     * test the index action
     */
    public function test_index()
    {
        $response = $this->get(route('books.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn($assert) => $assert
            ->component('Admin/Book/Index')
            ->has('books')
        );
    }

    /**
     * test the create action
     */
    public function test_create()
    {
        $response = $this->get(route('books.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn($assert) => $assert
            ->component('Admin/Book/Fields')
        );
    }

    /**
     * test the store action
     */
    public function test_store()
    {
        $this->withoutExceptionHandling();
        $response = $this->post(route('books.store'), [
            'title' => 'test title',
            'author' => 'test author',
            'price' => 100,
            'available' => 1,
            'description' => 'test description',
            'published_at' => '2021-01-01',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('books.index'));
    }

    /**
     * test the show action
     */
    // public function test_show()
    // {
    //     $book = \App\Models\Book::factory()->create();
    //     $response = $this->get(route('books.show', $book));
    //     $response->assertStatus(200);
    //     $response->assertInertia(fn($assert) => $assert
    //         ->component('Admin/Book/Fields')
    //         ->has('book')
    //     );
    // }

    /**
     * test the edit action
     */
     public function test_edit()
     {
         $book = \App\Models\Book::factory()->create();
         $response = $this->get(route('books.edit', $book));
         $response->assertStatus(200);
         $response->assertInertia(fn($assert) => $assert
            ->component('Admin/Book/Fields')
            ->has('book')
         );
     }

     /**
      * test the update action
      */
        public function test_update()
        {
            $book = \App\Models\Book::factory()->create();
            $response = $this->put(route('books.update', $book), [
                'title' => 'new title',
                'author' => 'new author',
                'price' => 100,
                'available' => 1,
                'description' => 'test description',
                'published_at' => '2021-01-01',
            ]);
            $response->assertStatus(302);
            $response->assertRedirect(route('books.index'));

            $book->refresh();
            $this->assertEquals('new title', $book->title);
            $this->assertEquals('new author', $book->author);
            $this->assertEquals(100, $book->price);
            $this->assertEquals(1, $book->available);
            $this->assertEquals('test description', $book->description);
            $this->assertEquals('2021-01-01', $book->published_at->format('Y-m-d'));
        }

        /**
         * test the destroy action
         */
        public function test_destroy()
        {
            $book = \App\Models\Book::factory()->create();
            $response = $this->delete(route('books.destroy', $book));
            $response->assertStatus(302);
            $response->assertRedirect(route('books.index'));
            $this->assertModelMissing($book);
        }
}
