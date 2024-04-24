<?php

declare(strict_types=1);

namespace cronv\Task\Management\Component;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Abstract JSON Request
 */
abstract class AbstractJsonRequest
{
    /**
     * The constructor populates the object's properties from the request data and validates the object.
     *
     * @param ValidatorInterface $validator Validator service used to validate the object's properties.
     * @param RequestStack $requestStack Request stack used to get the current request.
     * @throws \ReflectionException
     */
    public function __construct(
        protected readonly ValidatorInterface $validator,
        protected readonly RequestStack       $requestStack,
    )
    {
        $this->populate();
        $this->validate();
    }

    /**
     * Returns the current request.
     *
     * @return Request
     */
    protected function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * Populates the object's properties from the request data.
     *
     * @throws \ReflectionException
     */
    protected function populate(): void
    {
        $request = $this->getRequest();
        $reflection = new \ReflectionClass($this);


        if (json_validate($request->getContent())) {
            foreach ($request->toArray() as $property => $value) {
                $attribute = self::camelCase($property);

                if (property_exists($this, $attribute)) {
                    $reflectionProperty = $reflection->getProperty($attribute);
                    $reflectionProperty->setValue($this, $value);
                }
            }
        }

        // URL parameters
        if ($request->attributes->has('_route_params')) {
            foreach ($request->attributes->get('_route_params') as $property => $value) {
                $attribute = self::camelCase($property);
                if (property_exists($this, $attribute)) {
                    $reflectionProperty = $reflection->getProperty($attribute);
                    $reflectionProperty->setValue($this, $value);
                }
            }
        }

        // Form data
        if ($formData = $request->request->all()) {
            $arrayData = [];
            foreach ($formData as $property => $values) {
                $attribute = self::camelCase($property);

                foreach ($values as $k => $v) {
                    $arrayData[$k] = $v;
                }
                if (property_exists($this, $attribute)) {
                    $reflectionProperty = $reflection->getProperty($attribute);
                    $reflectionProperty->setValue($this, $arrayData);
                }
            }
        }
    }

    /**
     * Validates the object's properties.
     *
     * @return void
     */
    protected function validate(): void
    {
        $violations = $this->validator->validate($this);
        if (count($violations) < 1) {
            return;
        }

        $errors = [];

        /** @var ConstraintViolation $attribute */
        foreach ($violations as $violation) {
            $attribute = self::snakeCase($violation->getPropertyPath());
            $errors[$attribute] = [
                'value' => $violation->getInvalidValue(),
                'message' => $violation->getMessage(),
            ];
        }

        $response = new JsonResponse([
            'message' => null,
            'errors' => $errors
        ], Response::HTTP_BAD_REQUEST);
        $response->send();
        exit;
    }

    /**
     * Converts a string to camel case.
     *
     * @param string $attribute Attribute
     * @return string
     */
    private static function camelCase(string $attribute): string
    {
        $slugger = new AsciiSlugger();
        return $slugger->slug($attribute)->camel()->toString();
    }

    /**
     * Converts a string to snake case.
     *
     * @param string $attribute Attribute
     * @return string
     */
    private static function snakeCase(string $attribute): string
    {
        $slugger = new AsciiSlugger();
        return $slugger->slug($attribute)->snake()->toString();
    }
}
