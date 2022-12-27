<?php

namespace Drupal\pokemon;

/**
 * Services that provides batch callbacks for updating pokemon nodes.
 */
class PokemonProcessNodeBatch {

  /**
   * Batch process callback.
   *
   * @param int $id
   *   Id of the batch.
   * @param int $nid
   *   Id of the pokemon node.
   * @param string $operation_details
   *   Details of the operation.
   * @param array $context
   *   Context for operations.
   */
  public static function processPokemonNode(int $id, int $nid, string $operation_details, array &$context) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    $title = $node->label();
    $pokemon_node_number = $node->get('field_id')->value;

    $node->set('title', "$pokemon_node_number. $title")->save();

    // Store some results for post-processing in the 'finished' callback.
    // The contents of 'results' will be available as $results in the
    // 'finished' function.
    $context['results'][] = $id;

    // Optional message displayed under the progressbar.
    $context['message'] = t('Running Batch "@id" @details',
      ['@id' => $id, '@details' => $operation_details]
    );

  }

  /**
   * Batch Finished callback.
   *
   * @param bool $success
   *   Success of the operation.
   * @param array $results
   *   Array of results for post processing.
   * @param array $operations
   *   Array of operations.
   */
  public function processPokemonNodeFinished(bool $success, array $results, array $operations) {
    $messenger = \Drupal::messenger();
    if ($success) {
      // Here we could do something meaningful with the results.
      // We just display the number of nodes we processed...
      $messenger->addMessage(t('@count results processed.', ['@count' => count($results)]));
    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $messenger->addMessage(
        t('An error occurred while processing @operation with arguments : @args',
          [
            '@operation' => $error_operation[0],
            '@args' => print_r($error_operation[0], TRUE),
          ]
        )
      );
    }
  }

}
