<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180502092229 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE question_tag (question_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_339D56FB1E27F6BF (question_id), INDEX IDX_339D56FBBAD26311 (tag_id), PRIMARY KEY(question_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question_tag ADD CONSTRAINT FK_339D56FB1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE question_tag ADD CONSTRAINT FK_339D56FBBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE tag_question');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tag_question (tag_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_80C63295BAD26311 (tag_id), INDEX IDX_80C632951E27F6BF (question_id), PRIMARY KEY(tag_id, question_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_question ADD CONSTRAINT FK_80C632951E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_question ADD CONSTRAINT FK_80C63295BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE question_tag');
    }
}
