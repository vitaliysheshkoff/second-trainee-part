<?php

namespace Drupal\pokemon\Mail;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Entity update notification mail class.
 */
final class EntityUpdateNotificationMail {

  /**
   * The mail handler.
   *
   * @var \Drupal\pokemon\Mail\MailHandler
   */
  protected $mailHandler;

  /**
   * Constructs a new EntityUpdateNotificationMail object.
   *
   * @param \Drupal\pokemon\Mail\MailHandler $mail_handler
   *   The mail handler.
   */
  public function __construct(MailHandler $mail_handler) {
    $this->mailHandler = $mail_handler;
  }

  /**
   * Sends email.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $email
   *   The email.
   *
   * @return bool
   *   The message status.
   */
  public function send(EntityInterface $entity, string $email): bool {
    $entity_id = $entity->id();
    $entity_type = $entity->getEntityTypeId();
    $entity_label = $entity->label();

    $subject = new TranslatableMarkup('Pokemon @entity_type modification', [
      '@entity_type' => $entity_type,
    ]);

    $body = [
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => new TranslatableMarkup("We detected @entity_type modification:
         id: [@entity_id]
         label: [@entity_label]", [
           '@entity_id' => $entity_id,
           '@entity_type' => $entity_type,
           '@entity_label' => $entity_label,
         ]),
      ],
      '#markup' => new TranslatableMarkup('@entity_type:
       id: [@entity_id]
       label: [@entity_label] was modified', [
         '@entity_id' => $entity_id,
         '@entity_type' => $entity_type,
       ]),
    ];

    return $this->mailHandler->sendMail($email, $subject, $body);
  }

}
