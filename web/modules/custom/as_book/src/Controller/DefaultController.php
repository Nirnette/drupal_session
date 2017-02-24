<?php

namespace Drupal\as_book\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Class DefaultController.
 *
 * @package Drupal\as_book\Controller
 */
class DefaultController extends ControllerBase {

  /**
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * {@inheritdoc}
   */
  public function __construct(CurrentRouteMatch $current_route_match) {
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match')
    );
  }

  /**
   * Testdrupal.
   *
   * @return string
   *   Return Hello string.
   */
  public function testDrupal() {

//    \Drupal::getContainer()->get('current_route_match');
//    \Drupal::service('current_route_match');
//    \Drupal::routeMatch();

//EXEMPLES START

//\Drupal::service('current_route_match');
//$this->currentRouteMatch;

//\Drupal::currentUser();
//\Drupal::request();

//$node = \Drupal\node\Entity\Node\ ::load(1);
//$node = \Drupal\user\Entity\Node\ ::load(1);
//$node = \Drupal\taxonomy\Entity\Node\ ::load(1);

//kint($node);

//kint($node->id);
//kint($node->get('title')->getString());

//kint(\Drupal::entityQuery('node'));
//kint($this->entityQuery->get('node');

//EXEMPLES END

      $query = \Drupal::entityQuery('node');

//Filtrer en fonction du type de node qu'on veut, à savoir des book ici
      $query->condition('type','book');

//Livres publiés
      $query->condition('status',1);

//tous les livres de Gotlib
      $query->condition('field_author',2);

//executer la reuqête
      $result = $query->execute();

      $nodes = \Drupal\node\Entity\Node::loadMultiple($result);
      $books = [];

      // Génération des Render Array de slivres en mode d'affichage teaser à partir de l'objet entité des noeuds
      foreach ($nodes as $node){
          $book = node_view($node, 'teaser');
          $books[] = $book;
      }

    return [
        '#theme' => 'book_list',
        'books' => $books,
//      '#type' => 'markup',
//      '#markup' => $this->t('Implement method: testDrupal')
    ];
  }

}
