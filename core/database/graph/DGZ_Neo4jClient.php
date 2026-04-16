<?php 

namespace Dorguzen\Core\Database\Graph;


/**
 * Exposes Neo4j using the Laudis Neo4j PHP Client package from Composer (modern, maintained)
 * This way we do not have to write a Bolt driver ourselves.
 * Dorguzen will not attempt to invent a DSL for Neo4j because the latter already has a well-developed,
 * mature and supported, and popular DSL-the Cypher Query Language (CQL) which needs not be re-written.
 * 
 * Implementation of Neo4j PHP client in 6 steps
 * 
 * 1) Be sure to Install Neo4j PHP client
 * 
 *      composer require laudis/neo4j-php-client
 * 
 * 2) This class in Dorguzen\Core\Graph
 * 
 * 3) Make connection setting in Dorguzen config:
 * 
 *  Config setting:
 * 
 *      'neo4j' => [
 *          'uri' => 'bolt://localhost:7687',
 *          'username' => 'neo4j',
 *          'password' => 'password',
 *      ],
 * 
 * 4) Then register in container.
 * 
 * 5) Finally, use it in a controller like this:
 * 
 *      public function show()
 *      {
 *          $neo = container(DGZ_Neo4jClient::class);
 *      
 *          $users = $neo->run(
 *              'MATCH (u:User) RETURN u LIMIT 10'
 *          );
 *      
 *          return DGZ_Response::json($users);
 *      }
 * 
 *      OR
 * 
 *      class SocialGraphModel
 *      {
 *          public function friendsOf(string $name)
 *          {
 *              return $this->neo->run(
 *                  'MATCH (u:User {name: $name})-[:FRIEND_OF]->(f) RETURN f',
 *                  ['name' => $name]
 *              );
 *          }
 *      }
 * 
 * 6) Testing strategy (manual)
 *      Write the test in tests/manual/neo4j_test_1.php
 * 
 *    Test coverage:
 * 
 *      Test the following:
 *      
 *          -Connection
 *          -Simple MATCH
 *          -Parameter binding
 *          -Write query (CREATE)
 *          -Update query (SET)
 *          -That’s enough to know it works.
 * 
 *
 *  MVC will remain intact. We only added a second data engine.
 *  This will harness the full power of Neo4j within your Dorguzen application.
 */
class DGZ_Neo4jClient
{
    /** @var object */
    protected $client;

    public function __construct(array $config)
    {
        // this is part of making the Laudis Neo4j PHP Client package
        if (!class_exists(\Laudis\Neo4j\ClientBuilder::class)) {
            dgzie(    'Neo4j support requires: composer require laudis/neo4j-php-client ^3.4');
        }
        $this->client = \Laudis\Neo4j\ClientBuilder::create()
            ->withDriver(
                'bolt',
                $config['uri'],
                \Laudis\Neo4j\Authentication\Authenticate::basic(
                    $config['username'],
                    $config['password']
                )
            )
            ->build();
    }


    /**
     * Adds transaction support for neo4j
     * 
     * Usage:
     * 
     *  $neo->transaction(function ($tx) {
     *      $tx->run('CREATE (u:User {name: "Alice"})');
     *  });
     * 
     * 
     * @param callable $callback
     * @return mixed
     */
    public function transaction(callable $callback): mixed
    {
        $session = $this->client->session();

        $tx = $session->beginTransaction();

        try {
            $result = $callback($tx);
            $tx->commit();
            return $result;
        } catch (\Throwable $e) {
            $tx->rollback();
            throw $e;
        } finally {
            $session->close();
        }
    }


    /**
     * Run a Cypher query and return normalized array output.
     */
    public function run(string $cypher, array $params = []): array
    {
        $result = $this->client->run($cypher, $params);

        $output = [];

        foreach ($result as $record) {
            $row = [];

            foreach ($record->keys() as $key) {
                $value = $record->get($key);

                // If Node or Relationship object
                if (is_object($value) && method_exists($value, 'getProperties')) {
                    $row[$key] = $value->getProperties();
                } else {
                    $row[$key] = $value;
                }
            }

            $output[] = $row;
        }

        return $output;
    }
}