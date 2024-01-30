<?php

declare(strict_types = 1);

namespace Drupal\pu_survey\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a survey invitation block.
 *
 * @Block(
 *   id = "survey_invitation",
 *   admin_label = @Translation("Survey Invitation"),
 *   category = @Translation("Custom"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *   }
 * )
 */
final class SurveyInvitationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the plugin instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ExtensionPathResolver $extensionPathResolver,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('extension.path.resolver'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'news' => [
        'headline' => $this->t('News content headline'),
        'invitation_message' => $this->t('News content invitation message'),
        'button_label' => $this->t('News button label'),
      ],
      'others' => [
        'headline' => $this->t('Other content headline'),
        'invitation_message' => $this->t('Other content invitation message'),
        'button_label' => $this->t('Other button label'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    // Get configuration.
    $config = $this->getConfiguration();

    $form['news'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('News'),
    ];

    $form['news']['headline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Headline'),
      '#default_value' => $config['news']['headline'] ?? '',
    ];

    $form['news']['invitation_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Invitation message'),
      '#default_value' => $config['news']['invitation_message'] ?? '',
    ];

    $form['news']['button_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button label'),
      '#default_value' => $config['news']['button_label'] ?? '',
    ];

    $form['others'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Others'),
    ];

    $form['others']['headline'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Headline'),
      '#default_value' => $config['others']['headline'] ?? '',
    ];

    $form['others']['invitation_message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Invitation message'),
      '#default_value' => $config['others']['invitation_message'] ?? '',
    ];

    $form['others']['button_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button label'),
      '#default_value' => $config['others']['button_label'] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    // Save form data in configuration.
    $this->setConfigurationValue('news', $form_state->getValue('news', 'headline'));
    $this->setConfigurationValue('news', $form_state->getValue('news', 'invitation_message'));
    $this->setConfigurationValue('news', $form_state->getValue('news', 'button_label'));
    $this->setConfigurationValue('others', $form_state->getValue('others', 'headline'));
    $this->setConfigurationValue('others', $form_state->getValue('others', 'invitation_message'));
    $this->setConfigurationValue('others', $form_state->getValue('others', 'button_label'));
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    // Get node context.
    $node = $this->getContextValue('node');
    // Get configuration.
    $config = $this->getConfiguration();

    // Render data using the 'pu_survey' theme function.
    $build['content'] = [
      '#theme' => 'pu_survey',
      '#headline' => ($node?->bundle() === 'news') ? $config['news']['headline'] : $config['others']['headline'],
      '#invitation_message' => ($node?->bundle() === 'news') ? $config['news']['invitation_message'] : $config['others']['invitation_message'],
      '#button_label' => ($node?->bundle() === 'news') ? $config['news']['button_label'] : $config['others']['button_label'],
      '#attached' => [
        'drupalSettings' => [
          'pu_survey' => [
            'image_src' => '/' . $this->extensionPathResolver->getPath('module', 'pu_survey') . '/images/logo.png',
          ],
        ],
      ],
    ];

    return $build;
  }

}
