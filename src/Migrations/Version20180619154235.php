<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180619154235 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE answer_score (id INT AUTO_INCREMENT NOT NULL, scored_by_id INT NOT NULL, answer_id INT NOT NULL, score INT NOT NULL, INDEX IDX_A9A3DE0269617AEE (scored_by_id), INDEX IDX_A9A3DE02AA334807 (answer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question_score (id INT AUTO_INCREMENT NOT NULL, scored_by_id INT NOT NULL, question_id INT NOT NULL, score INT NOT NULL, INDEX IDX_272265569617AEE (scored_by_id), INDEX IDX_27226551E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer_score ADD CONSTRAINT FK_A9A3DE0269617AEE FOREIGN KEY (scored_by_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE answer_score ADD CONSTRAINT FK_A9A3DE02AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id)');
        $this->addSql('ALTER TABLE question_score ADD CONSTRAINT FK_272265569617AEE FOREIGN KEY (scored_by_id) REFERENCES app_users (id)');
        $this->addSql('ALTER TABLE question_score ADD CONSTRAINT FK_27226551E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question CHANGE selected_answer_id selected_answer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE app_users CHANGE ban_date ban_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE answer_score');
        $this->addSql('DROP TABLE question_score');
        $this->addSql('ALTER TABLE app_users CHANGE ban_date ban_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE question CHANGE selected_answer_id selected_answer_id INT DEFAULT NULL');
    }
}
