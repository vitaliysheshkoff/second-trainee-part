<?php

namespace Drupal\pokemon\Form;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\pokemon\PokemonProcessNodeBatch;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Dummy settings for this site.
 */
class UpdatePokemonNodeBatchForm extends FormBase {

  /**
   * Entity type service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Batch Builder.
   *
   * @var \Drupal\Core\Batch\BatchBuilder
   */
  protected $batchBuilder;

  /**
   * Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;

  /**
   * Constructs a new UpdatePokemonNodeBatchForm.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_channel_factory
   *   The logger service.
   */
  public function __construct(EntityTypeManager $entity_type_manager, LoggerChannelFactoryInterface $logger_channel_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->loggerChannelFactory = $logger_channel_factory;
    $this->batchBuilder = new BatchBuilder();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('logger.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pokemon_update_pokemon_node_batch_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['help'] = [
      '#markup' => $this->t('This form update all pokemon nodes'),
    ];

    $form['node_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Content type'),
      '#options' => [
        'pokemon' => 'Pokemon',
      ],
      '#required' => FALSE,
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['run'] = [
      '#type' => 'submit',
      '#value' => $this->t('Run batch'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node_type = $form_state->getValue(['node_type']);

    // 1. Log the start of the script.
    $this->loggerChannelFactory->get('pokemon')
      ->info('Update nodes batch operations start');

    if (strlen($node_type) == 0) {
      $node_type = 'pokemon';
    }

    try {
      $storage = $this->entityTypeManager->getStorage('node');
      $query = $storage->getQuery()
        ->condition('type', $node_type)
        ->condition('status', '1');
      $nids = $query->accessCheck(FALSE)->execute();
    }
    catch (\Exception $e) {
      $this->loggerChannelFactory->get('pokemon')
        ->warning('Error found @e', ['@e' => $e]);
    }

    $numOperations = 0;
    $operations = [];
    $batchId = 1;

    if (!empty($nids)) {
      foreach ($nids as $nid) {
        // Prepare the operation. Here we could do other operations on nodes.
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
      $this->loggerChannelFactory->get('pokemon')
        ->warning('No nodes of this type @type', ['@type' => $node_type]);
    }

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

    batch_set($this->batchBuilder->toArray());
  }

}
