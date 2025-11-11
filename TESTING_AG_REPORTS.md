# Auditor General Report Testing Guide

This document provides comprehensive information about testing the Auditor General Report functionality.

## Test Structure

The tests are organized into the following categories:

### 1. Unit Tests (`tests/Unit/`)

#### Model Tests (`tests/Unit/Models/AuditorGeneralReportTest.php`)
- ✅ Model creation and validation
- ✅ Total calculations (amounts, recoveries, counts)
- ✅ Status transitions and workflow
- ✅ Relationships (creator, approver, findings)
- ✅ Scopes and query builders
- ✅ Business logic methods

#### Enum Tests (`tests/Unit/Enums/AuditorGeneralReportEnumsTest.php`)
- ✅ Report type enum values and labels
- ✅ Status enum values, labels, colors, and icons
- ✅ Workflow validation logic
- ✅ Serialization/deserialization
- ✅ Options array generation

#### Validation Tests (`tests/Unit/Validation/AuditorGeneralReportValidationTest.php`)
- ✅ Required field validation
- ✅ Data type validation
- ✅ String length limits
- ✅ Enum value validation
- ✅ Year range validation
- ✅ Optional field handling

#### Factory Tests (`tests/Unit/Factories/AuditorGeneralReportFactoryTest.php`)
- ✅ Factory instance creation
- ✅ Unique value generation
- ✅ State methods (draft, published, etc.)
- ✅ Custom attribute overrides
- ✅ Relationship handling

### 2. Feature Tests (`tests/Feature/`)

#### Main Feature Tests (`tests/Feature/AuditorGeneralReportFeatureTest.php`)
- ✅ Full CRUD operations
- ✅ Finding attachment/detachment
- ✅ Total recalculation
- ✅ Status workflow integration
- ✅ Search and filtering
- ✅ Report categorization

#### Database Tests (`tests/Feature/Database/AuditorGeneralReportDatabaseTest.php`)
- ✅ Migration verification
- ✅ Table structure validation
- ✅ Foreign key constraints
- ✅ Cascade delete behavior
- ✅ Index performance
- ✅ Data integrity

#### Filament Resource Tests (`tests/Feature/Filament/AuditorGeneralReportResourceTest.php`)
- ✅ List page functionality
- ✅ Create/edit forms
- ✅ Filtering and searching
- ✅ Table sorting
- ✅ Bulk operations
- ✅ Navigation between pages

## Running Tests

### Run All AG Report Tests
```bash
./vendor/bin/sail test --filter="AuditorGeneral"
```

### Run Test Categories
```bash
# Model tests
./vendor/bin/sail test tests/Unit/Models/AuditorGeneralReportTest.php

# Enum tests
./vendor/bin/sail test tests/Unit/Enums/AuditorGeneralReportEnumsTest.php

# Validation tests
./vendor/bin/sail test tests/Unit/Validation/AuditorGeneralReportValidationTest.php

# Factory tests
./vendor/bin/sail test tests/Unit/Factories/AuditorGeneralReportFactoryTest.php

# Feature tests
./vendor/bin/sail test tests/Feature/AuditorGeneralReportFeatureTest.php

# Database tests
./vendor/bin/sail test tests/Feature/Database/AuditorGeneralReportDatabaseTest.php

# Filament tests
./vendor/bin/sail test tests/Feature/Filament/AuditorGeneralReportResourceTest.php
```

### Use the Test Runner Script
```bash
# Run all tests
./run-ag-tests.sh

# Run specific category
./run-ag-tests.sh model
./run-ag-tests.sh enum
./run-ag-tests.sh validation
./run-ag-tests.sh factory
./run-ag-tests.sh feature
./run-ag-tests.sh database
./run-ag-tests.sh filament

# Run by categories with detailed output
./run-ag-tests.sh categories

# Run with coverage
./run-ag-tests.sh coverage
```

## Test Coverage Areas

### ✅ Model Logic
- Report creation and updates
- Status workflow transitions
- Finding relationships
- Total calculations
- Business rules validation

