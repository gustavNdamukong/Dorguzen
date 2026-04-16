<?php

namespace Dorguzen\Tests\Unit;

use Dorguzen\Testing\TestCase;
use Dorguzen\Core\DGZ_DB_Singleton;
use Dorguzen\Tests\Manual\DgzTestAuthor;
use Dorguzen\Tests\Manual\DgzTestPost;

/**
 * Tests DGZ lazy loading via __call() on DGZ_Model.
 *
 * Usage pattern for lazy loading:
 *   $model = container(MyModel::class)->loadData($id);   // loads record into $this->data, returns $this
 *   $children = $model->myChildModel();                  // lazy-loads related records
 *
 * Covers:
 *   1. hasChild  — returns all child records for the loaded parent
 *   2. hasParent — returns the single parent record for the loaded child
 *   3. Empty result when there are no children
 *   4. BadMethodCallException for unknown method names
 *   5. RuntimeException when hasChild is called with no record loaded
 */
class LazyLoadTest extends TestCase
{
    private static bool $tablesCreated = false;

    // -------------------------------------------------------------------------
    // Setup / Teardown
    // -------------------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$tablesCreated) {
            $this->createTestTables();
            self::$tablesCreated = true;
        }

        $this->seedTestData();

        // Ensure test models are registered with the container
        container()->singleton(DgzTestAuthor::class, fn($c) => new DgzTestAuthor($c->get(\Dorguzen\Config\Config::class)));
        container()->singleton(DgzTestPost::class,   fn($c) => new DgzTestPost($c->get(\Dorguzen\Config\Config::class)));
    }

    protected function tearDown(): void
    {
        $this->clearTestData();
        parent::tearDown();
    }

    public static function tearDownAfterClass(): void
    {
        $db = DGZ_DB_Singleton::getInstance();
        $db->execute('DROP TABLE IF EXISTS dgz_test_posts');
        $db->execute('DROP TABLE IF EXISTS dgz_test_authors');
        self::$tablesCreated = false;
        parent::tearDownAfterClass();
    }

    // -------------------------------------------------------------------------
    // Tests
    // -------------------------------------------------------------------------

    /**
     * hasChild: $author->dgzTestPost() should return all posts belonging to that author.
     * Uses loadData() to populate the model before calling the dynamic method.
     */
    public function test_hasChild_returns_child_records(): void
    {
        // loadData() hydrates $this->data and returns $this — the correct pattern for lazy loading
        $author = container(DgzTestAuthor::class)->loadData(1);

        $posts = $author->dgzTestPost();

        $this->assertIsArray($posts, 'Lazy-loaded children should be an array');
        $this->assertCount(2, $posts, 'Author 1 should have exactly 2 posts');
        foreach ($posts as $post) {
            $this->assertEquals(1, $post['post_author_id'], 'Each post should belong to author 1');
        }
    }

    /**
     * hasChild: an author with no posts should return an empty array.
     */
    public function test_hasChild_returns_empty_for_no_children(): void
    {
        $author = container(DgzTestAuthor::class)->loadData(2);

        $posts = $author->dgzTestPost();

        $this->assertIsArray($posts);
        $this->assertEmpty($posts, 'Author 2 has no posts — should return empty array');
    }

    /**
     * hasParent: $post->dgzTestAuthor() should return the parent author row for that post.
     * Returns a single associative array row (same as getById).
     */
    public function test_hasParent_returns_parent_record(): void
    {
        $post = container(DgzTestPost::class)->loadData(1);

        $author = $post->dgzTestAuthor();

        $this->assertNotEmpty($author, 'Lazy-loaded parent should not be empty');
        $this->assertEquals('Alice', $author['author_name']);
    }

    /**
     * Calling an undefined method should throw BadMethodCallException.
     */
    public function test_undefined_method_throws_bad_method_call_exception(): void
    {
        $this->expectException(\BadMethodCallException::class);

        container(DgzTestAuthor::class)->loadData(1)->nonExistentRelationship();
    }

    /**
     * Calling a hasChild method on a model with no record loaded should throw RuntimeException.
     */
    public function test_hasChild_with_no_record_loaded_throws_runtime_exception(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/no primary key value/i');

        // Fresh instance — $this->data is empty, so PK lookup returns null
        $author = new DgzTestAuthor(container(\Dorguzen\Config\Config::class));
        $author->dgzTestPost();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function createTestTables(): void
    {
        $db = DGZ_DB_Singleton::getInstance();

        $db->execute('DROP TABLE IF EXISTS dgz_test_posts');
        $db->execute('DROP TABLE IF EXISTS dgz_test_authors');

        $db->execute('CREATE TABLE dgz_test_authors (
            author_id   INTEGER PRIMARY KEY,
            author_name TEXT NOT NULL
        )');

        $db->execute('CREATE TABLE dgz_test_posts (
            post_id        INTEGER PRIMARY KEY,
            post_title     TEXT NOT NULL,
            post_author_id INTEGER NOT NULL
        )');
    }

    private function seedTestData(): void
    {
        $db = DGZ_DB_Singleton::getInstance();

        $db->execute("INSERT INTO dgz_test_authors (author_id, author_name) VALUES (1, 'Alice')");
        $db->execute("INSERT INTO dgz_test_authors (author_id, author_name) VALUES (2, 'Bob')");

        $db->execute("INSERT INTO dgz_test_posts (post_id, post_title, post_author_id) VALUES (1, 'Post A', 1)");
        $db->execute("INSERT INTO dgz_test_posts (post_id, post_title, post_author_id) VALUES (2, 'Post B', 1)");
    }

    private function clearTestData(): void
    {
        $db = DGZ_DB_Singleton::getInstance();
        $db->execute('DELETE FROM dgz_test_posts');
        $db->execute('DELETE FROM dgz_test_authors');
    }
}
