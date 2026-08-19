<?php

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php scripts/convert_pg_dump_to_mysql.php input-postgres.sql output-mysql.sql\n");
    exit(1);
}

$inputPath = $argv[1];
$outputPath = $argv[2];

if (!is_file($inputPath)) {
    fwrite(STDERR, sprintf("Input file not found: %s\n", $inputPath));
    exit(1);
}

$schemas = [
    'app_user' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'email', 'roles', 'password'],
        'boolean' => [],
        'sql' => "CREATE TABLE `app_user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` VARCHAR(180) NOT NULL,
  `roles` JSON NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_88BDF3E9D17F50A6` (`uuid`),
  UNIQUE KEY `UNIQ_88BDF3E9E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'appointment' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'date', 'location', 'subject', 'special_page_id'],
        'boolean' => [],
        'sql' => "CREATE TABLE `appointment` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date` DATETIME NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `subject` LONGTEXT NOT NULL,
  `special_page_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FE38F844D17F50A6` (`uuid`),
  KEY `IDX_FE38F844D44A3F71` (`special_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'article' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'title', 'content', 'published_at', 'category_id', 'show_in_menu', 'menu_order'],
        'boolean' => ['show_in_menu'],
        'sql' => "CREATE TABLE `article` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` VARCHAR(255) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `published_at` DATETIME NOT NULL,
  `category_id` INT NOT NULL,
  `show_in_menu` TINYINT(1) NOT NULL DEFAULT 0,
  `menu_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_23A0E66D17F50A6` (`uuid`),
  KEY `IDX_23A0E6612469DE2` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'association' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'address', 'phone_number', 'linkedin_link', 'instagram_link', 'contact_email'],
        'boolean' => [],
        'sql' => "CREATE TABLE `association` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `address` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(20) DEFAULT NULL,
  `linkedin_link` VARCHAR(255) DEFAULT NULL,
  `instagram_link` VARCHAR(255) DEFAULT NULL,
  `contact_email` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FD8521CCD17F50A6` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'board_member' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'first_name', 'last_name', 'category', 'title', 'expertise', 'qualifications', 'sort_order', 'photo_id', 'prefix', 'dons', 'comites'],
        'boolean' => [],
        'sql' => "CREATE TABLE `board_member` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `first_name` VARCHAR(255) NOT NULL,
  `last_name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `title` VARCHAR(100) DEFAULT NULL,
  `expertise` VARCHAR(255) DEFAULT NULL,
  `qualifications` VARCHAR(255) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `photo_id` INT DEFAULT NULL,
  `prefix` VARCHAR(20) DEFAULT NULL,
  `dons` JSON NOT NULL,
  `comites` JSON NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_DCFABEDFD17F50A6` (`uuid`),
  KEY `IDX_DCFABEDF7E9E4C8C` (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'category' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'name', 'slug', 'menu_order'],
        'boolean' => [],
        'sql' => "CREATE TABLE `category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `menu_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_64C19C1D17F50A6` (`uuid`),
  UNIQUE KEY `UNIQ_64C19C1989D9B62` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'doctrine_migration_versions' => [
        'columns' => ['version', 'executed_at', 'execution_time'],
        'boolean' => [],
        'sql' => "CREATE TABLE `doctrine_migration_versions` (
  `version` VARCHAR(191) NOT NULL,
  `executed_at` DATETIME DEFAULT NULL,
  `execution_time` INT DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'linked_in_post' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'embed_link', 'title'],
        'boolean' => [],
        'sql' => "CREATE TABLE `linked_in_post` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `embed_link` VARCHAR(500) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1B796B7ED17F50A6` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'media' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'name', 'logo', 'website_url'],
        'boolean' => [],
        'sql' => "CREATE TABLE `media` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` VARCHAR(255) NOT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `website_url` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_6A2CA10CD17F50A6` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'messenger_messages' => [
        'columns' => ['id', 'body', 'headers', 'queue_name', 'created_at', 'available_at', 'delivered_at'],
        'boolean' => [],
        'sql' => "CREATE TABLE `messenger_messages` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `body` LONGTEXT NOT NULL,
  `headers` LONGTEXT NOT NULL,
  `queue_name` VARCHAR(190) NOT NULL,
  `created_at` DATETIME NOT NULL,
  `available_at` DATETIME NOT NULL,
  `delivered_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`, `available_at`, `delivered_at`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'partner' => [
        'columns' => ['id', 'uuid', 'title', 'logo', 'website_url', 'description', 'is_active', 'position', 'created_at', 'updated_at', 'subtitle'],
        'boolean' => ['is_active'],
        'sql' => "CREATE TABLE `partner` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `website_url` VARCHAR(255) DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `position` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_312B3E16D17F50A6` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'press_mention' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'title', 'external_link', 'published_at', 'media_id', 'special_page_id', 'type'],
        'boolean' => [],
        'sql' => "CREATE TABLE `press_mention` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` VARCHAR(255) NOT NULL,
  `external_link` VARCHAR(500) NOT NULL,
  `published_at` DATETIME DEFAULT NULL,
  `media_id` INT DEFAULT NULL,
  `special_page_id` INT DEFAULT NULL,
  `type` VARCHAR(50) NOT NULL DEFAULT 'Article',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_7B4AB945D17F50A6` (`uuid`),
  KEY `IDX_7B4AB945EA9FDD75` (`media_id`),
  KEY `IDX_7B4AB945D44A3F71` (`special_page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
    'special_page' => [
        'columns' => ['id', 'uuid', 'created_at', 'updated_at', 'title', 'slug', 'identifier', 'content', 'show_in_menu', 'menu_order', 'category_id'],
        'boolean' => ['show_in_menu'],
        'sql' => "CREATE TABLE `special_page` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `identifier` VARCHAR(50) NOT NULL,
  `content` LONGTEXT DEFAULT NULL,
  `show_in_menu` TINYINT(1) NOT NULL DEFAULT 0,
  `menu_order` INT NOT NULL DEFAULT 0,
  `category_id` INT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_965DB70BD17F50A6` (`uuid`),
  UNIQUE KEY `UNIQ_965DB70B989D9B62` (`slug`),
  UNIQUE KEY `UNIQ_965DB70B772E836A` (`identifier`),
  KEY `IDX_965DB70B12469DE2` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ],
];

$data = parseCopyData(file($inputPath, FILE_IGNORE_NEW_LINES), $schemas);

$output = [];
$output[] = '-- MySQL import generated from a PostgreSQL dump.';
$output[] = 'SET NAMES utf8mb4;';
$output[] = 'SET FOREIGN_KEY_CHECKS = 0;';
$output[] = '';

foreach (array_reverse(array_keys($schemas)) as $table) {
    $output[] = sprintf('DROP TABLE IF EXISTS `%s`;', $table);
}

$output[] = '';

foreach ($schemas as $table => $schema) {
    $output[] = $schema['sql'];
    $output[] = '';
}

foreach ($schemas as $table => $schema) {
    foreach ($data[$table] ?? [] as $row) {
        $columns = array_map(static fn(string $column): string => sprintf('`%s`', $column), $schema['columns']);
        $values = [];

        foreach ($schema['columns'] as $index => $column) {
            $value = $row[$index] ?? null;
            if (in_array($column, $schema['boolean'], true) && $value !== null) {
                $value = $value === 't' || $value === 'true' || $value === '1' ? '1' : '0';
            }
            $values[] = sqlValue($value);
        }

        $output[] = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s);',
            $table,
            implode(', ', $columns),
            implode(', ', $values)
        );
    }
    if (!empty($data[$table])) {
        $output[] = '';
    }
}

$output[] = 'SET FOREIGN_KEY_CHECKS = 1;';
$output[] = '';
$output[] = foreignKeySql();
$output[] = '';

$adminEmail = getenv('ADMIN_EMAIL') ?: null;
$adminPasswordHash = getenv('ADMIN_PASSWORD_HASH') ?: null;
if ($adminEmail && $adminPasswordHash) {
    $output[] = sprintf(
        "INSERT INTO `app_user` (`uuid`, `created_at`, `updated_at`, `email`, `roles`, `password`) VALUES (UUID(), NOW(), NOW(), %s, '[\"ROLE_ADMIN\"]', %s) ON DUPLICATE KEY UPDATE `roles` = '[\"ROLE_ADMIN\"]', `password` = VALUES(`password`), `updated_at` = NOW();",
        sqlValue($adminEmail),
        sqlValue($adminPasswordHash)
    );
    $output[] = '';
}

file_put_contents($outputPath, implode(PHP_EOL, $output));
printf("Generated %s\n", $outputPath);

/**
 * @param list<string> $lines
 * @param array<string, array{columns: list<string>}> $schemas
 * @return array<string, list<list<string|null>>>
 */
function parseCopyData(array $lines, array $schemas): array
{
    $data = array_fill_keys(array_keys($schemas), []);
    $currentTable = null;

    foreach ($lines as $line) {
        if ($currentTable !== null) {
            if ($line === '\.') {
                $currentTable = null;
                continue;
            }
            $data[$currentTable][] = array_map(unescapeCopyValue(...), explode("\t", $line));
            continue;
        }

        if (preg_match('/^COPY public\.([a-z_]+) \((.+)\) FROM stdin;$/', $line, $matches)) {
            $table = $matches[1];
            if (!isset($schemas[$table])) {
                continue;
            }
            $columns = array_map(
                static fn(string $column): string => trim($column, " \""),
                explode(',', $matches[2])
            );
            if ($columns !== $schemas[$table]['columns']) {
                throw new RuntimeException(sprintf(
                    'Unexpected column order for %s. Expected %s, got %s.',
                    $table,
                    implode(', ', $schemas[$table]['columns']),
                    implode(', ', $columns)
                ));
            }
            $currentTable = $table;
        }
    }

    return $data;
}

function unescapeCopyValue(string $value): ?string
{
    if ($value === '\N') {
        return null;
    }

    return strtr($value, [
        '\\b' => "\b",
        '\\f' => "\f",
        '\\n' => "\n",
        '\\r' => "\r",
        '\\t' => "\t",
        '\\v' => "\v",
        '\\\\' => '\\',
    ]);
}

function sqlValue(?string $value): string
{
    if ($value === null) {
        return 'NULL';
    }

    return "'" . str_replace(["\\", "'", "\0"], ["\\\\", "''", "\\0"], $value) . "'";
}

function foreignKeySql(): string
{
    return implode(PHP_EOL, [
        'ALTER TABLE `appointment` ADD CONSTRAINT `FK_FE38F844D44A3F71` FOREIGN KEY (`special_page_id`) REFERENCES `special_page` (`id`);',
        'ALTER TABLE `article` ADD CONSTRAINT `FK_23A0E6612469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);',
        'ALTER TABLE `board_member` ADD CONSTRAINT `FK_DCFABEDF7E9E4C8C` FOREIGN KEY (`photo_id`) REFERENCES `media` (`id`);',
        'ALTER TABLE `press_mention` ADD CONSTRAINT `FK_7B4AB945EA9FDD75` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`);',
        'ALTER TABLE `press_mention` ADD CONSTRAINT `FK_7B4AB945D44A3F71` FOREIGN KEY (`special_page_id`) REFERENCES `special_page` (`id`);',
        'ALTER TABLE `special_page` ADD CONSTRAINT `FK_965DB70B12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);',
    ]);
}
