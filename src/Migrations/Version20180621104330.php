<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180621104330 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question CHANGE selected_answer_id selected_answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users CHANGE ban_date ban_date DATETIME DEFAULT NULL, CHANGE avatar_path avatar_path VARCHAR(255) DEFAULT NULL, CHANGE old_avatar old_avatar VARCHAR(255) DEFAULT NULL, CHANGE new_avatar new_avatar VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users CHANGE ban_date ban_date DATETIME DEFAULT \'NULL\', CHANGE avatar_path avatar_path VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE old_avatar old_avatar VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE new_avatar new_avatar VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE question CHANGE selected_answer_id selected_answer_id INT DEFAULT NULL');
    }
}
