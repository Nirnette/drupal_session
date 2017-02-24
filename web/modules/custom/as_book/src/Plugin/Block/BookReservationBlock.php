<?php

namespace Drupal\as_book\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Provides a 'BookReservationBlock' block.
 *
 * @Block(
 *  id = "book_reservation_block",
 *  admin_label = @Translation("Book reservation block"),
 * )
 */
class BookReservationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;
  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;
  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;
  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        CurrentRouteMatch $current_route_match, 
	AccountProxy $current_user, 
	RequestStack $request_stack
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $current_route_match;
    $this->currentUser = $current_user;
    $this->requestStack = $request_stack;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('current_user'),
      $container->get('request_stack')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function build() {

      $build = [];

      if ($this->currentUser->isAuthenticated()){
          $node = $this->currentRouteMatch->getParameter('node');

          // Si une réservation existe pour ce livre et cet utilisateur, afficher un texte au lieu de la réservation



          if($node && $node->getType() == 'book'){ // Sinon on créer la réservation !

              $query = \Drupal::entityQuery('node');
              $query->condition('type','booking');
              $query->condition('field_member', $this->currentUser->id());
              $query->condition('field_book', $node->id());

              $result = $query->execute();

              if (count($result)){ //si la query retourne un résultat non nul
                  $build['book_reservation_block']['#markup'] = 'Vous avez déjà réservé ce livre';
              } else {
                  $form = \Drupal::formBuilder()->getForm('\Drupal\as_book\Form\BookReservationForm');
                  $build['book_reservation_block']['form'] = $form;
              }

          }

      }

    return $build;
  }

}
