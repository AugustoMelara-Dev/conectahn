<?php

// Manual migration execution via raw PDO
require __DIR__ . '/vendor/autoload.php';

try {
    // Load .env file
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
    $dotenv->load();
    
    echo "Connecting to database...\n";
    
    // Create database connection
    $host = env('DB_HOST', '127.0.0.1');
    $database = env('DB_DATABASE');
    $username = env('DB_USERNAME');
    $password = env('DB_PASSWORD');
    
    $pdo = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully!\n\n";
    
    // Check if migrations table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
    if ($stmt->rowCount() === 0) {
        echo "Creating migrations table...\n";
        $pdo->exec("CREATE TABLE migrations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL
        )");
    }
    
    // Get current batch number
    $stmt = $pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $batch = ($result['max_batch'] ?? 0) + 1;
    
    // Migration 1: follows table
    $migration1 = '2025_11_20_100000_create_follows_table';
    $stmt = $pdo->prepare("SELECT * FROM migrations WHERE migration = ?");
    $stmt->execute([$migration1]);
    
    if ($stmt->rowCount() === 0) {
        echo "Running: {$migration1}\n";
        $pdo->exec("CREATE TABLE follows (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            tenant_id BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            UNIQUE KEY unique_user_tenant (user_id, tenant_id),
            KEY idx_user_id (user_id),
            KEY idx_tenant_id (tenant_id),
            CONSTRAINT fk_follows_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            CONSTRAINT fk_follows_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        )");
        
        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$migration1, $batch]);
        echo "  ✓ Migrated\n";
    } else {
        echo "Skipping: {$migration1} (already migrated)\n";
    }
    
    // Migration 2: campaigns table
    $migration2 = '2025_11_20_100001_create_campaigns_table';
    $stmt = $pdo->prepare("SELECT * FROM migrations WHERE migration = ?");
    $stmt->execute([$migration2]);
    
    if ($stmt->rowCount() === 0) {
        echo "Running: {$migration2}\n";
        $pdo->exec("CREATE TABLE campaigns (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            tenant_id BIGINT UNSIGNED NOT NULL,
            subject VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            sent_at TIMESTAMP NULL,
            recipients_count INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            KEY idx_tenant_id (tenant_id),
            KEY idx_sent_at (sent_at),
            CONSTRAINT fk_campaigns_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
        )");
        
        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$migration2, $batch]);
        echo "  ✓ Migrated\n";
    } else {
        echo "Skipping: {$migration2} (already migrated)\n";
    }
    
    // Migration 3: add is_featured to products
    $migration3 = '2025_11_20_100002_add_is_featured_to_products_table';
    $stmt = $pdo->prepare("SELECT * FROM migrations WHERE migration = ?");
    $stmt->execute([$migration3]);
    
    if ($stmt->rowCount() === 0) {
        echo "Running: {$migration3}\n";
        
        // Check if column already exists
        $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'is_featured'");
        if ($stmt->rowCount() === 0) {
            $pdo->exec("ALTER TABLE products ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER is_visible");
        }
        
        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$migration3, $batch]);
        echo "  ✓ Migrated\n";
    } else {
        echo "Skipping: {$migration3} (already migrated)\n";
    }
    
    echo "\n✅ All migrations completed successfully!\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
