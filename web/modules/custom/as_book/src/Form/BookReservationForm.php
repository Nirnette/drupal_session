<?php

namespace Drupal\as_book\Form;

use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Class BookReservationForm.
 *
 * @package Drupal\as_book\Form
 */
class BookReservationForm extends FormBase {

  /**
   * Symfony\Component\HttpFoundation\RequestStack definition.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;
  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;
  public function __construct(
    RequestStack $request_stack,
    AccountProxy $current_user
  ) {
    $this->requestStack = $request_stack;
    $this->currentUser = $current_user;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('current_user')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return '/';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

//      $node = \Drupal::routeMatch()->getParameter('node'); // permet de récupérer l'id du node (en l'occurence, le livre !)
      $node = $this->requestStack->getCurrentRequest()->get('node');

    $form['member'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Membre'),
      '#value' => $this->currentUser->id(),
    ];
    $form['book'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Livre'),
      '#value' => $node->id(),
    ];

    $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Réserver'),
    ];

    return $form;
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

      $currentBook = $this->requestStack->getCurrentRequest()->get('node'); // attrape le node (book) en cours
      $livre = $currentBook->getTitle(); // récupère le titre
      // $livre->get('title')->getString(); // méthode numéro , bonne à savoir de type ->get($fieldname)
      $membre = $this->currentUser->getAccountName(); // facile, récupère le nom du currentUser (vive kintLolz)

      $values = [
          'type' => 'booking',
          'field_member' => $form_state->getValue('member'), // méthode 1 pour ajouter un champ dans l'objet
          'title' => 'réservation de " ' . $livre . ' par ' . $membre . ' "',
      ];

      $node = \Drupal\node\Entity\Node::create($values);
      $node->set('field_book', $form_state->getValue('book')); // méthode 2

      $node->save(); // on sauvegarde en base de données

      drupal_set_message('Votre réservation a bien été prise en charge');
//      $form_state->getValue('member');
//      $form_state->getValue('book');

    // Display result.
//    foreach ($form_state->getValues() as $key => $value) {
//        drupal_set_message($key . ': ' . $value);
//    }

  }

}
