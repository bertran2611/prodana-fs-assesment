# Product Testing Guide

This directory contains comprehensive tests for the product management system, including Livewire components, authorization policies, and bulk actions functionality.

## 🧪 Test Files Overview

### 1. **ProductTest.php** - Basic Product Functionality

Tests the core product features including:

-   Route access and authentication
-   Product CRUD operations
-   Form validation
-   Image upload handling
-   Authorization checks

### 2. **ProductPolicyTest.php** - Authorization Rules

Tests the ProductPolicy authorization system:

-   Admin vs regular user permissions
-   CRUD operation permissions
-   Edge cases with different user roles
-   Policy method coverage

### 3. **LivewireProductTest.php** - Livewire Components

Tests the Livewire product components:

-   ProductsIndex component functionality
-   ProductForm component behavior
-   Real-time validation
-   Component state management

### 4. **BulkActionsTest.php** - Bulk Operations

Tests the bulk actions functionality:

-   Product selection and deselection
-   Bulk delete operations
-   Select all functionality
-   Integration with filters and pagination

## 🚀 Running the Tests

### Prerequisites

-   Laravel application running
-   Database configured and accessible
-   Composer dependencies installed

### Quick Start

```bash
# Run all product tests
php artisan test tests/Feature/ProductTest.php tests/Unit/ProductPolicyTest.php tests/Feature/LivewireProductTest.php tests/Feature/BulkActionsTest.php

# Run specific test file
php artisan test tests/Feature/ProductTest.php

# Run with verbose output
php artisan test --verbose tests/Feature/ProductTest.php

# Run with coverage (if Xdebug is available)
php artisan test --coverage tests/Feature/ProductTest.php
```

### Using the Test Runner Script

```bash
# Make the script executable
chmod +x tests/run_tests.sh

# Run all tests
./tests/run_tests.sh
```

## 📋 Test Categories

### Feature Tests

-   **ProductTest.php**: Tests product routes, views, and basic functionality
-   **LivewireProductTest.php**: Tests Livewire component behavior
-   **BulkActionsTest.php**: Tests bulk operations functionality

### Unit Tests

-   **ProductPolicyTest.php**: Tests authorization policy logic

## 🔍 Test Coverage

### Product Management

-   ✅ Product creation and editing
-   ✅ Form validation (required fields, formats, uniqueness)
-   ✅ Image upload handling
-   ✅ Category management
-   ✅ Search and filtering
-   ✅ Sorting and pagination

### Authorization

-   ✅ Admin user permissions
-   ✅ Regular user restrictions
-   ✅ Policy method coverage
-   ✅ Edge case handling

### Livewire Components

-   ✅ Component rendering
-   ✅ State management
-   ✅ Real-time validation
-   ✅ Event handling
-   ✅ Integration with filters

### Bulk Actions

-   ✅ Product selection
-   ✅ Select all functionality
-   ✅ Bulk delete operations
-   ✅ Permission checking
-   ✅ Integration with pagination

## 🛠️ Test Utilities

### Factories

The tests use Laravel factories to create test data:

-   `UserFactory`: Creates users with different roles
-   `ProductFactory`: Creates products with realistic data
-   `CategoryFactory`: Creates product categories

### Database

-   Tests use `RefreshDatabase` trait for clean state
-   Storage is faked for file upload tests
-   Soft deletes are tested for product removal

### Livewire Testing

-   Uses `Livewire::actingAs()` for authenticated testing
-   Tests component state changes
-   Verifies component rendering and behavior

## 🐛 Debugging Tests

### Common Issues

1. **Database Connection**: Ensure test database is configured
2. **Factory Issues**: Check if factories are properly defined
3. **Permission Errors**: Verify user roles are set correctly
4. **Livewire Issues**: Check if Livewire is properly installed

### Debug Commands

```bash
# Run single test method
php artisan test --filter test_product_form_can_create_product

# Run with detailed output
php artisan test --verbose --stop-on-failure

# Check test database
php artisan migrate:status --env=testing
```

## 📊 Test Results

### Expected Output

