<?php

namespace Drupal\opening_hours\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\opening_hours\Plugin\Field\FieldType\OpeningHoursItem;
use Psr\Log\LoggerInterface;
use StadGent\Services\OpeningHours\Exception\ServiceNotFoundException;
use StadGent\Services\OpeningHours\Service\Channel\ChannelService;
use StadGent\Services\OpeningHours\Service\Service\ServiceService;
use StadGent\Services\OpeningHours\Value\Channel;
use StadGent\Services\OpeningHours\Value\Service;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A widget bar.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @FieldWidget(
 *   id = "opening_hours",
 *   label = @Translation("Opening Hours"),
 *   field_types = {
 *     "opening_hours"
 *   }
 * )
 */
class OpeningHoursWidget extends WidgetBase {

  use LoggerChannelTrait;

  /**
   * The ServiceService.
   *
   * @var \StadGent\Services\OpeningHours\Service\Service\ServiceService
   */
  protected $serviceService;

  /**
   * The ChannelService.
   *
   * @var \StadGent\Services\OpeningHours\Service\Channel\ChannelService
   */
  protected $channelService;

  /**
   * Constructs a Opening Hours widget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \StadGent\Services\OpeningHours\Service\Service\ServiceService $serviceService
   *   The Service service.
   * @param \StadGent\Services\OpeningHours\Service\Channel\ChannelService $channelService
   *   The Channel service.
   */
  public function __construct(
    string $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    ServiceService $serviceService,
    ChannelService $channelService,
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $third_party_settings
    );

    $this->serviceService = $serviceService;
    $this->channelService = $channelService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('opening_hours.service'),
      $container->get('opening_hours.channel')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    /** @var \Drupal\opening_hours\Plugin\Field\FieldType\OpeningHoursItem $item */
    $item = $items->get($delta);
    $formValues = $this->extractFormStateValues((int) $delta, $form, $form_state);

    $currentService = $this->getCurrentService($item, $formValues);
    $currentChannel = $currentService
      ? $this->getCurrentChannel($currentService, $item, $formValues)
      : NULL;
    $channelOptions = $currentService
      ? $this->getChannelOptionsForService($currentService)
      : [];

    // Unique channel wrapper id to update using ajax callback.
    $wrapperId = Html::getUniqueId(
      sprintf('%s-wrapper', $this->fieldDefinition->getName())
    );

    $element['opening_hours'] = [
      '#title' => $this->t('Opening hours'),
      '#description' => $this->t('Select a Service and one of its Channels to link this item with Opening Hours.'),
      '#type' => 'details',
      '#open' => TRUE,
      '#element_validate' => [
        [$this, 'validate'],
      ],
    ];

    $element['opening_hours']['service'] = [
      '#title' => $this->t('Service'),
      '#type' => 'textfield',
      '#default_value' => $currentService ? $this->getServiceValueString($currentService) : NULL,
      '#autocomplete_route_name' => 'opening_hours.service.autocomplete',
      '#ajax' => [
        'callback' => [get_class($this), 'channelsDropdownCallback'],
        'wrapper' => $wrapperId,
        'disable-refocus' => TRUE,
        'event' => 'autocompleteclose',
      ],
    ];

    $element['opening_hours']['channel'] = [
      '#title' => $this->t('Channel'),
      '#type' => 'select',
      '#default_value' => $currentChannel ? $currentChannel->getId() : NULL,
      '#options' => count($channelOptions) ? $channelOptions : [0 => $this->t('Select first a Service')],
      // We don't use container as it clutters up the form_state values.
      '#prefix' => sprintf('<div id="%s">', $wrapperId),
      '#suffix' => '</div>',
    ];
    if (count($channelOptions) > 1) {
      $element['opening_hours']['channel']['#empty_option'] = $this->t('- Select -');
      $element['opening_hours']['channel']['#empty_value'] = 0;
    }

