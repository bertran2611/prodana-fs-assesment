#!/bin/bash

echo "🧪 Running Product Feature Tests..."
echo "=================================="

echo ""
echo "📋 Running Product Tests..."
php artisan test tests/Feature/ProductTest.php

echo ""
echo "🔐 Running Product Policy Tests..."
php artisan test tests/Unit/ProductPolicyTest.php

echo ""
echo "⚡ Running Livewire Product Tests..."
php artisan test tests/Feature/LivewireProductTest.php

echo ""
echo "📦 Running Bulk Actions Tests..."
php artisan test tests/Feature/BulkActionsTest.php

echo ""
echo "🎯 Running All Product Tests Together..."
php artisan test tests/Feature/ProductTest.php tests/Unit/ProductPolicyTest.php tests/Feature/LivewireProductTest.php tests/Feature/BulkActionsTest.php

echo ""
echo "✅ All tests completed!"