```
🧪 Running Product Feature Tests...
==================================

📋 Running Product Tests...
PASS  Tests\Feature\ProductTest
✓ guest cannot access products
✓ user can view products index
✓ user can view products index with livewire
✓ user can search products
✓ user can filter products by category
✓ user can sort products
✓ user can change per page
✓ user can clear filters
✓ user can view product details
✓ user can view product create page
✓ user can view product edit page
✓ user can view trash page
✓ product form can create product
✓ product form can update product
✓ product form validates required fields
✓ product form validates price format
✓ product form validates stock format
✓ product form validates unique name
✓ product form can handle image upload
✓ product form validates image size
✓ product form validates image type
✓ live validation works on product form
✓ product form shows categories dropdown
✓ product form handles edit mode
✓ product form handles create mode

🔐 Running Product Policy Tests...
PASS  Tests\Unit\ProductPolicyTest
✓ admin can view any products
✓ regular user can view any products
✓ admin can view product
✓ regular user can view product
✓ admin can create product
✓ regular user cannot create product
✓ user without role cannot create product
✓ admin can update product
✓ regular user cannot update product
✓ user without role cannot update product
✓ admin can delete product
✓ regular user cannot delete product
✓ user without role cannot delete product
✓ admin can restore product
✓ regular user cannot restore product
✓ user without role cannot restore product
✓ admin can force delete product
✓ regular user cannot force delete product
✓ user without role cannot force delete product

⚡ Running Livewire Product Tests...
PASS  Tests\Feature\LivewireProductTest
✓ products index component loads with data
✓ products index component handles empty state
✓ products index component handles search
✓ products index component handles category filter
✓ products index component handles sorting
✓ products index component handles pagination
✓ products index component handles clear filters
✓ products index component shows filtered count
✓ product form component creates product
✓ product form component updates product
✓ product form component validates required fields
✓ product form component validates field formats
✓ product form component validates unique name
✓ product form component allows same name in edit mode
✓ product form component handles image upload
✓ product form component validates image size
✓ product form component validates image type
✓ product form component shows live validation
✓ product form component shows categories dropdown
✓ product form component handles edit mode
✓ product form component handles create mode
✓ product form component requires admin role
✓ product form component requires admin role for edit
✓ product form component handles validation errors properly
✓ product form component handles success messages

📦 Running Bulk Actions Tests...
PASS  Tests\Feature\BulkActionsTest
✓ bulk actions bar appears when products selected
✓ bulk actions bar shows correct count
✓ select all checkbox selects all current page products
✓ select all checkbox deselects all products
✓ select all checkbox updates when individual products selected
✓ select all checkbox updates when individual products deselected
✓ bulk delete moves products to trash
✓ bulk delete shows success message
✓ bulk delete handles empty selection
✓ bulk delete requires admin role
✓ bulk delete handles partial permissions
✓ clear selection resets selection state
✓ bulk actions work with pagination
✓ bulk actions work with search filters
✓ bulk actions work with category filters
✓ bulk actions work with sorting
✓ bulk actions clear when filters change
✓ bulk actions clear when per page changes
✓ bulk actions clear when sorting changes
✓ bulk actions work with empty results
✓ bulk actions handle mixed permissions correctly

✅ All tests completed!
```

## 🔧 Customizing Tests

### Adding New Tests

1. Create test method in appropriate test file
2. Follow naming convention: `test_descriptive_name()`
3. Use descriptive assertions
4. Test both success and failure cases

### Test Data

-   Use factories for consistent test data
-   Create realistic test scenarios
-   Test edge cases and error conditions
-   Ensure tests are independent

### Best Practices

-   One assertion per test method
-   Clear test method names
-   Proper setup and teardown
-   Mock external dependencies
-   Test both positive and negative cases

## 📚 Additional Resources

-   [Laravel Testing Documentation](https://laravel.com/docs/testing)
-   [Livewire Testing Guide](https://laravel-livewire.com/docs/testing)
-   [PHPUnit Documentation](https://phpunit.de/documentation.html)
-   [Laravel Policy Testing](https://laravel.com/docs/authorization#testing-policies)
