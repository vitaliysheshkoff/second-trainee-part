<?php

namespace Drupal\advertisement\Form;

use Drupal\advertisement\EntityCreationResult;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Field\FieldException;
use Drupal\Core\File\FileSystem;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Url;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a advertisement form.
 */
class AdvertisementForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * File system service.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

  /**
   * AdvertisementForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManager $entity_type_manager, FileSystem $file_system) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('file_system'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advertisement_advertisement';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('The title of the advertisement'),
      '#required' => TRUE,
      // '#element_validate' => [[$this, 'validateTitle']],
    ];

    $dir = 'public://advertisement_images';
    $form['advertisement_image'] = [
      '#type' => 'managed_file',
      '#title' => 'Advertisement image',
      '#name' => 'advertisement_image',
      '#description' => $this->t('The image of advertisement'),
      '#upload_location' => $dir,
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
        // 25.6mb.
        'file_validate_size' => [25600000],
        '#describtion' => t('Please make sure you use the proper format for uploading the file.'),
      ],
      '#required' => TRUE,
    ];

    $form['advertisement_date']['date_from'] = [
      '#type' => 'datetime',
      '#size' => 20,
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
      '#required' => TRUE,
    ];

    $form['advertisement_date']['date_to'] = [
      '#type' => 'datetime',
      '#size' => 20,
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
      '#required' => TRUE,
    ];

    $node_urls = $this->getPageUrls();
    $form['page_select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select the page in which the advertisement will be placed'),
      '#default_value' => NULL,
      '#options' => [
        $node_urls,
      ],
    ];

    $form['region_select'] = [
      '#type' => 'select',
      '#title' => $this->t('Select the region in which the advertisement will be placed'),
      '#options' => [
        'Left Sidebar' => $this->t('Left Sidebar'),
        'Right Sidebar' => $this->t('Right Sidebar'),
      ],
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * Element specific validation callback.
   */
  public function validateTitle($element, FormStateInterface $form_state, $form) {
    $title = $form_state->getValue('title');
    if (strlen($title) < 10) {
      $form_state->setError($element, $this->t('The title must be at least 10 characters long.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if ($form_state->getTriggeringElement()['#name'] != 'advertisement_image_upload_button') {
      // Dates validation.
      $date_to = $form_state->getValue('date_to');
      $date_from = $form_state->getValue('date_from');
      if ($date_to->getTimestamp() <= $date_from->getTimestamp()) {
        $form_state->setErrorByName('date_from', $this->t('Date From should be less than Date to.'));
      }
      // Advertisement title validation.
      $definition = DataDefinition::create('any');
      $definition->addConstraint('AdvertisementTitleConstraint');
      $typed_data = \Drupal::typedDataManager()
        ->create($definition, $form_state->getValue('title'));
      $violations = $typed_data->validate();
      if ($violations->count() > 0) {
        $form_state->setErrorByName('title', $violations[0]->getMessage());
      }
    }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $type = 'advertising_request';
    $title = $form_state->getValue('title');
    $date_to = $form_state->getValue('date_to')
      ->format('Y-m-d');
    $date_from = $form_state->getValue('date_from')
      ->format('Y-m-d');
    $region = $form_state->getValue('region_select');

    $base_path = Url::fromRoute('<front>', [], ['absolute' => TRUE])
      ->toString();
    $page_url = $base_path . substr($form_state->getValue('page_select'), 1);

    $fields = [
      'field_advertisement_title' => $title,
      'field_advertisement_date_to' => $date_to,
      'field_advertisement_date_from' => $date_from,
      'field_advertisement_url' => $page_url,
      'field_advertisement_region' => $region,
    ];

    $image_id = $form_state->getValue('advertisement_image')[0];
    $entity_creation_result = $this->createNode($fields, $image_id, $title, $type);

    if (!$entity_creation_result->getEntity()) {
      $this->messenger()->addError($entity_creation_result->getStatus());
    }
    else {
      $this->messenger()->addStatus($entity_creation_result->getStatus());
      $form_state->setRedirect('<front>');
    }
  }

  /**
   * Gets list of pages.
   *
   * @return array
   *   The array of pages.
   */
  public function getPageUrls(): array {
    $nids = $this->entityTypeManager->getStorage('node')->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'pokemon')
      ->execute();

    $nodes = Node::loadMultiple($nids);

    $node_names = [];
    foreach ($nodes as $node) {
      $node_names["{$node->toUrl()->toString()}"] = $node->toUrl()->toString();
    }

    return $node_names;
  }

  /**
   * Creates node with fields.
   *
   * @param array $fields
   *   List of regular fields.
   * @param int $image_id
   *   The image id.
   * @param string $title
   *   Node title.
   * @param string $type
   *   Node type.
   *
   * @return \Drupal\advertisement\EntityCreationResult
   *   The entity creation result.
   */
  public function createNode(array $fields, int $image_id, string $title, string $type): EntityCreationResult {
    try {
      $query = $this->entityTypeManager->getStorage('node')->getQuery();
      $query->condition('title', $title);
      $node_ids = $query->accessCheck(FALSE)->execute();

      if (!empty($node_ids)) {
        $node = $this->entityTypeManager->getStorage('node')
          ->load(reset($node_ids));
        return new EntityCreationResult("Node is already exist", $node);
      }

      $node = $this->entityTypeManager
        ->getStorage('node')
        ->create([
          'uid' => 1,
          'type' => $type,
          'title' => $title,
        ]);

      // Set regular fields.
      foreach ($fields as $field_name => $value) {
        $node->set($field_name, $value);
      }

      // Validate title field.
      $this->validateEntityField($node, 'field_advertisement_title');

      $node->save();

      // Set Img field.
      $media = Media::create([
        'bundle' => 'advertisement_image',
        'uid' => '1',
        'field_media_image_2' => [
          'target_id' => $image_id,
        ],
      ]);
      $media->save();

      $node->set('field_advertisement_image', $media->id());
      $node->save();

      return new EntityCreationResult('Node has successfully created', $node);

    }
    catch (\Throwable $e) {
      return new EntityCreationResult("Failure in creation new node: {$e->getMessage()}");
    }
  }

  /**
   * Validate entity field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $field_name
   *   The entity field name.
   */
  protected function validateEntityField(EntityInterface $entity, string $field_name) {
    $title = $entity->get($field_name);
    $violations = $title->validate();

    if ($violations->count() > 0) {
      throw new FieldException($violations[0]->getMessage());
    }
  }

}
