<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180621075832 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question CHANGE selected_answer_id selected_answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users ADD old_avatar VARCHAR(255) DEFAULT NULL, CHANGE ban_date ban_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users DROP old_avatar, CHANGE ban_date ban_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE question CHANGE selected_answer_id selected_answer_id INT DEFAULT NULL');
    }
}
