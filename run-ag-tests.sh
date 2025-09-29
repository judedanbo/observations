#!/bin/bash

# Auditor General Report Test Runner
# This script runs all tests related to the Auditor General Report functionality

echo "🔍 Running Auditor General Report Tests..."
echo "============================================"

# Set error handling
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Test categories
CATEGORIES=(
    "Unit/Models/AuditorGeneralReportTest"
    "Unit/Enums/AuditorGeneralReportEnumsTest" 
    "Unit/Validation/AuditorGeneralReportValidationTest"
    "Unit/Factories/AuditorGeneralReportFactoryTest"
    "Feature/AuditorGeneralReportFeatureTest"
    "Feature/Database/AuditorGeneralReportDatabaseTest"
    "Feature/Filament/AuditorGeneralReportResourceTest"
)

# Function to run a test category
run_test_category() {
    local category=$1
    echo -e "${BLUE}Running: $category${NC}"
    
    if ./vendor/bin/sail test "tests/$category.php" --stop-on-failure; then
        echo -e "${GREEN}✅ $category - PASSED${NC}"
    else
        echo -e "${RED}❌ $category - FAILED${NC}"
        return 1
    fi
    echo ""
}

# Function to run all AG-related tests
run_all_ag_tests() {
    echo -e "${YELLOW}Running all Auditor General Report tests...${NC}"
    ./vendor/bin/sail test --filter="AuditorGeneral" --stop-on-failure
}

# Function to run with coverage
run_with_coverage() {
    echo -e "${YELLOW}Running tests with coverage...${NC}"
    ./vendor/bin/sail test --filter="AuditorGeneral" --coverage-text --coverage-html=coverage
}

# Main execution
case "${1:-all}" in
    "model")
        run_test_category "Unit/Models/AuditorGeneralReportTest"
        ;;
    "enum")
        run_test_category "Unit/Enums/AuditorGeneralReportEnumsTest"
        ;;
    "validation")
        run_test_category "Unit/Validation/AuditorGeneralReportValidationTest"
        ;;
    "factory")
        run_test_category "Unit/Factories/AuditorGeneralReportFactoryTest"
        ;;
    "feature")
        run_test_category "Feature/AuditorGeneralReportFeatureTest"
        ;;
    "database")
        run_test_category "Feature/Database/AuditorGeneralReportDatabaseTest"
        ;;
    "filament")
        run_test_category "Feature/Filament/AuditorGeneralReportResourceTest"
        ;;
    "categories")
        echo -e "${BLUE}Running tests by category...${NC}"
        failed=0
        for category in "${CATEGORIES[@]}"; do
            if ! run_test_category "$category"; then
                failed=1
            fi
        done
        if [ $failed -eq 1 ]; then
            echo -e "${RED}Some test categories failed!${NC}"
            exit 1
        else
            echo -e "${GREEN}All test categories passed!${NC}"
        fi
        ;;
    "coverage")
        run_with_coverage
        ;;
    "all"|*)
        run_all_ag_tests
        ;;
esac

echo -e "${GREEN}🎉 Testing completed!${NC}"