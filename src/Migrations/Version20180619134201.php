<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180619134201 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question CHANGE title title VARCHAR(1024) NOT NULL, CHANGE text text VARCHAR(1024) NOT NULL');
        $this->addSql('ALTER TABLE answer CHANGE text text VARCHAR(1024) NOT NULL');
        $this->addSql('ALTER TABLE app_users CHANGE ban_date ban_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answer CHANGE text text VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE app_users CHANGE ban_date ban_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE question CHANGE title title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE text text VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
