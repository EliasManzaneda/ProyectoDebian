<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180619152951 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answer_user (answer_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D3B83589AA334807 (answer_id), INDEX IDX_D3B83589A76ED395 (user_id), PRIMARY KEY(answer_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer_user ADD CONSTRAINT FK_D3B83589AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answer_user ADD CONSTRAINT FK_D3B83589A76ED395 FOREIGN KEY (user_id) REFERENCES app_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question ADD selected_answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EF24C5BEC FOREIGN KEY (selected_answer_id) REFERENCES answer (id)');
        $this->addSql('CREATE INDEX IDX_B6F7494EF24C5BEC ON question (selected_answer_id)');
        $this->addSql('ALTER TABLE app_users CHANGE ban_date ban_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE answer_user');
        $this->addSql('ALTER TABLE app_users CHANGE ban_date ban_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EF24C5BEC');
        $this->addSql('DROP INDEX IDX_B6F7494EF24C5BEC ON question');
        $this->addSql('ALTER TABLE question DROP selected_answer_id');
    }
}
