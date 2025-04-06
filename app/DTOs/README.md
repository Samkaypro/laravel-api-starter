# Data Transfer Objects (DTOs)

## Overview

This application uses Data Transfer Objects (DTOs) to standardize data exchange between the API and clients. DTOs provide a structured way to format response data, ensuring consistency across the API and making it easier to maintain and evolve the API's data structures.

## Benefits

- **Type Safety**: DTOs provide clear type definitions for data objects
- **Separation of Concerns**: Data transformation logic is isolated from controller logic
- **Maintainability**: Changes to data structures can be made in a single location
- **Consistency**: Ensures uniform response formats across the API
- **Testability**: DTOs can be unit tested independently from controllers

## Structure

The DTO implementation follows these principles:

1. **Base DTO**: `BaseDTO` abstract class defines the common interface for all DTOs
2. **Specific DTOs**: Classes like `UserDTO`, `TokenDTO`, etc. for domain-specific data
3. **Composite DTOs**: `AuthResponseDTO` combines multiple DTOs into a complete response
4. **Generic Response**: `ResponseDTO` provides a standard wrapper for API responses

## Usage

### Creating a DTO

DTOs are created by extending the `BaseDTO` abstract class and implementing the required methods:

```php
class ExampleDTO extends BaseDTO
{
    public function __construct(
        public readonly string $property1,
        public readonly int $property2,
    ) {
    }
    
    public static function fromArray(array $data): static
    {
        return new self(
            property1: $data['property1'],
            property2: $data['property2'],
        );
    }
    
    public function toArray(): array
    {
        return [
            'property1' => $this->property1,
            'property2' => $this->property2,
        ];
    }
}
```

### Using DTOs in Controllers

Controllers use DTOs to format response data before sending it to the client:

```php
public function show(Request $request): JsonResponse
{
    $user = User::find($request->id);
    $userDto = UserDTO::fromUser($user);
    
    return $this->sendResponse($userDto->toArray(), 'User retrieved successfully');
}
```

## Best Practices

1. Keep DTOs immutable (use `readonly` properties)
2. Include static factory methods for convenient instantiation
3. Implement the `toArray()` method for JSON serialization
4. Use type hints and documentation to improve code readability
5. Don't include business logic in DTOs - they should only handle data transformation 