    return $element;
  }

  /**
   * Callback for the Channel select.
   *
   * @param array $form
   *   The form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   *
   * @return array
   *   The Channel form element.
   */
  public static function channelsDropdownCallback(array $form, FormStateInterface $form_state): array {
    $serviceElement = $form_state->getTriggeringElement();
    $openingHours = NestedArray::getValue(
      $form,
      array_slice($serviceElement['#array_parents'], 0, -1)
    );

    return $openingHours['channel'];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    foreach ($values as $delta => &$value) {
      $this->massageFormValue($value);
      unset($values[$delta]['opening_hours']);
    }

    return $values;
  }

  /**
   * Massage a single form value array.
   *
   * @param array $value
   *   The value array.
   */
  protected function massageFormValue(array &$value): void {
    $service = NULL;
    if (!empty($value['opening_hours']['service'])) {
      $service = $this->getServiceFromValueString($value['opening_hours']['service']);
    }
    $value['service'] = $service ? $service->getId() : NULL;
    $value['service_label'] = $service ? $service->getLabel() : NULL;

    $channel = NULL;
    if ($service && !empty($value['opening_hours']['channel'])) {
      $channel = $this->getChannelById($service, (int) $value['opening_hours']['channel']);
    }
    $value['channel'] = $channel ? $channel->getId() : NULL;
    $value['channel_label'] = $channel ? $channel->getLabel() : NULL;

    $value['broken'] = 0;
  }

  /**
   * Validate the fields.
   *
   * This will check if a URL is set if a label is filled in.
   *
   * @param array $element
   *   The form values container.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   */
  public function validate(array $element, FormStateInterface $form_state): void {
    $serviceElement = $element['service'];
    $channelElement = $element['channel'];
    $service = NULL;

    // No errors if both values are empty.
    if (empty($serviceElement['#value']) && empty($channelElement['#value'])) {
      return;
    }

    if (!empty($serviceElement['#value'])) {
      $service = $this->getServiceFromValueString($serviceElement['#value']);
      if (!$service) {
        $form_state->setError(
          $serviceElement,
          $this->t('Service does not exists.')
        );
        return;
      }
    }

    if (!empty($channelElement['#value']) && !$service) {
      $form_state->setError(
        $serviceElement,
        $this->t('Service is required when Channel is set.')
      );
      return;
    }

    if ($service && empty($channelElement['#value'])) {
      $form_state->setError(
        $serviceElement,
        $this->t('Channel is required when Service is set.')
      );
      return;
    }

    $channel = $this->getChannelById($service, (int) $channelElement['#value']);
    if (!$channel) {
      $form_state->setError(
        $serviceElement,
        $this->t('Channel does not exists for this Service.')
      );
      return;
    }
  }

  /**
   * Helper to get the current Service from the form state or saved Service ID.
   *
   * @param \Drupal\opening_hours\Plugin\Field\FieldType\OpeningHoursItem $item
   *   The field item.
   * @param array $form_values
   *   The values from the form.
   *
   * @return \StadGent\Services\OpeningHours\Value\Service|null
   *   The current Service (if any).
   */
  protected function getCurrentService(OpeningHoursItem $item, array $form_values): ?Service {
    if ($form_values['is_submitted']) {
      return $this->getServiceFromValueString($form_values['service']);
    }

    if (!empty($item->service)) {
      return $this->getServiceById($item->service);
    }

    return NULL;
  }

  /**
   * Helper to get the current Channel from the form state or saved Channel ID.
   *
   * @param \StadGent\Services\OpeningHours\Value\Service $service
   *   The Service to get the Channel for.
   * @param \Drupal\opening_hours\Plugin\Field\FieldType\OpeningHoursItem $item
   *   The field item.
   * @param array $form_values
   *   The values from the form.
   *
   * @return \StadGent\Services\OpeningHours\Value\Channel
   *   The current Channel ID.
   */
  protected function getCurrentChannel(Service $service, OpeningHoursItem $item, array $form_values): ?Channel {
    if ($form_values['is_submitted']) {
      return $this->getChannelById($service, (int) $form_values['channel']);
    }

    if ($item->getChannelId()) {
      return $this->getChannelById($service, $item->getChannelId());
    }

    return NULL;
  }

  /**
   * Get value as string.
   *
   * @param \StadGent\Services\OpeningHours\Value\Service $service
   *   The service object.
   *
   * @return string
   *   This wil contain "service label [ID]".
   */
  protected function getServiceValueString(Service $service): string {
    return sprintf('%s [%d]', $service->getLabel(), $service->getId());
  }

  /**
   * Get the Service object from value as string.
   *
   * @param string $value
   *   The string value to get the Service object from.
   *
   * @return \StadGent\Services\OpeningHours\Value\Service|null
   *   The Service (if any).
   */
  protected function getServiceFromValueString(string $value): ?Service {
    if (empty($value)) {
      return NULL;
    }

    $matches = [];
    preg_match('/\[(\d+)]$/', $value, $matches);
    if (empty($matches[1])) {
      return NULL;
    }

    return $this->getServiceById($matches[1]);
  }

  /**
   * Get a Service by its ID.
   *
   * @param int $serviceId
   *   The Service ID.
   *
   * @return \StadGent\Services\OpeningHours\Value\Service|null
   *   The Service (if any).
   */
  protected function getServiceById(int $serviceId): ?Service {
    try {
      return $this->serviceService->getById($serviceId);
    }
    catch (ServiceNotFoundException $exception) {
      return NULL;
    }
    catch (\Exception $exception) {
      $this->getOpeningHoursLogger()->error(
        'API returned : @message',
        ['@message' => $exception->getMessage()]
      );
      return NULL;
    }
  }

  /**
   * Get a Channel by its ID.
   *
   * @param \StadGent\Services\OpeningHours\Value\Service $service
   *   The Service to get the channel for.
   * @param int $channelId
   *   The Channel ID.
   *
   * @return \StadGent\Services\OpeningHours\Value\Channel|null
   *   The Service (if any).
   */
  protected function getChannelById(Service $service, int $channelId): ?Channel {
    try {
      return $this->channelService->getById($service->getId(), $channelId);
    }
    catch (ServiceNotFoundException $exception) {
      return NULL;
    }
    catch (\Exception $exception) {
      $this->getOpeningHoursLogger()->error(
        'API returned : @message',
        ['@message' => $exception->getMessage()]
      );
      return NULL;
    }
  }

  /**
   * Get the Channels options for a Service.
   *
   * @param \StadGent\Services\OpeningHours\Value\Service $service
   *   The Service to get the Channels for.
   *
   * @return array
   *   The found Channels as ID => Label.
   */
  protected function getChannelOptionsForService(Service $service): array {
    $options = [];

    $channels = $this->channelService->getAll($service->getId());
    foreach ($channels as $channel) {
      /** @var \StadGent\Services\OpeningHours\Value\Channel $channel */
      $options[$channel->getId()] = $channel->getLabel();
    }

    return $options;
  }

  /**
   * Helper to get the service & channel values from the form state.
   *
   * @param int $delta
   *   The form field element delta.
   * @param array $form
   *   The form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   Array containing:
   *   - service : The service value from the form state.
   *   - channel : The channel value from the form state.
   */
  protected function extractFormStateValues(int $delta, array $form, FormStateInterface $form_state): array {
    $values = [
      'is_submitted' => $this->isFormSubmitted($form_state),
      'service' => NULL,
      'channel' => NULL,
    ];

    if (!$values['is_submitted']) {
      return $values;
    }

    $fieldName = $this->fieldDefinition->getName();
    $parents = array_merge($form['#parents'], [$fieldName]);
    $openingHours = $form_state->getValue($parents);

    if (empty($openingHours[$delta]['opening_hours'])) {
      return $values;
    }

    if (!empty($openingHours[$delta]['opening_hours']['service'])) {
      $values['service'] = $openingHours[$delta]['opening_hours']['service'];
    }
    if (!empty($openingHours[$delta]['opening_hours']['channel'])) {
      $values['channel'] = $openingHours[$delta]['opening_hours']['channel'];
    }

    return $values;
  }

  /**
   * Check if the form was submitted by changing the service.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state to get the triggering element from.
   *
   * @return bool
   *   Is submitted.
   */
  protected function isFormSubmitted(FormStateInterface $form_state): bool {
    $trigger = $form_state->getTriggeringElement();

    return !empty($trigger['#autocomplete_route_name'])
      && $trigger['#autocomplete_route_name'] === 'opening_hours.service.autocomplete';
  }

  /**
   * Get the logger.
   *
   * @return \Psr\Log\LoggerInterface
   *   The opening hours channel logger.
   */
  private function getOpeningHoursLogger(): LoggerInterface {
    return $this->getLogger('opening_hours');
  }

}
