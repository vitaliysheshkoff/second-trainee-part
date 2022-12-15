<?php

namespace Drupal\pokemon\Commands;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\pokemon\PokemonProcessNodeBatch;
use Drush\Commands\DrushCommands;

/**
 * Defines Drush commands for the pokemon module.
 */
class PokemonDrushCommands extends DrushCommands {

  /**
   * Entity type service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;

  /**
   * Batch Builder.
   *
   * @var \Drupal\Core\Batch\BatchBuilder
   */
  protected $batchBuilder;

  /**
   * Constructs a new PokemonDrushCommands object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_channel_factory
   *   Logger service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, LoggerChannelFactoryInterface $logger_channel_factory) {
    parent::__construct();

    $this->entityTypeManager = $entity_type_manager;
    $this->loggerChannelFactory = $logger_channel_factory;
    $this->batchBuilder = new BatchBuilder();
  }

  /**
   * Update Node.
   *
   * @param string $type
   *   Type of node to update
   *   Argument provided to the drush command.
   *
   * @command update:node
   * @aliases update-node
   *
   * @usage update:node node_type
   *   node_type is the type of node to update
   */
  public function updateNode($type = '') {
    // 1. Log the start of the script.
    $this->loggerChannelFactory->get('pokemon')
      ->info('Update nodes batch operations start');

    // Check the type of node given as argument, if not, set pokemon type as default.
    if (strlen($type) == 0) {
      $type = 'pokemon';
    }

    // 2. Retrieve all nodes of this type.
    try {
      $storage = $this->entityTypeManager->getStorage('node');
      $query = $storage->getQuery()
        ->condition('type', $type)
        ->condition('status', '1');
      $nids = $query->accessCheck(FALSE)->execute();
    }
    catch (\Exception $e) {
      $this->output()->writeln($e);
      $this->loggerChannelFactory->get('pokemon')
        ->warning('Error found @e', ['@e' => $e]);
    }

    // 3. Create the operations array for the batch.
    $operations = [];
    $numOperations = 0;
    $batchId = 1;

    if (!empty($nids)) {
      foreach ($nids as $nid) {
        // Prepare the operation. Here we could do other operations on nodes.
        $this->output()->writeln("Preparing batch: " . $batchId);

        $operations[] = [
          'callback' => [
            new PokemonProcessNodeBatch(),
            'processPokemonNode',
          ],
          'params' =>
            [
              $batchId,
              $nid,
              t('Updating node @nid', ['@nid' => $nid]),
            ],
        ];
        $batchId++;
        $numOperations++;
      }
    }
    else {
      $this->logger()
        ->warning('No nodes of this type @type', ['@type' => $type]);
    }

    // 4. Create the batch.
    $this->batchBuilder
      ->setTitle(t('Updating @num node(s)', ['@num' => $numOperations]))
      ->setInitMessage(t('Initializing.'));

    foreach ($operations as $operation) {
      $this->batchBuilder->addOperation($operation['callback'], $operation['params']);
    }

    $this->batchBuilder->setFinishCallback([
      new PokemonProcessNodeBatch(),
      'processPokemonNodeFinished',
    ]);

    // 5. Add batch operations as new batch sets.
    batch_set($this->batchBuilder->toArray());

    // 6. Process the batch sets.
    drush_backend_batch_process();

    // 6. Show some information.
    $this->logger()->notice("Batch operations end.");
    // 7. Log some information.
    $this->loggerChannelFactory->get('pokemon')
      ->info('Update batch operations end.');
  }

}
