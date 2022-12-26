<?php

namespace Drupal\pokemon\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\pokemon\Event\PokemonMailingListEvent;
use Drupal\pokemon\Form\PokemonMailingListForm;
use Drupal\pokemon\Mail\EntityUpdateNotificationMail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Pokemon update node event subscriber.
 */
abstract class PokemonEntityUpdateBaseSubscriber implements EventSubscriberInterface {

  /**
   * The entity update notification mail service.
   *
   * @var \Drupal\pokemon\Mail\EntityUpdateNotificationMail
   */
  protected $entityUpdateNotificationMail;


  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new PokemonEntityUpdateBaseSubscriber object.
   *
   * @param \Drupal\pokemon\Mail\EntityUpdateNotificationMail $entity_update_notification_mail
   *   The node update notification mail service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(EntityUpdateNotificationMail $entity_update_notification_mail, ConfigFactoryInterface $config_factory) {
    $this->entityUpdateNotificationMail = $entity_update_notification_mail;
    $this->configFactory = $config_factory;
  }

  /**
   * Subscriber function  for PokemonMailingListEvent::XXX.
   *
   * @param \Drupal\pokemon\Event\PokemonMailingListEvent $event
   */
  public function updateEntity(PokemonMailingListEvent $event) {
    $config = $this->configFactory->getEditable(PokemonMailingListForm::SETTINGS);

    $emails_str = $config->get('mailing_list') ?? '';
    $emails_arr = explode(',', $emails_str);

    foreach ($emails_arr as $email) {
      $this->entityUpdateNotificationMail->send($event->getEntity(), $email);
    }
  }

}