### ✅ Data Validation
- Required fields
- Data types and formats
- Enum values
- Foreign key constraints
- Unique constraints

### ✅ User Interface
- Filament resource pages
- Forms and validation
- Tables and filtering
- Actions and bulk operations
- Navigation and routing

### ✅ Database Operations
- Migration integrity
- Foreign key relationships
- Cascade operations
- Index performance
- Data consistency

### ✅ Integration
- End-to-end workflows
- Multi-model interactions
- Status transitions
- Calculated fields
- Search functionality

## Key Test Scenarios

### Status Workflow Testing
1. **Draft → Under Review**: Submit for review
2. **Under Review → Approved**: Approve with proper user
3. **Approved → Published**: Publish with timestamps
4. **Under Review → Draft**: Return to draft for edits
5. **Invalid transitions**: Ensure proper validation

### Finding Management Testing
1. **Add findings**: Attach findings with metadata
2. **Remove findings**: Detach and recalculate totals
3. **Categorize findings**: Assign to different categories
4. **Order findings**: Custom section ordering
5. **Highlight findings**: Mark important findings

### Calculation Testing
1. **Total amounts**: Sum of all attached finding amounts
2. **Recovery totals**: Sum of all recoveries from findings
3. **Finding counts**: Accurate count of attached findings
4. **Recovery rates**: Percentage calculations
5. **Real-time updates**: Recalculation on changes

## Test Data Factory

The `AuditorGeneralReportFactory` provides several convenient methods:

```php
// Basic report
AuditorGeneralReport::factory()->create();

// Status-specific reports
AuditorGeneralReport::factory()->draft()->create();
AuditorGeneralReport::factory()->underReview()->create();
AuditorGeneralReport::factory()->approved()->create();
AuditorGeneralReport::factory()->published()->create();

// Type-specific reports
AuditorGeneralReport::factory()->annual()->create();
AuditorGeneralReport::factory()->quarterly()->create();
AuditorGeneralReport::factory()->special()->create();
AuditorGeneralReport::factory()->performance()->create();
AuditorGeneralReport::factory()->thematic()->create();

// With specific data
AuditorGeneralReport::factory()->withTotals()->create();
AuditorGeneralReport::factory()->complete()->create();
AuditorGeneralReport::factory()->forYear(2024)->create();
AuditorGeneralReport::factory()->createdBy($user)->create();
```

## Expected Test Results

All tests should pass with:
- **Model Tests**: 16+ assertions covering business logic
- **Enum Tests**: 13+ assertions covering enum functionality
- **Validation Tests**: 10+ assertions covering form validation
- **Factory Tests**: 20+ assertions covering data generation
- **Feature Tests**: 25+ assertions covering user workflows
- **Database Tests**: 15+ assertions covering data integrity
- **Filament Tests**: 20+ assertions covering UI functionality

## Troubleshooting

### Common Issues
1. **Migration errors**: Ensure all migrations have run successfully
2. **Factory dependencies**: Verify User factory is available
3. **Enum mismatches**: Update test expectations if enum values change
4. **Database cleanup**: Use RefreshDatabase trait in tests
5. **Authentication**: Ensure proper user authentication in feature tests

### Performance Considerations
- Database tests may take longer due to migration setup
- Factory tests generate significant test data
- Feature tests perform full request cycles
- Use `--stop-on-failure` flag for faster debugging

## Continuous Integration

These tests are designed to be run in CI/CD pipelines:

```bash
# CI-friendly command
./vendor/bin/sail test --filter="AuditorGeneral" --stop-on-failure --log-junit=test-results.xml
```

## Contributing

When adding new functionality to AG Reports:

1. **Write tests first** (TDD approach)
2. **Update existing tests** if behavior changes
3. **Add new test categories** for major features
4. **Update this documentation** with new test scenarios
5. **Ensure all tests pass** before submitting

## Coverage Goals

Target coverage areas:
- **Lines**: 95%+ coverage of AG Report related code
- **Functions**: 100% coverage of public methods
- **Branches**: 90%+ coverage of conditional logic
- **Classes**: 100% coverage of AG Report classes