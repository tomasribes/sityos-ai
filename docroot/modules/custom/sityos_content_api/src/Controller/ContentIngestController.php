<?php

declare(strict_types=1);

namespace Drupal\sityos_content_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\sityos_content_api\Authentication\ApiKeyAuthenticator;
use Drupal\sityos_content_api\Exception\IngestStepException;
use Drupal\sityos_content_api\Exception\IngestValidationException;
use Drupal\sityos_content_api\Service\ContentIngestOrchestrator;
use Drupal\sityos_content_api\Service\InputValidator;
use Drupal\sityos_content_api\ValueObject\IngestPayload;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

final class ContentIngestController extends ControllerBase {

  public function __construct(
    private readonly ApiKeyAuthenticator $authenticator,
    private readonly InputValidator $validator,
    private readonly ContentIngestOrchestrator $orchestrator,
  ) {}

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('sityos_content_api.api_key_authenticator'),
      $container->get('sityos_content_api.input_validator'),
      $container->get('sityos_content_api.content_ingest_orchestrator'),
    );
  }

  public function ingest(Request $request): JsonResponse {
    try {
      $this->authenticator->authenticate($request);
    }
    catch (TooManyRequestsHttpException $e) {
      return $this->errorResponse('RATE_LIMIT_EXCEEDED', $e->getMessage(), Response::HTTP_TOO_MANY_REQUESTS, [
        'Retry-After' => '60',
      ]);
    }
    catch (AccessDeniedHttpException $e) {
      return $this->errorResponse('UNAUTHORIZED', $e->getMessage(), Response::HTTP_UNAUTHORIZED);
    }

    if ($request->getContentTypeFormat() !== 'json') {
      return $this->errorResponse('INVALID_CONTENT_TYPE', 'Content-Type must be application/json', Response::HTTP_BAD_REQUEST);
    }

    $body = json_decode((string) $request->getContent(), TRUE);
    if (!is_array($body)) {
      return $this->errorResponse('INVALID_JSON', 'Request body is not valid JSON', Response::HTTP_BAD_REQUEST);
    }

    $payload = IngestPayload::fromArray($body);

    try {
      $this->validator->validate($payload);
    }
    catch (IngestValidationException $e) {
      return $this->jsonResponse([
        'status' => 'error',
        'code' => 'VALIDATION_FAILED',
        'message' => 'Input validation failed',
        'errors' => $e->errors,
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    try {
      $result = $this->orchestrator->ingest($payload);
    }
    catch (IngestStepException $e) {
      return $this->jsonResponse([
        'status' => 'error',
        'code' => 'TRANSACTION_FAILED',
        'message' => sprintf('Content creation failed. Database transaction rolled back.'),
        'failed_step' => $e->failedStep,
        'steps_completed_before_failure' => $e->completedSteps,
        'drupal_error_id' => $e->errorId,
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    return $this->jsonResponse([
      'status' => 'success',
      ...$result,
    ], Response::HTTP_CREATED);
  }

  private function jsonResponse(array $data, int $status = Response::HTTP_OK, array $headers = []): JsonResponse {
    $response = new JsonResponse($data, $status, $headers);
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $response->headers->set('X-Frame-Options', 'DENY');

    return $response;
  }

  private function errorResponse(string $code, string $message, int $status, array $headers = []): JsonResponse {
    return $this->jsonResponse([
      'status' => 'error',
      'code' => $code,
      'message' => $message,
    ], $status, $headers);
  }

}
